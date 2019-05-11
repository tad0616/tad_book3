<?php
use League\HTMLToMarkdown\HtmlConverter;
use XoopsModules\Tadtools\SyntaxHighlighter;
use XoopsModules\Tadtools\Utility;

require_once __DIR__ . '/header.php';
$xoopsOption['template_main'] = 'tadbook3_markdown.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require __DIR__ . '/vendor/autoload.php';


/*-----------function區--------------*/

//觀看某一頁
function view_page($tbdsn = '')
{
    global $xoopsDB, $xoopsTpl;

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

    $doc_sort = mk_category($category, $page, $paragraph, $sort);

    //高亮度語法
    $SyntaxHighlighter = new SyntaxHighlighter();
    $syntaxhighlighter_code = $SyntaxHighlighter->render();

    $main = "
      <h1>{$book['title']}</h1>
      $content
    ";

    $doc_select = doc_select($tbsn, $tbdsn);
    $near_docs = near_docs($tbsn, $tbdsn);
    $prev = explode(';', $near_docs['prev']);
    $next = explode(';', $near_docs['next']);

    $p = (empty($prev[1])) ? '' : "<a href='markdown.php?tbdsn={$prev[0]}' style='text-decoration: none;'><img src='images/arrow_left.png' alt='prev' title='Prev' border='0' align='absmiddle' hspace=4>{$prev[1]}</a>";
    $n = (empty($next[1])) ? '' : "<a href='markdown.php?tbdsn={$next[0]}' style='text-decoration: none;'>{$next[1]}<img src='images/arrow_right.png' alt='next' title='next' border='0' align='absmiddle' hspace=4></a>";

    $doc_sort = mk_category($category, $page, $paragraph, $sort);
    $xoopsTpl->assign('p', $p);
    $xoopsTpl->assign('n', $n);
    $xoopsTpl->assign('doc_select', $doc_select);
    $xoopsTpl->assign('needpasswd', $needpasswd);

    return $main;
}
/*-----------執行動作判斷區----------*/
require_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$op = system_CleanVars($_REQUEST, 'op', '', 'string');
$tbsn = system_CleanVars($_REQUEST, 'tbsn', 0, 'int');
$tbdsn = system_CleanVars($_REQUEST, 'tbdsn', 0, 'int');

switch ($_REQUEST['op']) {
    //預設動作
    default:
        global $xoopsTpl;
        $converter = new HtmlConverter();
        $html = view_page($tbdsn);
        $markdown = $converter->convert($html);
        $xoopsTpl->assign('markdown', $markdown);
        break;
}

/*-----------秀出結果區--------------*/
$xoopsTpl->assign('toolbar', Utility::toolbar_bootstrap($interface_menu));
$xoopsTpl->assign('jquery', Utility::get_jquery(true));
$xoopsTpl->assign('isAdmin', $isAdmin);
require_once XOOPS_ROOT_PATH . '/footer.php';
