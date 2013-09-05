<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
// 製作日期：2008-07-05
// $Id: function.php,v 1.1 2008/05/14 01:22:08 tad Exp $
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include "header.php";
include "post_function.php";
$xoopsOption['template_main'] = "tb3_index_tpl.html";
/*-----------function區--------------*/



/*-----------執行動作判斷區----------*/
$_REQUEST['op']=(empty($_REQUEST['op']))?"":$_REQUEST['op'];
$tbsn = (!isset($_REQUEST['tbsn']))? "":intval($_REQUEST['tbsn']);
$tbdsn = (!isset($_REQUEST['tbdsn']))? "":intval($_REQUEST['tbdsn']);

switch($_REQUEST['op']){
	//更新資料
	case "update_tad_book3_docs";
	update_tad_book3_docs($tbdsn);
	header("location: page.php?tbdsn={$tbdsn}");
	break;

	//新增資料
	case "insert_tad_book3_docs":
	$tbdsn=insert_tad_book3_docs();
	header("location: page.php?tbdsn={$tbdsn}");
	break;

	//輸入表格
	case "tad_book3_docs_form";
	$main=tad_book3_docs_form($tbdsn,$tbsn);
	break;

	//刪除資料
	case "delete_tad_book3_docs";
	delete_tad_book3_docs($tbdsn);
	header("location: {$_SERVER['PHP_SELF']}");
	break;

	//預設動作
	default:
	$main=tad_book3_docs_form($tbdsn,$tbsn);
	break;
}

/*-----------秀出結果區--------------*/
include XOOPS_ROOT_PATH."/header.php";
$xoopsTpl->assign( "css" , "<link rel='stylesheet' type='text/css' media='screen' href='".XOOPS_URL."/modules/tad_book3/module.css' />") ;
$xoopsTpl->assign( "toolbar" , toolbar($interface_menu)) ;
$xoopsTpl->assign( "content" , $main) ;
include_once XOOPS_ROOT_PATH.'/footer.php';

?>
