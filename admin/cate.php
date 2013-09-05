<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
// 製作日期：2008-07-05
// $Id: function.php,v 1.1 2008/05/14 01:22:08 tad Exp $
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include "../../../include/cp_header.php";
include "../function.php";

/*-----------function區--------------*/
//tad_book3_cate編輯表單
function tad_book3_cate_form($tbcsn=""){
	global $xoopsDB;
	include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");
	include_once(XOOPS_ROOT_PATH."/class/xoopseditor/xoopseditor.php");

	//抓取預設值
	if(!empty($tbcsn)){
		$DBV=get_tad_book3_cate($tbcsn);
	}else{
		$DBV=array();
	}

	//預設值設定

	$tbcsn=(!isset($DBV['tbcsn']))?"":$DBV['tbcsn'];
	$of_tbsn=(!isset($DBV['of_tbsn']))?"":$DBV['of_tbsn'];
	$sort=(!isset($DBV['sort']))?get_max_sort():$DBV['sort'];
	$title=(!isset($DBV['title']))?"":$DBV['title'];
	$description=(!isset($DBV['description']))?"":$DBV['description'];


	if(!file_exists(XOOPS_ROOT_PATH."/modules/tadtools/fck.php")){
    redirect_header("index.php",3, _MA_NEED_TADTOOLS);
  }
  include_once XOOPS_ROOT_PATH."/modules/tadtools/fck.php";
  $fck=new FCKEditor264("tad_book3","description",$description);
  $fck->setwidth(600);
  $fck->setHeight(150);
  $editor=$fck->render();

	$op=(empty($tbcsn))?"insert_tad_book3_cate":"update_tad_book3_cate";
	//$op="replace_tad_book3_cate";
	$main="
  <form action='{$_SERVER['PHP_SELF']}' method='post' id='myForm' enctype='multipart/form-data'>
  <table class='form_tbl'>

	<!--tr><td class='title'>"._MA_TADBOOK3_OF_TBSN."</td>
	<td class='col'><select name='of_tbsn' size=1>
		<option value='' ".chk($of_tbsn,'','1','selected')."></option>
	</select></td></tr-->
	<tr><td class='title'>"._MA_TADBOOK3_SORT."</td>
	<td class='col'><input type='text' name='sort' size='3' value='{$sort}'></td>
	<td class='title'>"._MA_TADBOOK3_CATE_TITLE."</td>
	<td class='col'><input type='text' name='title' size='40' value='{$title}' style='width:100%'></td></tr>
	<tr><td class='title'>"._MA_TADBOOK3_CATE_DESCRIPTION."</td>
	<td class='col' colspan=3>$editor</td></tr>
  <tr><td class='bar' colspan='4'>
	<input type='hidden' name='tbcsn' value='{$tbcsn}'>
  <input type='hidden' name='op' value='{$op}'>
  <input type='submit' value='"._MA_SAVE."'></td></tr>
  </table>
  </form>";

	$main=div_3d(_MA_INPUT_CATE_FORM,$main);

	return $main;
}


//新增資料到tad_book3_cate中
function insert_tad_book3_cate(){
	global $xoopsDB;
	$myts =& MyTextSanitizer::getInstance();
	$_POST['title']=$myts->addSlashes($_POST['title']);
	$_POST['description']=$myts->addSlashes($_POST['description']);
	$sql = "insert into ".$xoopsDB->prefix("tad_book3_cate")." (`of_tbsn`,`sort`,`title`,`description`) values('{$_POST['of_tbsn']}','{$_POST['sort']}','{$_POST['title']}','{$_POST['description']}')";
	$xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	//取得最後新增資料的流水編號
	$tbcsn=$xoopsDB->getInsertId();
	return $tbcsn;
}

