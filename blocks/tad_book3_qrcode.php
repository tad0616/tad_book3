<?php
//區塊主函式 (QR Code)
function tad_book3_qrcode_show($options)
{
    if (strpos($_SERVER['REQUEST_URI'], "tad_book3/index.php?op=list_docs&tbsn=") !== false) {
        $url = str_replace("index.php", "pda.php", $_SERVER['REQUEST_URI']);
    } elseif (strpos($_SERVER['REQUEST_URI'], "tad_book3/index.php") !== false) {
        $url = str_replace("index.php", "pda.php", $_SERVER['REQUEST_URI']);
    } elseif (strpos($_SERVER['REQUEST_URI'], "tad_book3/page.php?tbdsn=") !== false) {
        $url = str_replace("page.php", "pda.php", $_SERVER['REQUEST_URI']);
    } else {
        return;
    }

    //高亮度語法
    if (!file_exists(TADTOOLS_PATH . "/qrcode.php")) {
        redirect_header("index.php", 3, _MA_NEED_TADTOOLS);
    }
    include_once TADTOOLS_PATH . "/qrcode.php";
    $qrcode = new qrcode();
    $block  = $qrcode->render($url);
    return $block;
}
