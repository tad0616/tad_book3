<?php

/*-----------引入檔案區--------------*/
if(file_exists("mainfile.php")){
  include_once "mainfile.php";
}elseif("../../mainfile.php"){
  include_once "../../mainfile.php";
}
include_once "function.php";
/*-----------function區--------------*/


function show_allbook(){
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

		
		$data_arr[$cate_title][]="
					<li class='gallery-item'>
						<a href='{$_SERVER['PHP_SELF']}?tbsn={$tbsn}'><img src='$pic'/>
						<h3>{$title}</h3>
						</a>
					</li>
		";

	}


	$main="";
	$main.="
		<style>
		h1, h2, h3 {
		    margin: 0 0 10px;
		}
		ul, ol {
		    margin: 0 0 10px;
		}
		.gallery{
			float: left;
			width:100%;
		}
		.gallery-entries{ 
			list-style:none;
			padding:0;
			/*float: left;*/
		}
		.gallery-entries .gallery-item{
			float: left;
			margin-right:1%;
			margin-bottom:2%;
		}
		.gallery-entries .gallery-item img{
			-webkit-box-shadow: #999 0 3px 5px;
			-moz-box-shadow: #999 0 3px 5px;
			box-shadow: #999 0 3px 5px;
			border: 1px solid #fff;
			max-width:100%;
		}
		.gallery-entries .gallery-item a{
			font-weight:normal;
			text-decoration:none;
		}
		.gallery-entries .gallery-item a h3{
			width:120px;
			white-space:nowrap;
			font-size:1em;
			overflow: hidden;
			text-overflow:ellipsis;
			padding-top:3px;
		}
		.gallery-entries .gallery-item  a:hover h3, .gallery-entries .gallery-item  a:active h3{
			text-decoration:underline;
		}
		.clearfix:after {
		    clear: both;
		    content: '';
		    display: block;
		    visibility: hidden;
		}
			@media only screen and (min-width: 960px) {
			.gallery{
				float: left;
				width:80%;
			}
		}
			@media only screen and (min-width: 768px) and (max-width: 959px) {
			.gallery{
				float: left;
				width:80%;
			}
			.gallery-entries .gallery-item{
				width:22%;
				margin-right:2%;
			}
			.gallery-entries .gallery-item a h3{
				width:100%;
			}
		}
			@media only screen and (min-width: 480px) and (max-width: 767px) {
			.gallery{
				float: none;
				width:100%;
			}
			.gallery-entries .gallery-item{
				width:30%;
				margin-right:2%;
			}
			.gallery-entries .gallery-item a h3{
				width:100%;
			}
		}
			@media only screen and (max-width: 479px) {
			.gallery{
				float: none;
				width:100%;
			}
			.gallery-entries .gallery-item{
				width:45%;
				margin-right:4%;
			}
			.gallery-entries .gallery-item a h3{
				font-size:1em;
				width:100%;
			}
		}
		</style>
	";
	foreach($data_arr as $cate_title=>$book_arr){
	  $main.="
			<section class='gallery'>
			<h2>{$cate_title}</h2>
				<ul class='gallery-entries clearfix'>
	  ";
	  foreach($book_arr as $book){
			$main.="{$book}";
		}
		$main.="
				</ul>
			</section>";
	}
  return $main;
}





//找出上一張或下一張
function get_pre_next($tbsn="",$now_sn=""){
  global $xoopsDB;
  $sql = "select tbdsn,title from ".$xoopsDB->prefix("tad_book3")." where tbsn='{$tbsn}' order by sort , post_date";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  $stop=false;
  $pre=0;
  while(list($tbdsn,$title)=$xoopsDB->fetchRow($result)){
    if($stop){
      $next=$tbdsn;
      $next_title=$title;
      break;
    }
    if($tbdsn==$now_sn){
      $now=$tbdsn;
      $stop=true;
    }else{
      $pre=$tbdsn;
      $pre_title=$title;
    }
  }
  $main['back']['tbdsn']=$pre;
  $main['back']['title']=$pre_title;
  $main['next']['tbdsn']=$next;
  $main['next']['title']=$next_title;
  
  return $main;
}




