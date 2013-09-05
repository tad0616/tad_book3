<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
$xoopsOption['template_main'] = "tadbook3_adm_main.html";
include_once "header.php";
include_once "../function.php";

/*-----------function區--------------*/





//刪除tad_book3某筆資料資料
function delete_tad_book3($tbsn=""){
	global $xoopsDB;
	$sql = "delete from ".$xoopsDB->prefix("tad_book3")." where tbsn='$tbsn'";
	$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
}

//刪除tad_book3_docs某筆資料資料
function delete_tad_book3_docs($tbdsn=""){
	global $xoopsDB;
	$sql = "delete from ".$xoopsDB->prefix("tad_book3_docs")." where tbdsn='$tbdsn'";
	$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
}

/*-----------執行動作判斷區----------*/
$op = (!isset($_REQUEST['op']))? "main":$_REQUEST['op'];
$tbsn = (!isset($_REQUEST['tbsn']))? "":intval($_REQUEST['tbsn']);
$tbdsn = (!isset($_REQUEST['tbdsn']))? "":intval($_REQUEST['tbdsn']);

switch($op){

	//新增資料
	case "insert_tad_book3":
	insert_tad_book3();
	header("location: {$_SERVER['PHP_SELF']}");
	break;

	//輸入表格
	case "tad_book3_form";
	tad_book3_form($tbsn);
	break;
	

	case "update_tad_book3";
	update_tad_book3($tbsn);
	header("location: {$_SERVER['PHP_SELF']}");
	break;

	//刪除書籍
	case "delete_tad_book3";
	delete_tad_book3($tbsn);
	header("location: {$_SERVER['PHP_SELF']}");
	break;

	//刪除文章
	case "delete_tad_book3_docs";
	delete_tad_book3_docs($tbdsn);
	header("location: {$_SERVER['PHP_SELF']}");
	break;

	//更新資料
	
	//列出書籍文章
	case "list_docs";
	list_docs($tbsn);
	break;


	//預設動作
	default:
	list_all_cate_book();
	break;
}

/*-----------秀出結果區--------------*/
include_once 'footer.php';
?>
