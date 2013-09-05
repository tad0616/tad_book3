<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
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


//更新書籍計數器
function add_book_counter($tbsn=""){
	global $xoopsDB;
	$sql = "update ".$xoopsDB->prefix("tad_book3")." set  `counter` = `counter`+1 where tbsn='$tbsn'";
	$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
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
	 list_docs($tbsn);
  }else{
	 list_all_cate_book();
	}
	break;
}

/*-----------秀出結果區--------------*/
include_once XOOPS_ROOT_PATH.'/footer.php';

?>
