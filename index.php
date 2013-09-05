<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
// 製作日期：2008-07-05
// $Id: function.php,v 1.1 2008/05/14 01:22:08 tad Exp $
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include "header.php";
$xoopsOption['template_main'] = "tadbook3_index.html";
include_once XOOPS_ROOT_PATH."/header.php";

/*-----------function區--------------*/

//列出所有tad_book3資料
function list_all_book($tbcsn="",$border=true){
	global $xoopsDB,$xoopsModule;

	$all_cate=all_cate();

	$MDIR=$xoopsModule->getVar('dirname');
	$sql = "select * from ".$xoopsDB->prefix("tad_book3")." where tbcsn='$tbcsn' and enable='1' order by sort";

	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());


	$data="";
	while(list($tbsn,$tbcsn,$sort,$title,$description,$author,$read_group,$passwd,$enable,$pic_name,$counter,$create_date)=$xoopsDB->fetchRow($result)){
		if(!chk_power($read_group))continue;

		$enable_txt=($enable=='1')?_MI_TADBOOK3_ENABLE:_MI_TADBOOK3_UNABLE;
		$pic=(empty($pic_name))?XOOPS_URL."/modules/tad_book3/images/blank.png":_TADBOOK3_BOOK_URL."/{$pic_name}";
		if(function_exists('strip_tags')){
			$description=strip_tags($description);
		}

		$data.=book_shadow($tbsn,$pic,$title,$description,"{$_SERVER['PHP_SELF']}?tbsn=$tbsn");

	}

	if(empty($data))return;

	$data.="<div style='clear:both;'></div>";

	if(!$border){
		return $data;
	}

	$data=div_3d("",$data,"corners","width:100%;");

	return $data;
}

//列出所有tad_book3資料
function list_docs($tbsn=""){
	global $xoopsDB,$xoopsModule;
	$all_cate=all_cate();
	add_book_counter($tbsn);

	$MDIR=$xoopsModule->getVar('dirname');
	$sql = "select * from ".$xoopsDB->prefix("tad_book3")." where tbsn='$tbsn'";
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());

	$function_title=($show_function)?"<th>"._TAD_FUNCTION."</th>":"";

	list($tbsn,$tbcsn,$sort,$title,$description,$author,$read_group,$passwd,$enable,$pic_name,$counter,$create_date)=$xoopsDB->fetchRow($result);

	if(!chk_power($read_group)){
		header("location:index.php");
		exit;
	}

	$enable_txt=($enable=='1')?_MI_TADBOOK3_ENABLE:_MI_TADBOOK3_UNABLE;
	$pic=(empty($pic_name))?XOOPS_URL."/modules/tad_book3/images/blank.png":_TADBOOK3_BOOK_URL."/{$pic_name}";
	
	$create_date=date("Y-m-d H:i:s",xoops_getUserTimestamp(strtotime($create_date)));
	
	$book=book_shadow($tbsn,$pic,"",$description);
	
	$data="
	<h1>$title</h1>
	<div>
    {$book}{$description}
  	<div>"._MA_TADBOOK3_CREATE_DATE." {$create_date}</div>
    {$fun}
  </div>
  ";
	//$data=div_3d($title,$book_desc,"corners","width:100%");
	

	$data.="
	<table align='center' id='tbl' style='width:98%;margin-left:auto;margin-right:auto;'>
	<tbody>";
	
	if(!empty($passwd) and $_SESSION['passwd']!=$passwd){
		$data.="
		<tr><td colspan=2 align='center'>
		<form action='{$_SERVER['PHP_SELF']}' method='post' id='myForm' enctype='multipart/form-data'>
		<input type='hidden' name='tbsn' value=$tbsn>
		<input type='hidden' name='op' value='check_passwd'>
		"._MI_TADBOOK3_INPUT_PASSWD."<input type='text' name='passwd' size=20><input type='submit'>
		</form>
		</td></tr></table>";
		return $data;
		exit;
	}
	

	$sql = "select * from ".$xoopsDB->prefix("tad_book3_docs")." where tbsn='{$tbsn}' and enable='1' order by category,page,paragraph,sort";
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	while(list($tbdsn,$tbsn,$category,$page,$paragraph,$sort,$title,$content,$add_date,$last_modify_date,$uid,$count,$enable)=$xoopsDB->fetchRow($result)){
    $uid_name=XoopsUser::getUnameFromId($uid,1);
    $uid_name=(empty($uid_name))?XoopsUser::getUnameFromId($uid,0):$uid_name;

    $doc_sort=mk_category($category,$page,$paragraph,$sort);
    
    $last_modify_date=date("Y-m-d H:i:s",xoops_getUserTimestamp($last_modify_date));
    
		$data.="<tr>
		<td colspan=3 class='list'>
		<div style='float:right;' class='date'>[ $last_modify_date by {$uid_name} ]</div>
		<span class='doc_sort_{$doc_sort['level']}'>{$doc_sort['main']}
		<a href='page.php?tbdsn=$tbdsn'>{$title}</a></span> ( $count )</td></tr>";
	}

	$data.="
	</tbody>
	</table>";
 return $data;
}

