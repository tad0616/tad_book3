<?php
use XoopsModules\Tadtools\Utility;

/*-----------引入檔案區--------------*/
require 'header.php';
require 'post_function.php';
$xoopsOption['template_main'] = 'tadbook3_post.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
/*-----------function區--------------*/

/*-----------執行動作判斷區----------*/
require_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$op = system_CleanVars($_REQUEST, 'op', '', 'string');
$tbsn = system_CleanVars($_REQUEST, 'tbsn', 0, 'int');
$tbdsn = system_CleanVars($_REQUEST, 'tbdsn', 0, 'int');

switch ($op) {
    //更新資料
    case 'update_tad_book3_docs':
        update_tad_book3_docs($tbdsn);
        header("location: page.php?tbdsn={$tbdsn}");
        exit;

    //新增資料
    case 'insert_tad_book3_docs':
        $tbdsn = insert_tad_book3_docs();
        header("location: page.php?tbdsn={$tbdsn}");
        exit;

    //輸入表格
    case 'tad_book3_docs_form':
        tad_book3_docs_form($tbdsn, $tbsn);
        break;
    //刪除資料
    case 'delete_tad_book3_docs':
        delete_tad_book3_docs($tbdsn);
        header("location: {$_SERVER['PHP_SELF']}");
        exit;

    //預設動作
    default:
        tad_book3_docs_form($tbdsn, $tbsn);
        break;
}

/*-----------秀出結果區--------------*/

$xoopsTpl->assign('toolbar', Utility::toolbar_bootstrap($interface_menu));
$xoopsTpl->assign('jquery', Utility::get_jquery(true));
$xoopsTpl->assign('isAdmin', $isAdmin);
require_once XOOPS_ROOT_PATH . '/footer.php';
