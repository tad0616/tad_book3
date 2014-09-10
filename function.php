<?php
//引入TadTools的函式庫
if(!file_exists(XOOPS_ROOT_PATH."/modules/tadtools/tad_function.php")){
 redirect_header("http://www.tad0616.net/modules/tad_uploader/index.php?of_cat_sn=50",3, _TAD_NEED_TADTOOLS);
}
include_once XOOPS_ROOT_PATH."/modules/tadtools/tad_function.php";

define("_TADBOOK3_BOOK_DIR",XOOPS_ROOT_PATH."/uploads/tad_book3");
define("_TADBOOK3_BOOK_URL",XOOPS_URL."/uploads/tad_book3");

//秀出所有分類及書籍
function list_all_cate_book(){
  global $xoopsDB,$xoopsTpl,$xoopsUser;

  if($xoopsUser){
    $uid=$xoopsUser->uid();
  }else{
    $uid=0;
  }


  $sql = "select a.`tbsn`, a.`tbcsn`, a.`sort`, a.`title`, a.`description`, a.`author`, a.`read_group`, a.`passwd`, a.`enable`, a.`pic_name`, a.`counter`, a.`create_date`
,b.`of_tbsn`, b.`sort` as cate_sort, b.`title` as cate_title , b.`description` from ".$xoopsDB->prefix("tad_book3")." as a left join ".$xoopsDB->prefix("tad_book3_cate")." as b on a.tbcsn=b.tbcsn order by cate_sort,a.sort";


  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  while($data=$xoopsDB->fetchArray($result)){
    foreach($data as $k=>$v){
      $$k=$v;
    }
    $authors=explode(',',$author);
    if(!in_array($uid,$authors) and $enable!='1')continue;

    if(!in_array($uid,$authors) and !chk_power($read_group))continue;

    $pic=(empty($pic_name))?XOOPS_URL."/modules/tad_book3/images/blank.png":_TADBOOK3_BOOK_URL."/{$pic_name}";

    $description=strip_tags($description);



    $tool=in_array($uid,$authors)?"
    <div style='width:auto;font-size:12px;font-weight:normal;'>
    <a href='{$_SERVER['PHP_SELF']}?op=tad_book3_form&tbsn=$tbsn' class='btn btn-mini btn-warning'>"._TAD_EDIT."</a>
    <a href=\"javascript:delete_tad_book3_func($tbsn);\" class='btn btn-mini btn-danger'>"._TAD_DEL."</a>
    <a href='".XOOPS_URL."/modules/tad_book3/post.php?tbsn=$tbsn&op=tad_book3_docs_form' class='btn btn-mini btn-primary'>"._MD_TADBOOK3_ADD_DOC."</a>
    </div>":"";

    if(empty($cate_title))$cate_title=_MD_TADBOOK3_NOT_CLASSIFIED;

    $data_arr[$cate_title][]=book_shadow($tbsn,$pic,$title,$description,"{$_SERVER['PHP_SELF']}?op=list_docs&tbsn=$tbsn",$tool);

  }

  $i=0;
  $cate="";
  foreach($data_arr as $cate_title=>$book_arr){
    $cate[$i]['cate_title']=$cate_title;

    $j=0;
    $books="";
    foreach($book_arr as $book){
      $books[$j]['book']=$book;
      $j++;
    }
    $cate[$i]['books']=$books;
    $i++;
  }

  $xoopsTpl->assign('jquery',get_jquery(true));
  $xoopsTpl->assign('cate',$cate);
}


