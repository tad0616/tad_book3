<?php
use Xmf\Request;
use XoopsModules\Tadtools\Utility;

/*-----------引入檔案區--------------*/
require_once __DIR__ . '/header.php';
require __DIR__ . '/post_function.php';
$xoopsOption['template_main'] = 'tadbook3_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
/*-----------function區--------------*/

/*-----------執行動作判斷區----------*/
$op = Request::getString('op');
$tbsn = Request::getInt('tbsn');
$tbdsn = Request::getInt('tbdsn');

switch ($op) {
    //更新資料
    case 'update_tad_book3_docs':
        update_tad_book3_docs($tbdsn);
        header("location: page.php?tbsn={$tbsn}&tbdsn={$tbdsn}");
        exit;

    //新增資料
    case 'insert_tad_book3_docs':
        $tbdsn = insert_tad_book3_docs();
        header("location: page.php?tbsn={$tbsn}&tbdsn={$tbdsn}");
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
        $op = 'tad_book3_docs_form';
        break;
}

/*-----------秀出結果區--------------*/
$xoopsTpl->assign('toolbar', Utility::toolbar_bootstrap($interface_menu, false, $interface_icon));
$xoopsTpl->assign("now_op", $op);
$xoTheme->addStylesheet('modules/tad_book3/css/module.css');
require_once XOOPS_ROOT_PATH . '/footer.php';
