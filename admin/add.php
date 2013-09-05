<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
// 製作日期：2008-07-05
// $Id: function.php,v 1.1 2008/05/14 01:22:08 tad Exp $
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include "../../../include/cp_header.php";
include "../function.php";
include "../post_function.php";

/*-----------function區--------------*/



/*-----------執行動作判斷區----------*/
$op = (!isset($_REQUEST['op']))? "main":$_REQUEST['op'];
$tbsn = (!isset($_REQUEST['tbsn']))? "":intval($_REQUEST['tbsn']);
$tbdsn = (!isset($_REQUEST['tbdsn']))? "":intval($_REQUEST['tbdsn']);
switch($op){
	//更新資料
	case "update_tad_book3_docs";
	update_tad_book3_docs($tbdsn);
	header("location: index.php?op=list_docs&tbsn={$tbsn}");
	break;

	//新增資料
	case "insert_tad_book3_docs":
	insert_tad_book3_docs();
	header("location: index.php?op=list_docs&tbsn={$tbsn}");
	break;

	//輸入表格
	case "tad_book3_docs_form";
	$main=tad_book3_docs_form($tbdsn,$tbsn);
	break;


	//預設動作
	default:
	$main=tad_book3_docs_form($tbdsn,$tbsn);
	break;
}

/*-----------秀出結果區--------------*/
xoops_cp_header();
echo "<link rel='stylesheet' type='text/css' media='screen' href='../module.css' />";
admin_toolbar(2);
echo $main;
xoops_cp_footer();

?>