//列出所有tad_book3資料
function list_docs($tbsn=""){
	global $xoopsDB,$xoopsModule;
	add_book_counter($tbsn);

	$MDIR=$xoopsModule->getVar('dirname');
	$sql = "select * from ".$xoopsDB->prefix("tad_book3")." where tbsn='$tbsn'";
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());

	$function_title=($show_function)?"<th>"._TAD_FUNCTION."</th>":"";

	list($tbsn,$tbcsn,$sort,$title,$description,$author,$read_group,$passwd,$enable,$pic_name,$counter,$create_date)=$xoopsDB->fetchRow($result);

	if(!chk_power($read_group)){
		header("location:{$_SEREVR['PHP_SELF']}");
		exit;
	}

	$enable_txt=($enable=='1')?_MI_TADBOOK3_ENABLE:_MI_TADBOOK3_UNABLE;
	$pic=(empty($pic_name))?XOOPS_URL."/modules/tad_book3/images/blank.png":_TADBOOK3_BOOK_URL."/{$pic_name}";

	$create_date=date("Y-m-d H:i:s",xoops_getUserTimestamp(strtotime($create_date)));

	$book=book_shadow($tbsn,$pic,"",$description);


  //$home="<a href='{$_SERVER['PHP_SELF']}' class='nav'>⇧"._MD_TADBOOK3_HOMEPAGE."</a>";

	if(!empty($passwd) and $_SESSION['passwd']!=$passwd){
		$data.="
		<form action='{$_SERVER['PHP_SELF']}' method='post' id='myForm' enctype='multipart/form-data' data-ajax='false'>
		<input type='hidden' name='tbsn' value=$tbsn>
		<input type='hidden' name='op' value='check_passwd'>
		"._MI_TADBOOK3_INPUT_PASSWD."<input type='text' name='passwd'><button type='submit'>Submit</button>
		</form>
    ";
		return $data;
		exit;
	}

	$data.="
	<ul data-role='listview'>
	<li data-role='list-divider'>$title</li>
	";

	$sql = "select * from ".$xoopsDB->prefix("tad_book3_docs")." where tbsn='{$tbsn}' and enable='1' order by category,page,paragraph,sort";
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	while(list($tbdsn,$tbsn,$category,$page,$paragraph,$sort,$title,$content,$add_date,$last_modify_date,$uid,$count,$enable)=$xoopsDB->fetchRow($result)){
    $uid_name=XoopsUser::getUnameFromId($uid,1);
    $uid_name=(empty($uid_name))?XoopsUser::getUnameFromId($uid,0):$uid_name;

    $doc_sort=mk_category($category,$page,$paragraph,$sort);

    $last_modify_date=date("Y-m-d H:i:s",xoops_getUserTimestamp($last_modify_date));

    $size=56-$doc_sort['level']*10;
    $left=($doc_sort['level']-1)*56;

    $data.="
    <li><a href='{$_SERVER['PHP_SELF']}?tbdsn={$tbdsn}'>{$doc_sort['main']} {$title}<span class='ui-li-count'>$count</span></a></li>
	";
	}

	$data.="
	</ul>";
 return $data;
}


//更新書籍計數器
function add_book_counter($tbsn=""){
	global $xoopsDB;
	$sql = "update ".$xoopsDB->prefix("tad_book3")." set  `counter` = `counter`+1 where tbsn='$tbsn'";
	$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
}



//觀看某一頁
function view_page($tbdsn=""){
	global $xoopsDB;

	add_counter($tbdsn);

	$sql = "select * from ".$xoopsDB->prefix("tad_book3_docs")." where tbdsn='$tbdsn'";
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	list($tbdsn,$tbsn,$category,$page,$paragraph,$sort,$title,$content,$add_date,$last_modify_date,$uid,$count,$enable)=$xoopsDB->fetchRow($result);

	$book=get_tad_book3($tbsn);
	if(!chk_power($book['read_group'])){
		header("location:{$_SERVER['PHP_SELF']}");
		exit;
	}


	if(!empty($book['passwd']) and $_SESSION['passwd']!=$book['passwd']){
		$data.="
		<form action='{$_SERVER['PHP_SELF']}' method='post' id='myForm' enctype='multipart/form-data' data-ajax='false'>
		<input type='hidden' name='tbsn' value=$tbsn>
		<input type='hidden' name='op' value='check_passwd'>
		"._MI_TADBOOK3_INPUT_PASSWD."<input type='text' name='passwd' size=20><button type='submit'>Submit</button>
		</form>
		";
		return $data;
		exit;
	}


	$doc_select=doc_select($tbsn,$tbdsn);
	$near_docs=near_docs($tbsn,$tbdsn);
	$prev=explode(";",$near_docs['prev']);
	$next=explode(";",$near_docs['next']);

	$p_button=(empty($prev[1]))?"":"<a href='{$_SERVER['PHP_SELF']}?&tbdsn={$prev[0]}' class='nav' data-icon='arrow-u'>{$prev[1]}</a>";
	$n_button=(empty($next[1]))?"":"<a href='{$_SERVER['PHP_SELF']}?&tbdsn={$next[0]}' class='nav' data-icon='arrow-d'>{$next[1]}</a>";



	/*$bar="<tr><td style='width:33%;' background='images/relink_bg.gif'>{$p}</td>
	<td background='images/relink_bg.gif' style='text-align:center;'></td>
	<td background='images/relink_bg.gif' style='width:33%;text-align:right;'>{$n}</td></tr>";*/

	$doc_sort=mk_category($category,$page,$paragraph,$sort);


	//高亮度語法
  /*if(!file_exists(TADTOOLS_PATH."/syntaxhighlighter.php")){
   redirect_header("index.php",3, _MA_NEED_TADTOOLS);
  }
  include_once TADTOOLS_PATH."/syntaxhighlighter.php";
  $syntaxhighlighter= new syntaxhighlighter();
  $syntaxhighlighter_code=$syntaxhighlighter->render();*/

  //$home="<a href='{$_SERVER['PHP_SELF']}?tbsn=$tbsn' class='nav'>⇧{$book['title']}</a>";

  $nav="<div data-role='navbar' data-iconpos='left' style='margin-top:10px;margin-bottom:20px'>
  <ul>
	 <li>$p_button</li>
	 <li>$n_button</li>
   </ul>
  </div>";

  $book_title=$book['title'];

  $content=strip_tags($content,'<div><table><tr><td><br><a><p><img><ul><ol><li>');
  $content=preg_replace('/(<[^>]+) style=".*?"/i', '$1', $content);
  $content=preg_replace('/(<[^>]+) width=".*?"/i', '$1', $content);

	$main="
	<style>
	h1, h2, h3 {
	    margin: 0 0 10px;
	}
	#clean_news {
		border: 1px solid #CFCFCF;
		margin: 0px;
		background-image: none;
		padding:0px;
		border-radius: 5px;
	}
	#news_content {
		font-weight: normal;
		line-height: 1.5em;
		margin: 10px auto;
		overflow: hidden;
		width: 98%;
		word-wrap: break-word;
	}
	.ui-navbar li:last-child .ui-btn, .ui-navbar .ui-grid-duo .ui-block-b .ui-btn {
		border-right-width: 1px;
		margin-right: 0;
	}
	.ui-navbar li .ui-btn:last-child {
		border-right-width: 1px;
		margin-right: 0;
	}
	</style>
	<script type='text/javascript'>
	$('div[data-role=\"page\"]').live('pagebeforeshow',function(){
	$('#page_{$tbdsn} #news_content [href]').attr('rel','external');
	});
	</script>
	<h2>$book_title</h2>
  <select onChange=\"window.location.href='{$_SERVER['PHP_SELF']}?tbdsn='+this.value\">$doc_select</select>
  <div id='clean_news'>
    <div id='news_content'>
	$content
	</div>
  </div>
  $nav
  ";

	return $main;
}

