<?php
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

    $enable_txt=($enable=='1')?_MD_TADBOOK3_ENABLE:_MD_TADBOOK3_UNABLE;
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


  return $data;
}


//更新書籍計數器
function add_book_counter($tbsn=""){
  global $xoopsDB;
  $sql = "update ".$xoopsDB->prefix("tad_book3")." set  `counter` = `counter`+1 where tbsn='$tbsn'";
  $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
}


//更新狀態
function change_enable($enable,$tbdsn){
  global $xoopsDB;
  $sql = "update ".$xoopsDB->prefix("tad_book3_docs")." set  `enable` = '{$enable}' where tbdsn='$tbdsn'";
  $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
}



//tad_book3編輯表單
function import_form($tbsn=""){
  global $xoopsDB,$xoopsUser,$xoopsTpl;
  include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");

  //抓取預設值
  if(!empty($tbsn)){
    $DBV=get_tad_book3($tbsn);
  }else{
    $DBV=array();
  }

  //預設值設定

  $tbsn=(!isset($DBV['tbsn']))?"":$DBV['tbsn'];
  $tbcsn=(!isset($DBV['tbcsn']))?"":$DBV['tbcsn'];
  $sort=(!isset($DBV['sort']))?get_max_doc_sort($tbcsn):$DBV['sort'];
  $title=(!isset($DBV['title']))?"":$DBV['title'];
  $description=(!isset($DBV['description']))?"":$DBV['description'];
  $author=(!isset($DBV['author']))?"":$DBV['author'];
  $read_group=(!isset($DBV['read_group']))?"":$DBV['read_group'];
  $passwd=(!isset($DBV['passwd']))?"":$DBV['passwd'];
  $enable=(!isset($DBV['enable']))?"1":$DBV['enable'];
  $pic_name=(!isset($DBV['pic_name']))?"":$DBV['pic_name'];
  $counter=(!isset($DBV['counter']))?"":$DBV['counter'];
  $create_date=(!isset($DBV['create_date']))?"":$DBV['create_date'];



  if(!file_exists(XOOPS_ROOT_PATH."/modules/tadtools/fck.php")){
    redirect_header("index.php",3, _MD_NEED_TADTOOLS);
  }
  include_once XOOPS_ROOT_PATH."/modules/tadtools/fck.php";
  $fck=new FCKEditor264("tad_book3","description",$description);
  $fck->setwidth(600);
  $fck->setHeight(250);
  $editor=$fck->render();

  $author_arr=(empty($author))?array($xoopsUser->getVar("uid")):explode(",",$author);

  $cate_select=cate_select($tbcsn);

  $member_handler =& xoops_gethandler('member');
  $usercount = $member_handler->getUserCount(new Criteria('level', 0, '>'));

  if ($usercount < 1000) {

    $select = new XoopsFormSelect('', 'author',$author_arr, 5, true);
    $select->setExtra("class='span12'");
    $member_handler =& xoops_gethandler('member');
    $criteria = new CriteriaCompo();
    $criteria->setSort('uname');
    $criteria->setOrder('ASC');
    $criteria->setLimit(1000);
    $criteria->setStart(0);

    $select->addOptionArray($member_handler->getUserList($criteria));
    $user_menu=$select->render();
  }else{
    $user_menu="<textarea name='author_str' style='width:100%;'>$author</textarea>
    <div>user uid, ex:\"1,27,103\"</div>";
  }

  $group_arr=(empty($read_group))?array(""):explode(",",$read_group);
  $SelectGroup=new XoopsFormSelectGroup("", "read_group", false, $group_arr, 5, true);
  $SelectGroup->addOption("", _MD_TADBOOK3_ALL_OPEN, false);
  $SelectGroup->setExtra("class='span12'");
  $group_menu=$SelectGroup->render();


  $xoopsTpl->assign('action',$_SERVER['PHP_SELF']);
  $xoopsTpl->assign('tbsn',$tbsn);
  $xoopsTpl->assign('cate_select',$cate_select);
  $xoopsTpl->assign('user_menu',$user_menu);
  $xoopsTpl->assign('group_menu',$group_menu);
  $xoopsTpl->assign('now_op','import_form');
  $xoopsTpl->assign('upload_note',sprintf(_MD_TADBOOK3_UL_FILE,XOOPS_ROOT_PATH."/uploads/tad_book3/"));
  $xoopsTpl->assign('new_path',sprintf(_MD_TADBOOK3_ABS_PATH,XOOPS_URL));
  $XOOPS_URL=str_replace("//", "", XOOPS_URL);

  if(strpos("/", $XOOPS_URL)!==false){
    $xoopsTpl->assign('checked','');
  }else{
    $xoopsTpl->assign('checked','checked');
  }

}


