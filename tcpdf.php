<?php
include_once "header.php";
set_time_limit(0);
ini_set("memory_limit","150M");
$op=(empty($_REQUEST['op']))?"":$_REQUEST['op'];
$tbdsn=(empty($_REQUEST['tbdsn']))?"":intval($_REQUEST['tbdsn']);
$filename=(empty($_REQUEST['filename']))?"":$_REQUEST['filename'];

$html ='<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset='._CHARSET.'">
  <style type="text/css">
    #page{
      border:1px solid black;
      padding: 40px 60px 40px 60px;
      background-image: url(images/paper_bg.jpg);
      background-repeat: repeat-x;
      line-height:200%;
    }

    #page_title{
      border-bottom: 1px solid black;
      text-align:right;
      color:black;
      margin-bottom:20px;
    }
  </style>
  </head>
  <body>';
$html.=view_page($tbdsn);
$html.='
  </body>
</html>';

require_once('class/tcpdf/tcpdf.php');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->setPrintHeader(false); //不要頁首
$pdf->setPrintFooter(false); //不要頁尾
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);  //設定自動分頁
$pdf->setLanguageArray($l); //設定語言相關字串
$pdf->setFontSubsetting(true); //產生字型子集（有用到的字才放到文件中）
$pdf->SetFont('droidsansfallback', '', 12, '', true); //設定字型
$pdf->AddPage(); //新增頁面
//$pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));//文字陰影

$pdf->writeHTML($html);
$pdf->Output($filename, 'D');

//觀看某一頁
function view_page($tbdsn=""){
  global $xoopsDB;

  $sql = "select * from ".$xoopsDB->prefix("tad_book3_docs")." where tbdsn='$tbdsn'";
  $result = $xoopsDB->query($sql) or redirect_header($_SERVER['PHP_SELF'],3, mysql_error());
  list($tbdsn,$tbsn,$category,$page,$paragraph,$sort,$title,$content,$add_date,$last_modify_date,$uid,$count,$enable)=$xoopsDB->fetchRow($result);

  $book=get_tad_book3($tbsn);
  if(!chk_power($book['read_group'])){
    header("location:index.php");
    exit;
  }


  if(!empty($book['passwd']) and $_SESSION['passwd']!=$book['passwd']){
    $data.=_MI_TADBOOK3_INPUT_PASSWD;
    return $data;
    exit;
  }


  $doc_sort=mk_category($category,$page,$paragraph,$sort);


  //高亮度語法
  if(!file_exists(TADTOOLS_PATH."/syntaxhighlighter.php")){
   redirect_header("index.php",3, _MD_NEED_TADTOOLS);
  }
  include_once TADTOOLS_PATH."/syntaxhighlighter.php";
  $syntaxhighlighter= new syntaxhighlighter();
  $syntaxhighlighter_code=$syntaxhighlighter->render();


  $main="
  <div id='page'>
    <div id='page_title'>{$book['title']}</div>
    $content
  </div>
  ";

  return $main;
}
?>