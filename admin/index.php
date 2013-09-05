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
//秀出所有分類及書籍
function list_all_cate_book(){
	global $xoopsDB;
	//$sql = "select * from ".$xoopsDB->prefix("tad_book3")." where tbcsn='$tbcsn' and enable='1' order by sort";
	$sql = "select a.`tbsn`, a.`tbcsn`, a.`sort`, a.`title`, a.`description`, a.`author`, a.`read_group`, a.`passwd`, a.`enable`, a.`pic_name`, a.`counter`, a.`create_date`
,b.`of_tbsn`, b.`sort` as cate_sort, b.`title` as cate_title , b.`description` from ".$xoopsDB->prefix("tad_book3")." as a left join ".$xoopsDB->prefix("tad_book3_cate")." as b on a.tbcsn=b.tbcsn  order by cate_sort,a.sort";


	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	while($data=$xoopsDB->fetchArray($result)){
	  foreach($data as $k=>$v){
			$$k=$v;
		}

		//if(!chk_power($read_group))continue;

		$pic=(empty($pic_name))?XOOPS_URL."/modules/tad_book3/images/blank.png":_TADBOOK3_BOOK_URL."/{$pic_name}";
		if(function_exists('strip_tags')){
			$description=strip_tags($description);
		}

    $tool="
    <div  style='width:auto;font-size:12px;font-weight:normal;'>
		<a href='{$_SERVER['PHP_SELF']}?op=tad_book3_form&tbsn=$tbsn'>"._BP_EDIT."</a> |
		<a href=\"javascript:delete_tad_book3_func($tbsn);\">"._BP_DEL."</a> |
		<a href='add.php?tbsn=$tbsn&op=tad_book3_docs_form'>"._MA_TADBOOK3_ADD_DOC."</a>
		</div>";

		if(empty($cate_title))$cate_title=_MI_TADBOOK3_NOT_CLASSIFIED;
		
		$data_arr[$cate_title][]=book_shadow($tbsn,$pic,$title,$description,"{$_SERVER['PHP_SELF']}?op=list_docs&tbsn=$tbsn",$tool);

	}

    $jquery=get_jquery(true);

	$main="
	<div id='save_msg'></div>
	$jquery

  <script language=\"JavaScript\">
  jQuery().ready(function(){
  	jQuery(function() {
  		jQuery(\"#books_sort\").sortable({ opacity: 0.6, cursor: 'move', update: function() {
  			var order = $(this).sortable(\"serialize\") + '&action=updateRecordsListings';
  			jQuery.post(\"save_book_sort.php\", order, function(theResponse){
				  jQuery(\"#save_msg\").html(theResponse);
  			});
  		}
  		});
    });
  });
  </script>

	<script>
	function delete_tad_book3_func(tbsn){
		var sure = window.confirm('"._BP_DEL_CHK."');
		if (!sure)	return;
		location.href=\"{$_SERVER['PHP_SELF']}?op=delete_tad_book3&tbsn=\" + tbsn;
	}
	</script>";
	foreach($data_arr as $cate_title=>$book_arr){
	  $main.="<h1 style='color:#A0A0A0;margin-top:20px;font-size:24px;'>{$cate_title}</h1>
		<div style='margin-left:20px;' id='books_sort'>";
	  foreach($book_arr as $book){
			$main.="{$book}";
		}
		$main.="</div><div style='clear:both;'></div>";
	}
	return $main;
}