//匯入書籍
function import_book($tbcsn){
  global $xoopsDB;
  if(!empty($_POST['new_tbcsn'])){
    $tbcsn=add_tad_book3_cate();
  }else{
    $tbcsn=$_POST['tbcsn'];
  }

  $tadbook3_dir=XOOPS_ROOT_PATH."/uploads/tad_book3";
  if(!empty($_POST['author_str'])){
    $author=$_POST['author_str'];
  }else{
    $author=implode(",",$_POST['author']);
  }
  $read_group=(in_array("",$_POST['read_group']))?"":implode(",",$_POST['read_group']);

  $book_sql=file_get_contents($_FILES['book']['tmp_name']);
  $book_sql=str_replace("`tad_book3`", "`".$xoopsDB->prefix("tad_book3")."`", $book_sql);
  $book_sql=str_replace("{{tbcsn}}", $tbcsn, $book_sql);
  $book_sql=str_replace("{{author}}", $author, $book_sql);
  $book_sql=str_replace("{{read_group}}", $read_group, $book_sql);
  $xoopsDB->queryF($book_sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  //取得最後新增資料的流水編號
  $tbsn=$xoopsDB->getInsertId();

  //取出亂數資料夾內容
  $sql = "select pic_name from ".$xoopsDB->prefix("tad_book3")." where tbsn='$tbsn'";
  $result=$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  list($rand)=$xoopsDB->fetchRow($result);

  //修改書籍封面圖
  $sql = "update ".$xoopsDB->prefix("tad_book3")." set pic_name = 'book_{$tbsn}.png' where tbsn='$tbsn'";
  $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());

  //產生書籍封面圖
  copy("{$tadbook3_dir}/file/{$rand}/book.png","{$tadbook3_dir}/book_{$tbsn}.png");

  $docs_sql=file_get_contents($_FILES['docs']['tmp_name']);
  $docs_sql=str_replace("`tad_book3_docs`", "`".$xoopsDB->prefix("tad_book3_docs")."`", $docs_sql);
  $docs_sql=str_replace("{{tbsn}}", $tbsn, $docs_sql);

  if($_POST['abs_path']=='1'){
    $docs_sql=str_replace("{{path}}", XOOPS_URL, $docs_sql);
  }else{
    $docs_sql=str_replace("{{path}}", '', $docs_sql);
  }

  $docs_sql_arr=explode("--tad_book3_import_doc--",$docs_sql);
  foreach($docs_sql_arr as $docs_sql){
    $sql=trim($docs_sql);
    if(!empty($sql)){
      $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
    }
  }

  return $tbsn;
}

/*-----------執行動作判斷區----------*/
$_REQUEST['op']=(empty($_REQUEST['op']))?"":$_REQUEST['op'];
$tbsn = (!isset($_REQUEST['tbsn']))? "":intval($_REQUEST['tbsn']);
$tbdsn = (!isset($_REQUEST['tbdsn']))? "":intval($_REQUEST['tbdsn']);
$enable = (!isset($_REQUEST['enable']))? "":intval($_REQUEST['enable']);
$tbcsn = (!isset($_REQUEST['tbcsn']))? "":intval($_REQUEST['tbcsn']);

$xoopsTpl->assign( "toolbar" , toolbar_bootstrap($interface_menu)) ;
$xoopsTpl->assign( "bootstrap" , get_bootstrap()) ;
$xoopsTpl->assign( "jquery" , get_jquery(true)) ;
$xoopsTpl->assign( "isAdmin" , $isAdmin) ;

switch($_REQUEST['op']){

  case "check_passwd":
  check_passwd($tbsn);
  break;

  case "list_docs":
  list_docs($tbsn);
  break;

  case "list_all_book":
  list_all_book($tbcsn);
  break;

  case "change_enable":
  change_enable($enable,$tbdsn);
  header("location: {$_SERVER['PHP_SELF']}?op=list_docs&tbsn=$tbsn");
  break;

  //新增資料
  case "insert_tad_book3":
  insert_tad_book3();
  header("location: {$_SERVER['PHP_SELF']}");
  break;

  //輸入表格
  case "tad_book3_form";
  tad_book3_form($tbsn);
  break;


  //匯入表格
  case "import_form";
  import_form($tbsn);
  break;

  case "import_book":
  $tbsn=import_book($tbcsn);
  header("location: index.php?op=list_docs&tbsn=$tbsn");
  break;


  case "update_tad_book3";
  update_tad_book3($tbsn);
  header("location: {$_SERVER['PHP_SELF']}");
  break;


  //刪除文章
  case "delete_tad_book3_docs";
  delete_tad_book3_docs($tbdsn);
  header("location: {$_SERVER['PHP_SELF']}");
  break;

  default:
  if(!empty($tbsn)){
   list_docs($tbsn);
  }else{
   list_all_cate_book($isAdmin);
  }
  break;
}

/*-----------秀出結果區--------------*/
include_once XOOPS_ROOT_PATH.'/footer.php';

?>