//列出所有tad_book3_cate資料
function list_tad_book3_cate(){
	global $xoopsDB,$xoopsModule;
	
	$all_cate=all_cate();
	
	
	$MDIR=$xoopsModule->getVar('dirname');
	$sql = "select * from ".$xoopsDB->prefix("tad_book3_cate")."";
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  $data="";
	while(list($tbcsn,$of_tbsn,$sort,$title,$description)=$xoopsDB->fetchRow($result)){

		$data.="<tr>
		<td>{$sort}</td>
		<td>{$title}</td>
		<td>{$description}</td>
		<td><a href='{$_SERVER['PHP_SELF']}?op=tad_book3_cate_form&tbcsn=$tbcsn'>"._BP_EDIT."</a> |
		<a href=\"javascript:delete_tad_book3_cate_func($tbcsn);\">"._BP_DEL."</a></td></tr>";
	}
	
	if(empty($data)){
		header("location:{$_SERVER['PHP_SELF']}?op=tad_book3_cate_form");
		exit;
	}
	
	$main="
	<script>
	function delete_tad_book3_cate_func(tbcsn){
		var sure = window.confirm('"._BP_DEL_CHK."');
		if (!sure)	return;
		location.href=\"{$_SERVER['PHP_SELF']}?op=delete_tad_book3_cate&tbcsn=\" + tbcsn;
	}
	</script>
	<table id='tbl'>
	<tr>
	<th>"._MA_TADBOOK3_SORT."</th>
	<th>"._MA_TADBOOK3_CATE_TITLE."</th>
	<th>"._MA_TADBOOK3_CATE_DESCRIPTION."</th>
	<th>"._BP_FUNCTION."</th>
	</tr>
	<tbody>
	$data
	<tr>
	<td colspan=6 class='bar'>
	<a href='{$_SERVER['PHP_SELF']}?op=tad_book3_cate_form'><img src='".XOOPS_URL."/modules/{$MDIR}/images/add.gif' alt='"._BP_ADD."' align='right'></a></td></tr>
	</tbody>
	</table>";
	

	$main=div_3d("",$main,"corners");
	return $main;
}


//以流水號取得某筆tad_book3_cate資料
function get_tad_book3_cate($tbcsn=""){
	global $xoopsDB;
	if(empty($tbcsn))return;
	$sql = "select * from ".$xoopsDB->prefix("tad_book3_cate")." where tbcsn='$tbcsn'";
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	$data=$xoopsDB->fetchArray($result);
	return $data;
}

//更新tad_book3_cate某一筆資料
function update_tad_book3_cate($tbcsn=""){
	global $xoopsDB;
	$myts =& MyTextSanitizer::getInstance();
	$_POST['title']=$myts->addSlashes($_POST['title']);
	$_POST['description']=$myts->addSlashes($_POST['description']);
	
	$sql = "update ".$xoopsDB->prefix("tad_book3_cate")." set  `of_tbsn` = '{$_POST['of_tbsn']}', `sort` = '{$_POST['sort']}', `title` = '{$_POST['title']}', `description` = '{$_POST['description']}' where tbcsn='$tbcsn'";
	$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	return $tbcsn;
}

//刪除tad_book3_cate某筆資料資料
function delete_tad_book3_cate($tbcsn=""){
	global $xoopsDB;
	$sql = "delete from ".$xoopsDB->prefix("tad_book3_cate")." where tbcsn='$tbcsn'";
	$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
}


/*-----------執行動作判斷區----------*/
$op = (!isset($_REQUEST['op']))? "":$_REQUEST['op'];
$tbcsn = (!isset($_REQUEST['tbcsn']))? "":intval($_REQUEST['tbcsn']);

switch($op){
	//更新資料
	case "update_tad_book3_cate";
	update_tad_book3_cate($tbcsn);
	header("location: {$_SERVER['PHP_SELF']}");
	break;

	//新增資料
	case "insert_tad_book3_cate":
	insert_tad_book3_cate();
	header("location: {$_SERVER['PHP_SELF']}");
	break;
	
	//輸入表格
	case "tad_book3_cate_form";
	$main=tad_book3_cate_form($tbcsn);
	break;

	//刪除資料
	case "delete_tad_book3_cate";
	delete_tad_book3_cate($tbcsn);
	header("location: {$_SERVER['PHP_SELF']}");
	break;

	//預設動作
	default:
	$main=list_tad_book3_cate();
	break;



}

/*-----------秀出結果區--------------*/
xoops_cp_header();
echo "<link rel='stylesheet' type='text/css' media='screen' href='../module.css' />";
admin_toolbar(1);
echo $main;
xoops_cp_footer();

?>
