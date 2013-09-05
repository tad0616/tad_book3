<?php
//區塊主函式 (QR Code)
function tad_book3_qrcode_show($options){
  if(preg_match("/tad_book3\/index.php\?tbsn=/i", $_SERVER['REQUEST_URI'])){
    $url=str_replace("index.php","pda.php",$_SERVER['REQUEST_URI']);
  }elseif(preg_match("/tad_book3\/$/i", $_SERVER['REQUEST_URI'])){
    $url=$_SERVER['REQUEST_URI']."pda.php";
  }elseif(preg_match("/tad_book3\/page.php\?tbdsn=/i", $_SERVER['REQUEST_URI'])){
    $url=str_replace("page.php","pda.php",$_SERVER['REQUEST_URI']);
  }else{
    return ;
  }

  //高亮度語法
  if(!file_exists(TADTOOLS_PATH."/qrcode.php")){
   redirect_header("index.php",3, _MA_NEED_TADTOOLS);
  }
  include_once TADTOOLS_PATH."/qrcode.php";
  $qrcode= new qrcode();
  $block=$qrcode->render($url);
	return $block;
}
?>
