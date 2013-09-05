<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
// 製作日期：2008-07-05
// $Id: function.php,v 1.1 2008/05/14 01:22:08 tad Exp $
// ------------------------------------------------------------------------- //

/*-----------引入檔案區--------------*/
include "header.php";
$xoopsOption['template_main'] = "page.html";
/*-----------function區--------------*/

//觀看某一頁
function view_page($tbdsn=""){
	global $xoopsDB,$xoopsModuleConfig;
	
	add_counter($tbdsn);
	
	$sql = "select * from ".$xoopsDB->prefix("tad_book3_docs")." where tbdsn='$tbdsn'";
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	list($tbdsn,$tbsn,$category,$page,$paragraph,$sort,$title,$content,$add_date,$last_modify_date,$uid,$count,$enable)=$xoopsDB->fetchRow($result);

	$book=get_tad_book3($tbsn);
	if(!chk_power($book['read_group'])){
		header("location:index.php");
		exit;
	}
	

	if(!empty($book['passwd']) and $_SESSION['passwd']!=$book['passwd']){
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
	
	
	$doc_select=doc_select($tbsn,$tbdsn);
	$near_docs=near_docs($tbsn,$tbdsn);
	$prev=explode(";",$near_docs['prev']);
	$next=explode(";",$near_docs['next']);
	
	$p=(empty($prev[1]))?"":"<a href='page.php?tbdsn={$prev[0]}' style='text-decoration: none;'><img src='images/arrow_left.png' alt='prev' title='Prev' border='0' align='absmiddle' hspace=4>{$prev[1]}</a>";
	$n=(empty($next[1]))?"":"<a href='page.php?tbdsn={$next[0]}' style='text-decoration: none;'>{$next[1]}<img src='images/arrow_right.png' alt='next' title='next' border='0' align='absmiddle' hspace=4></a>";
	
	$bar="<tr><td style='width:33%;' background='images/relink_bg.gif'>{$p}</td>
	<td background='images/relink_bg.gif' style='text-align:center;'><select onChange=\"window.location.href='page.php?tbdsn='+this.value\">$doc_select</select></td>
	<td background='images/relink_bg.gif' style='width:33%;text-align:right;'>{$n}</td></tr>";
	
	$doc_sort=mk_category($category,$page,$paragraph,$sort);
	
	$facebook_comments=facebook_comments($xoopsModuleConfig['facebook_comments_width'],'tad_book3','page.php','tbdsn',$tbdsn);
	
	//高亮度語法
  if(!file_exists(TADTOOLS_PATH."/syntaxhighlighter.php")){
   redirect_header("index.php",3, _MA_NEED_TADTOOLS);
  }
  include_once TADTOOLS_PATH."/syntaxhighlighter.php";
  $syntaxhighlighter= new syntaxhighlighter();
  $syntaxhighlighter_code=$syntaxhighlighter->render();


	$main="
	$syntaxhighlighter_code

	<table cellspacing='0' cellpadding='4' style='width:100%;'>
	$bar
	<tr><td colspan=3>
	<div class='page'>
	<div class='page_title'><a href='index.php?op=list_docs&tbsn=$tbsn'>{$book['title']}</a></div>
	<div class='page_content'>
	<h{$doc_sort['level']}>{$doc_sort['main']} $title</h{$doc_sort['level']}>
	$content
	</div>
	</div>
	</td></tr>
	$bar
	</table>
  $facebook_comments";

	return $main;
}

//更新頁面計數器
function add_counter($tbdsn=""){
	global $xoopsDB;
	$sql = "update ".$xoopsDB->prefix("tad_book3_docs")." set  `count` = `count`+1 where tbdsn='$tbdsn'";
	$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
}

/*-----------執行動作判斷區----------*/
$_REQUEST['op']=(empty($_REQUEST['op']))?"":$_REQUEST['op'];
$tbsn = (!isset($_REQUEST['tbsn']))? "":intval($_REQUEST['tbsn']);
$tbdsn = (!isset($_REQUEST['tbdsn']))? "":intval($_REQUEST['tbdsn']);

switch($_REQUEST['op']){

	case "check_passwd":
	check_passwd($tbsn);
	break;


	default:
	$main=view_page($tbdsn);
	break;
}

/*-----------秀出結果區--------------*/
include XOOPS_ROOT_PATH."/header.php";
$xoopsTpl->assign( "css" , "<link rel='stylesheet' type='text/css' media='screen' href='".XOOPS_URL."/modules/tad_book3/module.css' />\n<link rel='stylesheet' type='text/css' media='screen' href='".XOOPS_URL."/modules/tad_book3/reset.css' />") ;
$xoopsTpl->assign( "toolbar" , toolbar($interface_menu)) ;
$xoopsTpl->assign( "content" , $main) ;
$xoopsTpl->assign( "xoops_showrblock" , 0) ;
include_once XOOPS_ROOT_PATH.'/include/comment_view.php';

include_once XOOPS_ROOT_PATH.'/footer.php';

?>