//列出某書資料
function list_docs($tbsn=""){
  global $xoopsDB,$xoopsUser,$xoopsModule,$xoopsTpl;

  if($xoopsUser){
    $uid=$xoopsUser->uid();
  }else{
    $uid=0;
  }

  $xoopsTpl->assign('now_op','list_docs');

  $all_cate=all_cate();

  $sql = "select * from ".$xoopsDB->prefix("tad_book3")." where tbsn='$tbsn'";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());


  list($tbsn,$tbcsn,$sort,$title,$description,$author,$read_group,$passwd,$enable,$pic_name,$counter,$create_date)=$xoopsDB->fetchRow($result);
  if(!chk_power($read_group)){
    header("location:index.php");
    exit;
  }
  $enable_txt=($enable=='1')?_MD_TADBOOK3_ENABLE:_MD_TADBOOK3_UNABLE;

  $read_group=txt_to_group_name($read_group,_MD_TADBOOK3_ALL_OPEN);

  //共同編輯者
  $author_arr=explode(",",$author);
  $my=in_array($uid,$author_arr);
  $xoopsTpl->assign('my',$my);
  foreach($author_arr as $uid){
    $uidname=XoopsUser::getUnameFromId($uid,1);
    $uidname=(empty($uidname))?XoopsUser::getUnameFromId($uid,0):$uidname;
    $uid_name[]=$uidname;
  }
  $author=implode(" , ",$uid_name);
  $uid_name="";

  $create_date=date("Y-m-d H:i:s",xoops_getUserTimestamp(strtotime($create_date)));

  $cate=(empty($all_cate[$tbcsn]))?_MD_TADBOOK3_NOT_CLASSIFIED:$all_cate[$tbcsn];

  $pic=(empty($pic_name))?XOOPS_URL."/modules/tad_book3/images/blank.png":_TADBOOK3_BOOK_URL."/{$pic_name}";

  $book=book_shadow($tbsn,$pic,"",$description,"{$_SERVER['PHP_SELF']}?op=list_docs&tbsn=$tbsn");


  $xoopsTpl->assign('book',$book);
  $xoopsTpl->assign('tbsn',$tbsn);
  $xoopsTpl->assign('cate',$cate);
  $xoopsTpl->assign('title',$title);
  $xoopsTpl->assign('description',$description);
  $xoopsTpl->assign('sort',$sort);
  $xoopsTpl->assign('read_group',$read_group);
  $xoopsTpl->assign('author',$author);
  $xoopsTpl->assign('passwd',$passwd);
  $xoopsTpl->assign('enable',$enable);
  $xoopsTpl->assign('enable_txt',$enable_txt);
  $xoopsTpl->assign('counter',$counter);
  $xoopsTpl->assign('create_date',$create_date);
  $xoopsTpl->assign('push_url',push_url());

  $i=0;
  $docs="";
  $sql = "select * from ".$xoopsDB->prefix("tad_book3_docs")." where tbsn='{$tbsn}' order by category,page,paragraph,sort";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  while(list($tbdsn,$tbsn,$category,$page,$paragraph,$sort,$title,$content,$add_date,$last_modify_date,$uid,$count,$enable)=$xoopsDB->fetchRow($result)){
    $doc_sort=mk_category($category,$page,$paragraph,$sort);
    $last_modify_date=date("Y-m-d H:i:s",xoops_getUserTimestamp($last_modify_date));

    if($enable!='1' and !$my)continue;
    $enable_txt=($enable=='1')?"":"["._MD_TADBOOK3_UNABLE."] ";

    $docs[$i]['tbdsn']=$tbdsn;
    $docs[$i]['last_modify_date']=$last_modify_date;
    $docs[$i]['doc_sort_level']=$doc_sort['level'];
    $docs[$i]['doc_sort_main']=$doc_sort['main'];
    $docs[$i]['title']=$title;
    $docs[$i]['count']=$count;
    $docs[$i]['enable']=$enable;
    $docs[$i]['enable_txt']=$enable_txt;
    $i++;
  }

  $xoopsTpl->assign('docs',$docs);
}



//tad_book3編輯表單
function tad_book3_form($tbsn=""){
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
    <div>user uid, ex:\"1;27;103\"</div>";
  }

  $group_arr=(empty($read_group))?array(""):explode(",",$read_group);
  $SelectGroup=new XoopsFormSelectGroup("", "read_group", false,$group_arr, 5, true);
  $SelectGroup->addOption("", _MD_TADBOOK3_ALL_OPEN, false);
  $group_menu=$SelectGroup->render();

  $op=(empty($tbsn))?"insert_tad_book3":"update_tad_book3";

  $xoopsTpl->assign('action',$_SERVER['PHP_SELF']);
  $xoopsTpl->assign('tbsn',$tbsn);
  $xoopsTpl->assign('cate_select',$cate_select);
  $xoopsTpl->assign('sort',$sort);
  $xoopsTpl->assign('title',$title);
  $xoopsTpl->assign('editor',$editor);
  $xoopsTpl->assign('user_menu',$user_menu);
  $xoopsTpl->assign('group_menu',$group_menu);
  $xoopsTpl->assign('enable',$enable);
  $xoopsTpl->assign('passwd',$passwd);
  $xoopsTpl->assign('op',$op);
  $xoopsTpl->assign('now_op','tad_book3_form');
}


