<?php
use Xmf\Request;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_book3\Tools;

/*-----------引入檔案區--------------*/
require_once __DIR__ . '/header.php';
set_time_limit(0);
ini_set('memory_limit', '150M');

/*-----------執行動作判斷區----------*/
$op = Request::getString('op');
$tbsn = Request::getInt('tbsn');

$artical = get_tad_book3_docs($tbdsn);
foreach ($artical as $key => $value) {
    $$key = $value;
}
$doc_sort = Tools::mk_category($category, $page, $paragraph, $sort);
$book = get_tad_book3($tbsn);

//高亮度語法
$prism = Utility::prism('return');
$bootstrap = Utility::get_bootstrap('return');

$html = '<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
  <meta charset="utf-8">
  <title>' . $book['title'] . '-' . $doc_sort['main'] . '-' . $title . '</title>
  ' . $bootstrap . '
  <link rel="stylesheet" type="text/css" href="' . XOOPS_URL . '/modules/tad_book3/css/reset.css" >
  <style type="text/css">
    body{
      font-size: 100%;
    }

    .page{
      font-size: 100%;
      line-height:2;
      background-image: url(' . XOOPS_URL . '/modules/tad_book3/images/paper_bg.jpg);
      background-repeat: repeat-x;
    }

    .page_content{
      font-size: 100%;
    }

  </style>
  </head>
  <body>' . $prism;

$html .= view_page($tbdsn);
$html .= '
  </body>
</html>';
die($html);

//觀看某一頁
function view_page($tbdsn = '')
{
    global $book, $artical, $doc_sort;

    foreach ($artical as $key => $value) {
        $$key = $value;
    }

    if (!empty($from_tbdsn)) {
        $form_page = get_tad_book3_docs($from_tbdsn);
        $content .= $form_page['content'];
    }

    if (!Tools::chk_power($book['read_group'])) {
        redirect_header('index.php', 3, _MD_TADBOOK3_CANT_READ);
    }

    if (!empty($book['passwd']) and $_SESSION['passwd'] != $book['passwd']) {
        $data .= _MD_TADBOOK3_INPUT_PASSWD;

        return $data;
        exit;
    }

    $main = "
    <div class='page'>
      <div class='page_content'>
        <h{$doc_sort['level']}>{$doc_sort['main']} {$title}</h{$doc_sort['level']}>
        $content
      </div>
    </div>
    ";

    return $main;
}
