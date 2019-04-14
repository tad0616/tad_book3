<?php
/*-----------引入檔案區--------------*/
require __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'tadbook3_page.tpl';
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

    $p = (empty($prev[1])) ? '' : "<a href='page.php?tbdsn={$prev[0]}' style='text-decoration: none;'><img src='images/arrow_left.png' alt='prev' title='Prev' border='0' align='absmiddle' hspace=4>{$prev[1]}</a>";
    $n = (empty($next[1])) ? '' : "<a href='page.php?tbdsn={$next[0]}' style='text-decoration: none;'>{$next[1]}<img src='images/arrow_right.png' alt='next' title='next' border='0' align='absmiddle' hspace=4></a>";

    $doc_sort = mk_category($category, $page, $paragraph, $sort);

    $facebook_comments = facebook_comments($xoopsModuleConfig['facebook_comments_width'], 'tad_book3', 'page.php', 'tbdsn', $tbdsn);

    //高亮度語法
    if (!file_exists(TADTOOLS_PATH . '/syntaxhighlighter.php')) {
        redirect_header('index.php', 3, _MD_NEED_TADTOOLS);
    }
    require_once TADTOOLS_PATH . '/syntaxhighlighter.php';
    $syntaxhighlighter = new syntaxhighlighter();
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
    $xoopsDB->queryF($sql) or web_error($sql, __FILE__, __LINE__);
}

/*-----------執行動作判斷區----------*/
require_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$op = system_CleanVars($_REQUEST, 'op', '', 'string');
$tbsn = system_CleanVars($_REQUEST, 'tbsn', 0, 'int');
$tbdsn = system_CleanVars($_REQUEST, 'tbdsn', 0, 'int');

switch ($op) {
    case 'check_passwd':
        check_passwd($tbsn);
        break;
    default:
        view_page($tbdsn);
        break;
}

/*-----------秀出結果區--------------*/

$xoopsTpl->assign('toolbar', toolbar_bootstrap($interface_menu));
$xoopsTpl->assign('bootstrap', get_bootstrap());
$xoopsTpl->assign('jquery', get_jquery(true));
$xoopsTpl->assign('isAdmin', $isAdmin);
require_once XOOPS_ROOT_PATH . '/footer.php';