//新增資料到tad_book3中
function insert_tad_book3(){
  global $xoopsDB;


  if(!empty($_POST['new_tbcsn'])){
    $tbcsn=add_tad_book3_cate();
  }else{
    $tbcsn=$_POST['tbcsn'];
  }


  if(!empty($_POST['author_str'])){
    $author=$_POST['author_str'];
  }else{
    $author=implode(",",$_POST['author']);
  }

  $myts =& MyTextSanitizer::getInstance();
  $_POST['title']=$myts->addSlashes($_POST['title']);
  $_POST['description']=$myts->addSlashes($_POST['description']);

  $read_group=(in_array("",$_POST['read_group']))?"":implode(",",$_POST['read_group']);
    $now=date("Y-m-d H:i:s" , xoops_getUserTimestamp(time()));
  $sql = "insert into ".$xoopsDB->prefix("tad_book3")." (`tbcsn`,`sort`,`title`,`description`,`author`,`read_group`,`passwd`,`enable`,`pic_name`,`counter`,`create_date`) values('{$tbcsn}','{$_POST['sort']}','{$_POST['title']}','{$_POST['description']}','{$author}','{$read_group}','{$_POST['passwd']}','{$_POST['enable']}','{$_POST['pic_name']}','{$_POST['counter']}','{$now}')";
  $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  //取得最後新增資料的流水編號
  $tbsn=$xoopsDB->getInsertId();

  if(!empty($_FILES['pic_name']['name'])){
    mk_thumb($tbsn,"pic_name",120);
  }


  return $tbsn;
}


//新增資料到tad_book3_cate中
function add_tad_book3_cate(){
  global $xoopsDB,$xoopsModuleConfig;
  if(empty($_POST['new_tbcsn']))return;
  $myts =& MyTextSanitizer::getInstance();
  $title=$myts->addSlashes($_POST['new_tbcsn']);
  $sort=get_max_sort();
  $sql = "insert into ".$xoopsDB->prefix("tad_book3_cate")." (`of_tbsn`,`sort`,`title`) values('0','{$sort}','{$title}')";
  $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  //取得最後新增資料的流水編號
  $tbcsn=$xoopsDB->getInsertId();
  return $tbcsn;
}


//自動取得新排序
function get_max_sort(){
  global $xoopsDB,$xoopsModule;
  $sql = "select max(sort) from ".$xoopsDB->prefix("tad_book3_cate")." where of_tbsn=''";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  list($sort)=$xoopsDB->fetchRow($result);
  return ++$sort;
}


//更新tad_book3某一筆資料
function update_tad_book3($tbsn=""){
  global $xoopsDB;

  if(!empty($_POST['new_tbcsn'])){
    $tbcsn=add_tad_book3_cate();
  }else{
    $tbcsn=$_POST['tbcsn'];
  }


  if(!empty($_POST['author_str'])){
    $author=$_POST['author_str'];
  }else{
    $author=implode(",",$_POST['author']);
  }

  $myts =& MyTextSanitizer::getInstance();
  $_POST['title']=$myts->addSlashes($_POST['title']);
  $_POST['description']=$myts->addSlashes($_POST['description']);

  $read_group=(in_array("",$_POST['read_group']))?"":implode(",",$_POST['read_group']);
  $sql = "update ".$xoopsDB->prefix("tad_book3")." set  `tbcsn` = '{$tbcsn}', `sort` = '{$_POST['sort']}', `title` = '{$_POST['title']}', `description` = '{$_POST['description']}', `author` = '{$author}', `read_group` = '{$read_group}', `passwd` = '{$_POST['passwd']}', `enable` = '{$_POST['enable']}' where tbsn='$tbsn'";
  $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());

  if(!empty($_FILES['pic_name']['name'])){
    mk_thumb($tbsn,"pic_name",120);
  }
  return $tbsn;
}


//自動取得新排序
function get_max_doc_sort($tbcsn=""){
  global $xoopsDB,$xoopsModule;
  $sql = "select max(sort) from ".$xoopsDB->prefix("tad_book3")." where tbcsn='{$tbcsn}'";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  list($sort)=$xoopsDB->fetchRow($result);
  return ++$sort;
}




