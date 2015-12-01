<?php
include_once "header.php";
$xoopsOption['template_main'] = set_bootstrap("tadbook3_markdown.html");
include_once XOOPS_ROOT_PATH . "/header.php";
require 'vendor/autoload.php';
use League\HTMLToMarkdown\HtmlConverter;
/*-----------function區--------------*/

//觀看某一頁
function view_page($tbdsn = "")
{
    global $xoopsDB, $xoopsTpl;

    $sql    = "select * from " . $xoopsDB->prefix("tad_book3_docs") . " where tbdsn='$tbdsn'";
    $result = $xoopsDB->query($sql) or web_error($sql);

    list($tbdsn, $tbsn, $category, $page, $paragraph, $sort, $title, $content, $add_date, $last_modify_date, $uid, $count, $enable) = $xoopsDB->fetchRow($result);

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

    $main = "
      <h1>{$book['title']}</h1>
      $content
    ";

    $doc_select = doc_select($tbsn, $tbdsn);
    $near_docs  = near_docs($tbsn, $tbdsn);
    $prev       = explode(";", $near_docs['prev']);
    $next       = explode(";", $near_docs['next']);

    $p = (empty($prev[1])) ? "" : "<a href='markdown.php?tbdsn={$prev[0]}' style='text-decoration: none;'><img src='images/arrow_left.png' alt='prev' title='Prev' border='0' align='absmiddle' hspace=4>{$prev[1]}</a>";
    $n = (empty($next[1])) ? "" : "<a href='markdown.php?tbdsn={$next[0]}' style='text-decoration: none;'>{$next[1]}<img src='images/arrow_right.png' alt='next' title='next' border='0' align='absmiddle' hspace=4></a>";

    $doc_sort = mk_category($category, $page, $paragraph, $sort);
    $xoopsTpl->assign('p', $p);
    $xoopsTpl->assign('n', $n);
    $xoopsTpl->assign('doc_select', $doc_select);
    return $main;
}
/*-----------執行動作判斷區----------*/
$_REQUEST['op'] = (empty($_REQUEST['op'])) ? "" : $_REQUEST['op'];
$tbsn           = (!isset($_REQUEST['tbsn'])) ? "" : intval($_REQUEST['tbsn']);
$tbdsn          = (!isset($_REQUEST['tbdsn'])) ? "" : intval($_REQUEST['tbdsn']);

$xoopsTpl->assign("toolbar", toolbar_bootstrap($interface_menu));
$xoopsTpl->assign("bootstrap", get_bootstrap());
$xoopsTpl->assign("jquery", get_jquery(true));
$xoopsTpl->assign("isAdmin", $isAdmin);

switch ($_REQUEST['op']) {

    //預設動作
    default:
        global $xoopsTpl;
        $converter = new HtmlConverter();
        $html      = view_page($tbdsn);
        $markdown  = $converter->convert($html);
        $xoopsTpl->assign('markdown', $markdown);
        break;
}

/*-----------秀出結果區--------------*/
include_once XOOPS_ROOT_PATH . '/footer.php';
