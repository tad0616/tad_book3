<?php
use Xmf\Request;
use XoopsModules\Tadtools\SyntaxHighlighter;
use XoopsModules\Tadtools\Utility;

/*-----------引入檔案區--------------*/

require_once __DIR__ . '/header.php';
set_time_limit(0);
ini_set('memory_limit', '150M');

/*-----------執行動作判斷區----------*/
$op = Request::getString('op');
$tbsn = Request::getInt('tbsn');
$header = Request::getInt('header', 1);

$book = get_tad_book3($tbsn);

if ($xoopsUser) {
    $uid = $xoopsUser->uid();
} else {
    $uid = 0;
}
$author_arr = explode(',', $book['author']);
$my = in_array($uid, $author_arr);
//高亮度語法
$SyntaxHighlighter = new SyntaxHighlighter();
$syntaxhighlighter_code = $SyntaxHighlighter->render();
$bootstrap = Utility::get_bootstrap('return');

$html = '<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
  <meta charset="utf-8">
  <title>' . $book['title'] . '</title>
  ' . $bootstrap . '
  <link rel="stylesheet" type="text/css" href="' . XOOPS_URL . '/modules/tad_book3/css/reset.css" >
  <style type="text/css">
    body{
      font-size: 100%;
    }

    .page{
      font-size: 100%;
      line-height:2;
      padding: 2cm;
      background-image: url(' . XOOPS_URL . '/modules/tad_book3/images/paper_bg.jpg);
      background-repeat: repeat-x;
    }

    .page_content{
      font-size: 100%;
    }

    .page_title{
      border-bottom: 1px solid black;
      text-align:right;
      color:black;
      margin-bottom:20px;
    }
  </style>
  </head>
  <body>' . $syntaxhighlighter_code;

$i = 0;
$docs = '';
$sql = 'select tbdsn,enable from ' . $xoopsDB->prefix('tad_book3_docs') . " where tbsn='{$tbsn}' order by category,page,paragraph,sort";
$result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
while (false !== ($all = $xoopsDB->fetchArray($result))) {
    foreach ($all as $k => $v) {
        $$k = $v;
    }

    if ('1' != $enable and !$my) {
        continue;
    }
    $html .= view_page($tbdsn, $header);
    $html .= '<p style="page-break-after:always"></p>';
}
$html .= '
  </body>
</html>';
die($html);

//觀看某一頁
function view_page($tbdsn = '', $header = 1)
{
    global $xoopsDB, $book;

    $all = get_tad_book3_docs($tbdsn);
    foreach ($all as $key => $value) {
        $$key = $value;
    }

    if (!empty($from_tbdsn)) {
        $form_page = get_tad_book3_docs($from_tbdsn);
        $content .= $form_page['content'];
    }

    if (!chk_power($book['read_group'])) {
        header('location:index.php');
        exit;
    }

    if (!empty($book['passwd']) and $_SESSION['passwd'] != $book['passwd']) {
        $data .= _MD_TADBOOK3_INPUT_PASSWD;

        return $data;
        exit;
    }

    $doc_sort = mk_category($category, $page, $paragraph, $sort);
    $page_title = $header ? "<div class='page_title'>{$book['title']}</div>" : '';
    $main = "
    <div class='page'>
        $page_title
        <div class='page_content'>
            <h{$doc_sort['level']}>{$doc_sort['main']} {$title}</h{$doc_sort['level']}>
            $content
        </div>
    </div>
    ";

    return $main;
}