//縮圖上傳
function mk_thumb($tbsn="",$col_name="",$width=100){
  global $xoopsDB;
  include_once XOOPS_ROOT_PATH."/modules/tadtools/upload/class.upload.php";

  if(file_exists(_TADBOOK3_BOOK_DIR."/book_{$tbsn}.png")){
    unlink(_TADBOOK3_BOOK_DIR."/book_{$tbsn}.png");
  }
  $handle = new upload($_FILES[$col_name]);
  if ($handle->uploaded) {
      $handle->file_new_name_body   = "book_{$tbsn}";
      $handle->image_convert = 'png';
      $handle->image_resize         = true;
      $handle->image_x              = $width;
      $handle->image_ratio_y        = true;
      $handle->file_overwrite       = true;
      $handle->process(_TADBOOK3_BOOK_DIR);
      $handle->auto_create_dir = true;
      if ($handle->processed) {
          $handle->clean();
          $sql = "update ".$xoopsDB->prefix("tad_book3")." set pic_name = 'book_{$tbsn}.png' where tbsn='$tbsn'";
          $xoopsDB->queryF($sql);
          return true;
      }else{
        die($handle->error);
      }
  }
  return false;
}


//book陰影
function book_shadow($tbsn="",$pic="",$title="",$description="",$link="",$tool=""){
  $url=(empty($link))?"":"<a href='$link'>";
  $url2=(empty($link))?"":"</a>";

  $myts =& MyTextSanitizer::getInstance();
  $description=$myts->htmlSpecialChars($description);
  $title=$myts->htmlSpecialChars($title);


  $book_title=(empty($title))?"":"<div style='text-align:center;'>{$url}{$title}{$url2}</div>";

  $data="
  <div style='width:145px;height:250px;float:left;padding:0px;border:0px;margin-right:10px;' id='tr_{$tbsn}'>

    <a href='{$link}'><img src='{$pic}' alt='{$description}' title='{$description}' class='img-polaroid'></a>
    {$tool}
    {$book_title}
  </div>
  ";

/*
  $data="
  <div style='width:145px;height:250px;float:left;padding:0px;border:0px;margin-right:10px;' id='tr_{$tbsn}'>
  $tool
  <div id='tb3_shadow'>
    <div>
      <a href='{$link}'><img src='{$pic}' alt='{$description}' title='{$description}'></a>
    </div>
  </div>
  {$book_title}
  </div>
  ";
  */
  return $data;
}



//檢查文章密碼
function check_passwd($tbsn=""){
  global $xoopsDB;
  $sql = "select passwd from ".$xoopsDB->prefix("tad_book3")." where tbsn='$tbsn'";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  list($passwd)=$xoopsDB->fetchRow($result);
  if($_POST['passwd']==$passwd){
    $_SESSION['passwd']=$passwd;
  }
  header("location:".XOOPS_URL."/modules/tad_book3/index.php?op=list_docs&tbsn=$tbsn");
  exit;
}


//以流水號取得某筆tad_book3資料
function get_tad_book3($tbsn=""){
  global $xoopsDB;
  if(empty($tbsn))return;
  $sql = "select * from ".$xoopsDB->prefix("tad_book3")." where tbsn='$tbsn'";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  $data=$xoopsDB->fetchArray($result);
  return $data;
}

//取得所有分類
function all_cate(){
  global $xoopsDB,$xoopsModule;
  $sql = "select tbcsn,title from ".$xoopsDB->prefix("tad_book3_cate")." order by sort";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  while(list($tbcsn,$title)=$xoopsDB->fetchRow($result)){
    $main[$tbcsn]=$title;
  }
  return $main;
}

//分類選單
function cate_select($cate_sn=""){
  $all_cate=all_cate();
  $main="";
  foreach($all_cate as $tbcsn=>$title){
    $selected=($cate_sn==$tbcsn)?"selected":"";
    $main.="<option value=$tbcsn $selected>$title</option>";
  }
  return $main;
}

//取得所有書名
function all_books(){
  global $xoopsDB,$xoopsModule;
  $sql = "select tbsn,title from ".$xoopsDB->prefix("tad_book3")." order by sort";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  while(list($tbsn,$title)=$xoopsDB->fetchRow($result)){
    $main[$tbsn]=$title;
  }
  return $main;
}

//書名選單
function book_select($book_sn=""){
  $all_books=all_books();
  foreach($all_books as $tbsn=>$title){
    $selected=($book_sn==$tbsn)?"selected":"";
    $main.="<option value=$tbsn $selected>$title</option>";
  }
  return $main;
}


//產生章節選單
function category_menu($num=""){
  $opt="";
  for($i=0;$i<=50;$i++){
    $selected=($num==$i)?"selected":"";
    $opt.="<option value='{$i}' $selected>$i</option>";
  }
  return $opt;
}

