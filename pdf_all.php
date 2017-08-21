<?php
include_once "header.php";
//require_once "class/dompdf/dompdf_config.inc.php";
set_time_limit(0);
ini_set("memory_limit", "150M");
include_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$op   = system_CleanVars($_REQUEST, 'op', '', 'string');
$tbsn = system_CleanVars($_REQUEST, 'tbsn', 0, 'int');
$book = get_tad_book3($tbsn);

$pdffile = "tbsn_{$tbsn}.pdf";
$from    = urlencode(XOOPS_URL . "/modules/tad_book3/pdf_html_all.php?tbsn={$tbsn}");
$to      = urlencode($pdffile);
$url     = "http://120.115.2.78/pdf.php?from={$from}&to={$to}";
// die($url);

$handle = fopen($url, "rb");
fclose($handle);

check_pdf("http://120.115.2.78/uploads/{$pdffile}");

unlink(XOOPS_ROOT_PATH . "/uploads/{$filename}");

$ptitle = iconv('UTF-8', 'Big5', "{$book['title']}.pdf");

$pdf_content = file_get_contents("http://120.115.2.78/uploads/{$pdffile}");
header('Content-type: application/pdf');
header('Content-Disposition: attachment; filename="' . $ptitle . '"');
echo $pdf_content;

function check_pdf($pdf_file)
{
    if (!fileExists($pdf_file)) {
        sleep(1);
        check_pdf($pdf_file);
    } else {
        return;
    }
}
function fileExists($path)
{
    return (@fopen($path, "r") == true);
}