//更新頁面計數器
function add_counter($tbdsn=""){
	global $xoopsDB;
	$sql = "update ".$xoopsDB->prefix("tad_book3_docs")." set  `count` = `count`+1 where tbdsn='$tbdsn'";
	$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
}

//檢查文章密碼
function check_passwd_m($tbsn=""){
	global $xoopsDB;
	$sql = "select passwd from ".$xoopsDB->prefix("tad_book3")." where tbsn='$tbsn'";
	$result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
	list($passwd)=$xoopsDB->fetchRow($result);
	if($_POST['passwd']==$passwd){
		$_SESSION['passwd']=$passwd;
	}
	header("location:".XOOPS_URL."/modules/tad_book3/pda.php?op=list_docs&tbsn=$tbsn");
	exit;
}

/*-----------執行動作判斷區----------*/
$_REQUEST['op']=(empty($_REQUEST['op']))?"":$_REQUEST['op'];

$tbdsn=(isset($_REQUEST['tbdsn']))?intval($_REQUEST['tbdsn']) : 0;
$tbsn=(isset($_REQUEST['tbsn']))?intval($_REQUEST['tbsn']) : 0;


$jquery=get_jquery();


switch($_REQUEST['op']){

	case "check_passwd":
	check_passwd_m($tbsn);
	break;

	default:
	if(!empty($tbdsn)){
		$main=view_page($tbdsn);
	}elseif(!empty($tbsn)){
		$main=list_docs($tbsn);
	}else{
		$main=show_allbook();
	}
	break;
}

//分類下拉選單
//$cate_option=get_tad_book3_cate_option(0,0,$tbsn);

$title=$xoopsModule->getVar('name');
/*-----------秀出結果區--------------*/
echo "
<!DOCTYPE HTML>
<html>
<head>
<meta charset='"._CHARSET."'>
<meta name='viewport' content='width=device-width, initial-scale=1'>
<meta name='apple-mobile-web-app-capable'content='yes'/>
<title>$title</title>
<link href='".XOOPS_URL."/modules/tadtools/jquery.mobile/jquery.mobile.css' rel='stylesheet' type='text/css'/>
<script src='".XOOPS_URL."/modules/tadtools/jquery/jquery.js' type='text/javascript'></script>
<script>
$(document).bind('mobileinit', function(){
$.mobile.defaultPageTransition = 'slide';
});
</script>
<style>
.ui-bar-b {
  opacity:0.9;
}
</style>
<script src='".XOOPS_URL."/modules/tadtools/jquery.mobile/jquery.mobile.js' type='text/javascript'></script>
</head>
<div data-role='page' id='page_{$tbdsn}' data-add-back-btn='true'>
  <div data-role='header' data-theme='b' data-position='fixed'>
	<h1>$title</h1>
	<a href='{$_SERVER['PHP_SELF']}' data-icon='home' data-iconpos='notext' class='ui-btn-right' rel='external'>Home</a>
  </div>
  <div data-role='content' id='content'>
	$main
  </div>
</div>
</body>
</html>
";

?>