//更新書籍計數器
function add_book_counter($tbsn=""){
	global $xoopsDB;
	$sql = "update ".$xoopsDB->prefix("tad_book3")." set  `counter` = `counter`+1 where tbsn='$tbsn'";
	$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
}

//秀出所有分類及書籍
function list_all_cate_book(){
	global $xoopsDB;
	//$sql = "select * from ".$xoopsDB->prefix("tad_book3")." where tbcsn='$tbcsn' and enable='1' order by sort";
	$sql = "select a.`tbsn`, a.`tbcsn`, a.`sort`, a.`title`, a.`description`, a.`author`, a.`read_group`, a.`passwd`, a.`enable`, a.`pic_name`, a.`counter`, a.`create_date`
,b.`of_tbsn`, b.`sort` as cate_sort, b.`title` as cate_title , b.`description` from ".$xoopsDB->prefix("tad_book3")." as a left join ".$xoopsDB->prefix("tad_book3_cate")." as b on a.tbcsn=b.tbcsn where a.enable='1' order by cate_sort,a.sort";


	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	while($data=$xoopsDB->fetchArray($result)){
	  foreach($data as $k=>$v){
			$$k=$v;
		}
		
		if(!chk_power($read_group))continue;

		$pic=(empty($pic_name))?XOOPS_URL."/modules/tad_book3/images/blank.png":_TADBOOK3_BOOK_URL."/{$pic_name}";
		if(function_exists('strip_tags')){
			$description=strip_tags($description);
		}

		if(empty($cate_title))$cate_title=_MI_TADBOOK3_NOT_CLASSIFIED;

		$data_arr[$cate_title][]=book_shadow($tbsn,$pic,$title,$description,"{$_SERVER['PHP_SELF']}?tbsn=$tbsn");
	  
	}
	

	$main="";
	foreach($data_arr as $cate_title=>$book_arr){
	  $main.="<h1 style='color:#A0A0A0;margin-top:20px;font-size:24px;'>{$cate_title}</h1>
		<div style='margin-left:20px;'>";
	  foreach($book_arr as $book){
			$main.="{$book}";
		}
		$main.="</div><div style='clear:both;'></div>";
	}
	return $main;
}

/*-----------執行動作判斷區----------*/
$_REQUEST['op']=(empty($_REQUEST['op']))?"":$_REQUEST['op'];
$tbsn = (!isset($_REQUEST['tbsn']))? "":intval($_REQUEST['tbsn']);
$tbdsn = (!isset($_REQUEST['tbdsn']))? "":intval($_REQUEST['tbdsn']);

$xoopsTpl->assign( "toolbar" , toolbar_bootstrap($interface_menu)) ;
$xoopsTpl->assign( "bootstrap" , get_bootstrap()) ;
$xoopsTpl->assign( "jquery" , get_jquery(true)) ;
$xoopsTpl->assign( "isAdmin" , $isAdmin) ;

switch($_REQUEST['op']){

	case "check_passwd":
	check_passwd($tbsn);
	break;
	
	case "list_docs":
	$main=list_docs($tbsn);
	break;

	case "list_all_book":
	$main=list_all_book($tbcsn);
	break;



	//新增資料
	case "insert_tad_book3":
	insert_tad_book3();
	header("location: {$_SERVER['PHP_SELF']}");
	break;

	//輸入表格
	case "tad_book3_form";
	$main=tad_book3_form($tbsn);
	break;


	case "update_tad_book3";
	update_tad_book3($tbsn);
	header("location: {$_SERVER['PHP_SELF']}");
	break;

	default:
	if(!empty($tbsn)){
	 $main=list_docs($tbsn);
  }else{
	 $main=list_all_cate_book();
	}
	break;
}

/*-----------秀出結果區--------------*/
include_once XOOPS_ROOT_PATH.'/footer.php';

?>
