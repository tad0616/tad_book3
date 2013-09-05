<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
// 製作日期：2008-07-05
// $Id: post_function.php,v 1.1 2008/05/14 01:22:08 tad Exp $
// --------------

//tad_book3_docs編輯表單
function tad_book3_docs_form($tbdsn="",$tbsn=""){
	global $xoopsDB,$xoopsUser,$xoopsModule;
	include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");
	
	if ($xoopsUser) {
    $module_id = $xoopsModule->getVar('mid');
    $isAdmin=$xoopsUser->isAdmin($module_id);
  }else{
    $isAdmin=false;
	}
	if(!$isAdmin){
		$book=get_tad_book3($tbsn);
		if(!chk_edit_power($book['author'])){
			header("location:index.php");
		}
	}
	//抓取預設值
	if(!empty($tbdsn)){
		$DBV=get_tad_book3_docs($tbdsn);
	}else{
		$DBV=array();
	}

	//預設值設定

	$tbdsn=(!isset($DBV['tbdsn']))?"":$DBV['tbdsn'];
	$tbsn=(!isset($DBV['tbsn']))?$tbsn:$DBV['tbsn'];
	$category=(!isset($DBV['category']))?"":$DBV['category'];
	$page=(!isset($DBV['page']))?"":$DBV['page'];
	$paragraph=(!isset($DBV['paragraph']))?"":$DBV['paragraph'];
	$sort=(!isset($DBV['sort']))?"":$DBV['sort'];
	$title=(!isset($DBV['title']))?"":$DBV['title'];
	$content=(!isset($DBV['content']))?"":$DBV['content'];
	$add_date=(!isset($DBV['add_date']))?"":$DBV['add_date'];
	$last_modify_date=(!isset($DBV['last_modify_date']))?"":$DBV['last_modify_date'];
	$uid=(!isset($DBV['uid']))?"":$DBV['uid'];
	$count=(!isset($DBV['count']))?"":$DBV['count'];
	$enable=(!isset($DBV['enable']))?"1":$DBV['enable'];


  if(!file_exists(XOOPS_ROOT_PATH."/modules/tadtools/fck.php")){
      redirect_header("index.php",3, _MA_NEED_TADTOOLS);
  }

	include_once XOOPS_ROOT_PATH."/modules/tadtools/fck.php";
  $fck=new FCKEditor264("tad_book3","content",$content);
	$fck->setHeight(450);
	$editor=$fck->render();


	$op=(empty($tbdsn))?"insert_tad_book3_docs":"update_tad_book3_docs";
	//$op="replace_tad_book3_docs";
	$main="
	$syntaxhighlighter_code
  <form action='{$_SERVER['PHP_SELF']}' method='post' id='myForm' enctype='multipart/form-data'>
  <table class='form_tbl' style='width:100%'>

	<input type='hidden' name='tbdsn' value='{$tbdsn}'>
	<tr><td class='title'>"._MA_TADBOOK3_TITLE."</td>
	<td class='col'><select name='tbsn' class='span12'>".book_select($tbsn)."</select></td>
<td class='title'>"._MA_TADBOOK3_ENABLE."</td>
	<td class='col'>
	<input type='radio' name='enable' value='1' ".chk($enable,'1','1').">"._MI_TADBOOK3_ENABLE."
	<input type='radio' name='enable' value='0' ".chk($enable,'0').">"._MI_TADBOOK3_UNABLE."</td>
	</tr>
	<tr>
	<td class='title'>"._MA_TADBOOK3_DOC_TITLE."</td>
	<td class='col'><input type='text' name='title' size='40' value='{$title}' class='span12'></td>
	<td class='title'>"._MA_TADBOOK3_CATEGORY."</td>
	<td class='col'>
	<select name='category' size=1 class='span2'>".category_menu($category)."</select>-
	<select name='page' size=1 class='span2'>".category_menu($page)."</select>-
	<select name='paragraph' size=1 class='span2'>".category_menu($paragraph)."</select>-
	<select name='sort' size=1 class='span2'>".category_menu($sort)."</select></td>
	</tr>
	<tr>
	<td class='col' colspan=4>$editor</td></tr>
  <tr><td class='bar' colspan='4'>
  <input type='hidden' name='op' value='{$op}'>
  <input type='submit' value='"._MA_SAVE."'></td></tr>
  </table>
  </form>";

	$main=div_3d(_MA_INPUT_DOC_FORM,$main,"raised","width:100%");

	return $main;
}




//以流水號取得某筆tad_book3_docs資料
function get_tad_book3_docs($tbdsn=""){
	global $xoopsDB;
	if(empty($tbdsn))return;
	$sql = "select * from ".$xoopsDB->prefix("tad_book3_docs")." where tbdsn='$tbdsn'";
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	$data=$xoopsDB->fetchArray($result);
	return $data;
}


//新增資料到tad_book3_docs中
function insert_tad_book3_docs(){
	global $xoopsDB,$xoopsUser;
	$time=time();
	//$time=xoops_getUserTimestamp(time());

	$myts =& MyTextSanitizer::getInstance();
	$_POST['title']=$myts->addSlashes($_POST['title']);
	$_POST['content']=$myts->addSlashes($_POST['content']);
	
	
	$uid=$xoopsUser->getVar('uid');
	$sql = "insert into ".$xoopsDB->prefix("tad_book3_docs")." (`tbsn`,`category`,`page`,`paragraph`,`sort`,`title`,`content`,`add_date`,`last_modify_date`,`uid`,`count`,`enable`) values('{$_POST['tbsn']}','{$_POST['category']}','{$_POST['page']}','{$_POST['paragraph']}','{$_POST['sort']}','{$_POST['title']}','{$_POST['content']}','{$time}','{$time}','{$uid}','0','{$_POST['enable']}')";
	$xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	//取得最後新增資料的流水編號
	$tbdsn=$xoopsDB->getInsertId();
	return $tbdsn;
}

//更新tad_book3_docs某一筆資料
function update_tad_book3_docs($tbdsn=""){
	global $xoopsDB;
	$time=time();
	//$time=xoops_getUserTimestamp(time());
	$myts =& MyTextSanitizer::getInstance();
	$_POST['title']=$myts->addSlashes($_POST['title']);
	$_POST['content']=$myts->addSlashes($_POST['content']);
	
	$sql = "update ".$xoopsDB->prefix("tad_book3_docs")." set  `tbsn` = '{$_POST['tbsn']}', `category` = '{$_POST['category']}', `page` = '{$_POST['page']}', `paragraph` = '{$_POST['paragraph']}', `sort` = '{$_POST['sort']}', `title` = '{$_POST['title']}', `content` = '{$_POST['content']}', `last_modify_date` = '{$time}', `enable` = '{$_POST['enable']}' where tbdsn='$tbdsn'";
	$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	return $tbdsn;
}
?>
