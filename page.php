<?php
/*-----------引入檔案區--------------*/
include "header.php";
$xoopsOption['template_main'] = set_bootstrap("tadbook3_page.html");
include_once XOOPS_ROOT_PATH . "/header.php";
/*-----------function區--------------*/

//觀看某一頁
function view_page($tbdsn = "")
{
    global $xoopsDB, $xoopsModuleConfig, $xoopsTpl;

    add_counter($tbdsn);

    $sql    = "select * from " . $xoopsDB->prefix("tad_book3_docs") . " where tbdsn='$tbdsn'";
    $result = $xoopsDB->query($sql) or web_error($sql);

    list($tbdsn, $tbsn, $category, $page, $paragraph, $sort, $title, $content, $add_date, $last_modify_date, $uid, $count, $enable) = $xoopsDB->fetchRow($result);

    $book = get_tad_book3($tbsn);
    if (!chk_power($book['read_group'])) {
        header("location:index.php");
        exit;
    }

    if (!empty($book['passwd']) and $_SESSION['passwd'] != $book['passwd']) {
        $data .= "
        <tr><td colspan=2 align='center'>
        <form action='{$_SERVER['PHP_SELF']}' method='post' id='myForm' enctype='multipart/form-data'>
        <input type='hidden' name='tbsn' value=$tbsn>
        <input type='hidden' name='op' value='check_passwd'>
        " . _MD_TADBOOK3_INPUT_PASSWD . "<input type='text' name='passwd' size=20><input type='submit'>
        </form>
        </td></tr></table>";
        return $data;
        exit;
    }

    $doc_select = doc_select($tbsn, $tbdsn);
    $near_docs  = near_docs($tbsn, $tbdsn);
    $prev       = explode(";", $near_docs['prev']);
    $next       = explode(";", $near_docs['next']);

    $p = (empty($prev[1])) ? "" : "<a href='page.php?tbdsn={$prev[0]}' style='text-decoration: none;'><img src='images/arrow_left.png' alt='prev' title='Prev' border='0' align='absmiddle' hspace=4>{$prev[1]}</a>";
    $n = (empty($next[1])) ? "" : "<a href='page.php?tbdsn={$next[0]}' style='text-decoration: none;'>{$next[1]}<img src='images/arrow_right.png' alt='next' title='next' border='0' align='absmiddle' hspace=4></a>";

    $doc_sort = mk_category($category, $page, $paragraph, $sort);

    $facebook_comments = facebook_comments($xoopsModuleConfig['facebook_comments_width'], 'tad_book3', 'page.php', 'tbdsn', $tbdsn);

    //高亮度語法
    if (!file_exists(TADTOOLS_PATH . "/syntaxhighlighter.php")) {
        redirect_header("index.php", 3, _MD_NEED_TADTOOLS);
    }
    include_once TADTOOLS_PATH . "/syntaxhighlighter.php";
    $syntaxhighlighter      = new syntaxhighlighter();
    $syntaxhighlighter_code = $syntaxhighlighter->render();

    $xoopsTpl->assign('syntaxhighlighter_code', $syntaxhighlighter_code);
    $xoopsTpl->assign('tbsn', $tbsn);
    $xoopsTpl->assign('book_title', $book['title']);
    $xoopsTpl->assign('doc_sort_main', $doc_sort['main']);
    $xoopsTpl->assign('title', $title);
    $xoopsTpl->assign('doc_sort_level', $doc_sort['level']);
    $xoopsTpl->assign('content', $content);
    $xoopsTpl->assign('p', $p);
    $xoopsTpl->assign('n', $n);
    $xoopsTpl->assign('doc_select', $doc_select);
    $xoopsTpl->assign('facebook_comments', $facebook_comments);
    $xoopsTpl->assign('push_url', push_url());
    $xoopsTpl->assign('tbdsn', $tbdsn);
}

//更新頁面計數器
function add_counter($tbdsn = "")
{
    global $xoopsDB;
    $sql = "update " . $xoopsDB->prefix("tad_book3_docs") . " set  `count` = `count`+1 where tbdsn='$tbdsn'";
    $xoopsDB->queryF($sql) or web_error($sql);
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

    case "check_passwd":
        check_passwd($tbsn);
        break;

    default:
        view_page($tbdsn);
        break;
}

/*-----------秀出結果區--------------*/
include_once XOOPS_ROOT_PATH . '/footer.php';
