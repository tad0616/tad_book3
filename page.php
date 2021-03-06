<?php
use Xmf\Request;
use XoopsModules\Tadtools\SyntaxHighlighter;
use XoopsModules\Tadtools\Utility;

/*-----------引入檔案區--------------*/
require __DIR__ . '/header.php';
$xoopsOption['template_main'] = 'tadbook3_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
/*-----------function區--------------*/

//觀看某一頁
function view_page($tbdsn = '')
{
    global $xoopsDB, $xoopsModuleConfig, $xoopsTpl;

    add_counter($tbdsn);
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
        header('location:index.php');
        exit;
    }

    $needpasswd = 0;
    if (!empty($book['passwd']) and $_SESSION['passwd'] != $book['passwd']) {
        $needpasswd = 1;
    }

    $doc_select = doc_select($tbsn, $tbdsn);
    $near_docs = near_docs($tbsn, $tbdsn);
    $prev = explode(';', $near_docs['prev']);
    $next = explode(';', $near_docs['next']);

    $p = (empty($prev[1])) ? '' : "<a href='page.php?tbsn={$tbsn}&tbdsn={$prev[0]}' style='text-decoration: none;'><img src='images/arrow_left.png' alt='prev' title='Prev' border='0' align='absmiddle' hspace=4>{$prev[1]}</a>";
    $n = (empty($next[1])) ? '' : "<a href='page.php?tbsn={$tbsn}&tbdsn={$next[0]}' style='text-decoration: none;'>{$next[1]}<img src='images/arrow_right.png' alt='next' title='next' border='0' align='absmiddle' hspace=4></a>";

    $doc_sort = mk_category($category, $page, $paragraph, $sort);

    $facebook_comments = Utility::facebook_comments($xoopsModuleConfig['facebook_comments_width'], 'tad_book3', 'page.php', 'tbdsn', $tbdsn);

    //高亮度語法
    $SyntaxHighlighter = new SyntaxHighlighter();
    $SyntaxHighlighter->render();

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
    $xoopsTpl->assign('push_url', Utility::push_url());
    $xoopsTpl->assign('tbdsn', $tbdsn);
    $xoopsTpl->assign('needpasswd', $needpasswd);
    $xoopsTpl->assign('use_social_tools', $xoopsModuleConfig['use_social_tools']);

    $xoopsTpl->assign('fb_title', $title);
    $xoopsTpl->assign('fb_description', mb_substr(strip_tags($content), 0, 150));
    $xoopsTpl->assign('xoops_pagetitle', $title);
}

//更新頁面計數器
function add_counter($tbdsn = '')
{
    global $xoopsDB;
    $sql = 'update ' . $xoopsDB->prefix('tad_book3_docs') . " set  `count` = `count`+1 where tbdsn='$tbdsn'";
    $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);
}

/*-----------執行動作判斷區----------*/
$op = Request::getString('op');
$tbsn = Request::getInt('tbsn');
$tbdsn = Request::getInt('tbdsn');

switch ($op) {
    case 'check_passwd':
        check_passwd($tbsn);
        break;

    default:
        view_page($tbdsn);
        $op = 'view_page';
        break;
}

/*-----------秀出結果區--------------*/

$xoopsTpl->assign('toolbar', Utility::toolbar_bootstrap($interface_menu));
$xoopsTpl->assign("now_op", $op);
$xoTheme->addStylesheet(XOOPS_URL . '/modules/tad_book3/css/reset.css');
$xoTheme->addStylesheet(XOOPS_URL . '/modules/tad_book3/css/module.css');
$xoTheme->addStylesheet(XOOPS_URL . '/modules/tadtools/css/kbdfun.css');
require_once XOOPS_ROOT_PATH . '/footer.php';