//取得前後文章
function near_docs($tbsn="",$doc_sn=""){
  global $xoopsDB,$isAdmin;
  $and_enable=$isAdmin?"":"and enable='1'";
  $sql = "select tbdsn,title,category,page,paragraph,sort from ".$xoopsDB->prefix("tad_book3_docs")." where tbsn='$tbsn' $and_enable order by category,page,paragraph,sort";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  $get_next=false;
  while(list($tbdsn,$title,$category,$page,$paragraph,$sort)=$xoopsDB->fetchRow($result)){
    $doc_sort=mk_category($category,$page,$paragraph,$sort);
    if($doc_sn==$tbdsn){
      $doc['main']="{$tbdsn};{$doc_sort['main']} {$title}";
      $get_next=true;
    }elseif($get_next){
      $doc['next']="{$tbdsn};{$doc_sort['main']} {$title}";
      return $doc;
      break;
    }else{
      $doc['prev']="{$tbdsn};{$doc_sort['main']} {$title}";
    }
  }
  return $doc;
}


//文章選單
function doc_select($tbsn="",$doc_sn=""){
  global $xoopsDB,$xoopsUser;

  if(empty($xoopsUser)){
    $andenable=" and `enable`='1'";
    $now_uid=0;
  }else{
    $andenable="";
    $now_uid=$xoopsUser->uid();
  }

  $main="";

  $sql = "select tbdsn,title,category,page,paragraph,sort,enable,uid from ".$xoopsDB->prefix("tad_book3_docs")." where tbsn='$tbsn' $andenable order by category,page,paragraph,sort";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  while(list($tbdsn,$title,$category,$page,$paragraph,$sort,$enable,$uid)=$xoopsDB->fetchRow($result)){
    $selected=($doc_sn==$tbdsn)?"selected":"";
    $doc_sort=mk_category($category,$page,$paragraph,$sort);

    $stat='';
    if($enable!='1'){
      if($now_uid!=$uid){
        continue;
      }else{
        $style=" style='color:gray;'";
        $stat="["._MD_TADBOOK3_UNABLE."] ";
      }
    }else{
      $style=" style='color:black;'";
    }
    $main.="<option value=$tbdsn $selected $style>".str_repeat("&nbsp;",($doc_sort['level']-1)*2)."{$doc_sort['main']} {$stat}{$title}</option>";
  }
  return $main;
}

//章節格式化
function mk_category($category="",$page="",$paragraph="",$sort=""){
  if(!empty($sort)){
    $main="{$category}-${page}-{$paragraph}-{$sort}";
    $level=4;
  }elseif(!empty($paragraph)){
    $main="{$category}-${page}-{$paragraph}";
    $level=3;
  }elseif(!empty($page)){
    $main="{$category}-${page}";
    $level=2;
  }elseif(!empty($category)){
    $main="{$category}.";
    $level=1;
  }else{
    $main="";
    $level=0;
  }
  $all['main']=$main;
  $all['level']=$level;
  return $all;
}



//判斷本文是否允許該用戶之所屬群組觀看
function chk_power($enable_group=""){
  global $xoopsDB,$xoopsUser;
  if(empty($enable_group))return true;

  //取得目前使用者的所屬群組
  if($xoopsUser){
    $User_Groups=$xoopsUser->getGroups();
  }else{
    $User_Groups=array();
  }

  $news_enable_group=explode(",",$enable_group);
  foreach($User_Groups as $gid){
    if(in_array($gid,$news_enable_group)){
      return true;
    }
  }
  return false;
}


//判斷本文是否允許該用戶編輯
function chk_edit_power($uid_txt=""){
  global $xoopsDB,$xoopsUser;
  if(empty($uid_txt))return false;

  //取得目前使用者的所屬群組
  if($xoopsUser){
    $user_id=$xoopsUser->getVar('uid');
  }else{
    $user_id=array();
  }

  $uid_arr=explode(",",$uid_txt);

  if(in_array($user_id,$uid_arr)){
    return true;
  }

  return false;
}


/********************* 預設函數 *********************/
//刪除tad_book3_docs某筆資料資料
function delete_tad_book3_docs($tbdsn=""){
  global $xoopsDB;
  $sql = "delete from ".$xoopsDB->prefix("tad_book3_docs")." where tbdsn='$tbdsn'";
  $xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
}

?>
