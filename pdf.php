<?php
include_once "header.php";
//require_once "class/dompdf/dompdf_config.inc.php";
set_time_limit(0);
ini_set("memory_limit", "150M");
include_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$op    = system_CleanVars($_REQUEST, 'op', '', 'string');
$tbdsn = system_CleanVars($_REQUEST, 'tbdsn', 0, 'int');

$html = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=' . _CHARSET . '">
  <style type="text/css">
    #page{
      border:1px solid black;
      padding: 40px 60px 40px 60px;
      background-image: url(images/paper_bg.jpg);
      background-repeat: repeat-x;
      line-height:200%;
    }

    #page_title{
      border-bottom: 1px solid black;
      text-align:right;
      color:black;
      margin-bottom:20px;
    }
  </style>
  </head>
  <body>';
$html .= view_page($tbdsn);
$html .= '
  </body>
</html>';

echo $html;

//觀看某一頁
function view_page($tbdsn = "")
{
    global $xoopsDB;

    $all = get_tad_book3_docs($tbdsn);
    foreach ($all as $key => $value) {
        $$key = $value;
    }

    if (!empty($from_tbdsn)) {
        $form_page = get_tad_book3_docs($from_tbdsn);
        $content .= $form_page['content'];
    }

    $book = get_tad_book3($tbsn);
    if (!chk_power($book['read_group'])) {
        header("location:index.php");
        exit;
    }

    if (!empty($book['passwd']) and $_SESSION['passwd'] != $book['passwd']) {
        $data .= _MD_TADBOOK3_INPUT_PASSWD;
        return $data;
        exit;
    }

    $doc_sort = mk_category($category, $page, $paragraph, $sort);

    //高亮度語法
    if (!file_exists(TADTOOLS_PATH . "/syntaxhighlighter.php")) {
        redirect_header("index.php", 3, _MD_NEED_TADTOOLS);
    }
    include_once TADTOOLS_PATH . "/syntaxhighlighter.php";
    $syntaxhighlighter      = new syntaxhighlighter();
    $syntaxhighlighter_code = $syntaxhighlighter->render();

    $main = "$syntaxhighlighter_code
  <div class='page'>
    <div class='page_title'>{$book['title']}</div>
    $content
  </div>
  ";

    return $main;
}