//列出某書資料
function list_docs($tbsn=""){
	global $xoopsDB,$xoopsModule;

	$all_cate=all_cate();

	$MDIR=$xoopsModule->getVar('dirname');
	$sql = "select * from ".$xoopsDB->prefix("tad_book3")." where tbsn='$tbsn'";
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());


	$data="

	<script>
	function delete_tad_book3_docs_func(tbdsn){
		var sure = window.confirm('"._BP_DEL_CHK."');
		if (!sure)	return;
		location.href=\"{$_SERVER['PHP_SELF']}?op=delete_tad_book3_docs&tbdsn=\" + tbdsn;
	}
	</script>
	<table id='tbl'>
	<tbody>";
	list($tbsn,$tbcsn,$sort,$title,$description,$author,$read_group,$passwd,$enable,$pic_name,$counter,$create_date)=$xoopsDB->fetchRow($result);

		$enable_txt=($enable=='1')?_MI_TADBOOK3_ENABLE:_MI_TADBOOK3_UNABLE;

		$read_group=txt_to_group_name($read_group,_MA_TADBOOK3_ALL_OPEN);
		//共同編輯者
		$author_arr=explode(",",$author);
		foreach($author_arr as $uid){
			$uidname=XoopsUser::getUnameFromId($uid,1);
            $uidname=(empty($uidname))?XoopsUser::getUnameFromId($uid,0):$uidname;
			$uid_name[]=$uidname;
		}
		$author=implode("<br>",$uid_name);
		$uid_name="";
		
		$create_date=date("Y-m-d H:i:s",xoops_getUserTimestamp(strtotime($create_date)));
		
		$cate=(empty($all_cate[$tbcsn]))?_MI_TADBOOK3_NOT_CLASSIFIED:$all_cate[$tbcsn];
		
		$pic=(empty($pic_name))?XOOPS_URL."/modules/tad_book3/images/blank.png":_TADBOOK3_BOOK_URL."/{$pic_name}";
		$book=book_shadow($tbsn,$pic,"",$description,"{$_SERVER['PHP_SELF']}?op=list_docs&tbsn=$tbsn");

	$data.="<tr>
		<td rowspan=2>
			$book
		</td>
		<th class='title' style='text-align:left;font-size:16px;'  colspan=3>

			<div  style='width:auto;margin-left:10px;float:right;font-size:12px;'>
			<a href='{$_SERVER['PHP_SELF']}?op=tad_book3_form&tbsn=$tbsn'>"._BP_EDIT."</a> |
			<a href=\"javascript:delete_tad_book3_func($tbsn);\">"._BP_DEL."</a> |
			<a href='add.php?tbsn=$tbsn&op=tad_book3_docs_form'>"._MA_TADBOOK3_ADD_DOC."</a>
			</div>

		<span style='font-size:12px;'>[<b>{$cate}</b>]</span> <a href='{$_SERVER['PHP_SELF']}?op=list_docs&tbsn=$tbsn'>{$title}</a></th>
		</tr>

		<tr>
		<td style='vertical-align:top;' colspan=3>

			{$description}

			<div  style='width:auto;margin-left:10px;float:right;font-size:12px;'>
				<table style='width:auto;border:1px solid rgb(192,192,192);'>
				<tr>
					<th class='title' nowrap>"._MA_TADBOOK3_SORT."</th>
					<th class='title' nowrap>"._MA_TADBOOK3_READ_GROUP."</th>
					<th class='title' nowrap>"._MA_TADBOOK3_AUTHOR."</th>
					<th class='title' nowrap>"._MA_TADBOOK3_PASSWD."</th>
					<th class='title' nowrap>"._MA_TADBOOK3_ENABLE."</th>
					<th class='title' nowrap>"._MA_TADBOOK3_COUNTER."</th>
				</tr>

				<tr>
					<td nowrap align='center'>{$sort}</td>
					<td nowrap align='center'>{$read_group}</td>
					<td nowrap align='center'>{$author}</td>
					<td nowrap align='center'>{$passwd}</td>
					<td nowrap align='center'>{$enable_txt}</td>
				  <td align='center'>{$counter}</td>
				</tr>
				</table>
			 <div align='right'>"._MA_TADBOOK3_CREATE_DATE." {$create_date}</div>
			</div>
		</td>

		</tr>
	";

	$sql = "select * from ".$xoopsDB->prefix("tad_book3_docs")." where tbsn='{$tbsn}' order by category,page,paragraph,sort";
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	while(list($tbdsn,$tbsn,$category,$page,$paragraph,$sort,$title,$content,$add_date,$last_modify_date,$uid,$count,$enable)=$xoopsDB->fetchRow($result)){
	  $doc_sort=mk_category($category,$page,$paragraph,$sort);
	  $last_modify_date=date("Y-m-d H:i:s",xoops_getUserTimestamp($last_modify_date));
		$data.="<tr>
		<td colspan=2  class='list'><div style='float:right;'>($last_modify_date)</div>
		<span class='doc_sort_{$doc_sort['level']}'>{$doc_sort['main']} <a href='../page.php?tbdsn=$tbdsn'>{$title}</a></span></td>
		<td>"._MA_TADBOOK3_COUNT." {$count}</td>
		<td nowrap><a href='add.php?op=tad_book3_docs_form&tbdsn=$tbdsn'>"._BP_EDIT."</a> |
		<a href=\"javascript:delete_tad_book3_docs_func($tbdsn);\">"._BP_DEL."</a></td></tr>";
	}
  
	$data.="
	<tr><td style='width:140px;'></td><td></td><td></td><td></td></tr>
	</tbody>
	</table>";
	
	$data=div_3d("",$data,"corners");
	
	return $data;
}


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
	$main=tad_book3_form($tbsn);
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
	$main=list_docs($tbsn);
	break;


	//預設動作
	default:
	$main=list_all_cate_book();
	break;
}

/*-----------秀出結果區--------------*/
xoops_cp_header();
echo "<link rel='stylesheet' type='text/css' media='screen' href='../module.css' />";
admin_toolbar(0);
echo $main;
xoops_cp_footer();

?>
