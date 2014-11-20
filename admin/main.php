<?php

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

function tad_book3_export($tbsn=""){
  global $xoopsDB;
  $rand=randStr();

  $tadbook3_dir=XOOPS_ROOT_PATH."/uploads/tad_book3";
  $import_dir="{$tadbook3_dir}/import_{$tbsn}";
  $from_file_dir="{$tadbook3_dir}/file";
  $from_image_dir="{$tadbook3_dir}/image";
  $import_file_dir="{$import_dir}/file/{$rand}";
  $import_image_dir="{$import_dir}/image/{$rand}";
  $bookfile = "{$import_dir}/1_book.sql";
  $docsfile = "{$import_dir}/2_docs.sql";
  rrmdir($import_dir);
  mk_dir($import_dir);
  mk_dir($import_dir."/file");
  mk_dir($import_dir."/image");
  mk_dir($import_file_dir);
  mk_dir($import_image_dir);

  //輸出書籍設定
  $sql = "select * from ".$xoopsDB->prefix("tad_book3")." where tbsn='$tbsn'";
  $result=$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  $book=$xoopsDB->fetchArray($result);
  copy($tadbook3_dir."/{$book['pic_name']}",$import_file_dir."/book.png");

  $cols=$vals="";
  foreach($book as $col=>$val){
    if($col=="tbsn")continue;
    if($col=="tbcsn"){
      $val='{{tbcsn}}';
    }elseif($col=="author"){
      $val='{{author}}';
    }elseif($col=="read_group"){
      $val='{{read_group}}';
    }elseif($col=="pic_name"){
      $val=$rand;
    }else{
      $val=mysql_real_escape_string($val);
    }
    $cols.="`{$col}`, ";
    $vals.="'{$val}', ";
  }
  $cols=substr($cols,0,-2);
  $vals=substr($vals,0,-2);
  $current="insert into `tad_book3` ({$cols}) values({$vals});\n";

  file_put_contents($bookfile, $current);

  //輸出文章設定
  $current="";
  $sql = "select * from ".$xoopsDB->prefix("tad_book3_docs")." where tbsn='$tbsn' order by category ,  page , paragraph , sort";
  $result=$xoopsDB->queryF($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  $all="";
  while($doc=$xoopsDB->fetchArray($result)){
    $cols=$vals="";
    foreach($doc as $col=>$val){
      if($col=="tbdsn")continue;

      if($col=="tbsn"){
        $val="{{tbsn}}";
      }else{
        if(strpos($val, '/uploads/tad_book3/image')!==false){
          preg_match_all('/src="([^"]+)/',$val, $match);
          foreach($match[1] as $image_url){
            $strpos=strpos($image_url, '/uploads/tad_book3/image');
            if($strpos!==false){
              $image="{{path}}".substr($image_url, $strpos);

              $val=str_replace($image_url,str_replace('tad_book3/image', "tad_book3/image/{$rand}", $image), $val);


              $form_image=XOOPS_ROOT_PATH.$image;
              $new_image=XOOPS_ROOT_PATH.str_replace('tad_book3/image', "tad_book3/import_{$tbsn}/image/{$rand}", $image);
              $image_dir=substr(dirname(str_replace($from_image_dir, '', $form_image)),1);
              $dirs=explode('/',$image_dir);
              if(is_array($dirs)){
                $new_import_image_dir=$import_image_dir;
                foreach($dirs as $d){
                  $new_import_image_dir=$new_import_image_dir.'/'.$d;
                  mk_dir($new_import_image_dir);
                }
              }

              if(file_exists($form_image)){
                if(copy($form_image, $new_image)){
                  $all.="<li>[{$image}] {$form_image}→{$new_image}</li>";
                }else{
                  $all.="<li style='color:red'>{$form_image}→{$new_image} 複製失敗！</li>";
                }
              }
            }
          }

        }

        if(strpos($val, '/uploads/tad_book3/file')!==false){
          preg_match_all('/href="([^"]+)/',$val, $match2);
          foreach($match2[1] as $file_url){
            $strpos=strpos($file_url, '/uploads/tad_book3/file');
            if($strpos!==false){
              $file="{{path}}".substr($file_url, $strpos);

              $val=str_replace($file_url,str_replace('tad_book3/file', "tad_book3/file/{$rand}", $file), $val);

              $form_file=XOOPS_ROOT_PATH.$file;
              $new_file=XOOPS_ROOT_PATH.str_replace('tad_book3/file', "tad_book3/import_{$tbsn}/file/{$rand}", $file);
              $file_dir=substr(dirname(str_replace($from_file_dir, '', $form_file)),1);
              $dirs=explode('/',$file_dir);
              if(is_array($dirs)){
                $new_import_file_dir=$import_file_dir;
                foreach($dirs as $d){
                  $new_import_file_dir=$new_import_file_dir.'/'.$d;
                  mk_dir($new_import_file_dir);
                }
              }

              if(file_exists($form_file)){
                if(copy($form_file, $new_file)){
                  $all.="<li>[{$file}] {$form_file}→{$new_file}</li>";
                }else{
                  $all.="<li style='color:red'>{$form_file}→{$new_file} 複製失敗！</li>";
                }
              }
            }
          }

        }

        $val=mysql_real_escape_string($val);
      }
      $cols.="`{$col}`, ";
      $vals.="'{$val}', ";
    }
    $cols=substr($cols,0,-2);
    $vals=substr($vals,0,-2);
    $current.="insert into `tad_book3_docs` ({$cols}) values({$vals});\n--tad_book3_import_doc--\n";
  }

  file_put_contents($docsfile, $current);

  $zip_name=XOOPS_ROOT_PATH."/uploads/tad_book3/import_{$tbsn}.zip";
  if(file_exists($zip_name)){
    unlink($zip_name);
  }


  $msg=shell_exec("zip -r -j {$zip_name} $import_dir");

  if(file_exists($zip_name)){
    header("location:".XOOPS_URL."/uploads/tad_book3/import_{$tbsn}.zip");
  }else{
    include_once('../class/pclzip.lib.php');
    $zipfile = new PclZip($zip_name);
    $v_list = $zipfile->create($import_dir,PCLZIP_OPT_REMOVE_PATH,XOOPS_ROOT_PATH."/uploads/tad_book3");

    if ($v_list == 0) {
      die("Error : ".$archive->errorInfo(true));
    }else{
      header("location:".XOOPS_URL."/uploads/tad_book3/import_{$tbsn}.zip");
    }
  }

  exit;
  die("<ol>$all</ol>");
  //http://120.115.2.90/uploads/tad_book3/file/school_news_20140815.zip
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

  //匯出書籍
  case "tad_book3_export":
  tad_book3_export($tbsn);
  break;


  //預設動作
  default:
  list_all_cate_book(true);
  break;
}

/*-----------秀出結果區--------------*/
include_once 'footer.php';
?>
