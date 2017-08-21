<?php
include_once "header.php";
//require_once "class/dompdf/dompdf_config.inc.php";
set_time_limit(0);
ini_set("memory_limit", "150M");
include_once $GLOBALS['xoops']->path('/modules/system/include/functions.php');
$op    = system_CleanVars($_REQUEST, 'op', '', 'string');
$tbdsn = system_CleanVars($_REQUEST, 'tbdsn', 0, 'int');

$artical = get_tad_book3_docs($tbdsn);
foreach ($artical as $key => $value) {
    $$key = $value;
}
$doc_sort = mk_category($category, $page, $paragraph, $sort);
$book     = get_tad_book3($tbsn);

$pdffile = "tbdsn_{$tbdsn}.pdf";
$from    = urlencode(XOOPS_URL . "/modules/tad_book3/pdf_html.php?tbdsn={$tbdsn}");
$to      = urlencode($pdffile);
$url     = "http://120.115.2.78/pdf.php?from={$from}&to={$to}";
// die($url);

$handle = fopen($url, "rb");
fclose($handle);

check_pdf("http://120.115.2.78/uploads/{$pdffile}");

unlink(XOOPS_ROOT_PATH . "/uploads/{$filename}");

$ptitle = iconv('UTF-8', 'Big5', "{$book['title']}-{$doc_sort['main']}-{$title}.pdf");

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
