<?php
use Xmf\Request;
use XoopsModules\Tadtools\TadDataCenter;
use XoopsModules\Tadtools\TadUpFiles;

require_once __DIR__ . '/header.php';

$op = Request::getString('op');
$mod = Request::getString('mod');
$time = Request::getString('time');
$length = Request::getString('length');
$col_name = Request::getString('col_name');
$col_sn = Request::getInt('col_sn');

switch ($op) {
    case 'video_log':
        video_log($mod, $col_name, $col_sn, $time);
        exit;

    case 'video_length':
        video_length($mod, $col_name, $col_sn, $length);
        exit;

    default:
        # code...
        break;
}

function video_log($module_dirname, $col_name, $col_sn, $time)
{
    $TadDataCenter = new TadDataCenter($module_dirname);
    $TadDataCenter->set_col($col_name, $col_sn);
    $data_arr = [
        'currentTime' => [0 => $time],
    ];
    $TadDataCenter->saveCustomData($data_arr);
}

function video_length($module_dirname, $col_name, $col_sn, $length)
{
    $TadDataCenter = new TadDataCenter($module_dirname);
    $TadDataCenter->set_col($col_name, $col_sn);
    $data_arr = [
        'length' => [0 => $length],
    ];
    $TadDataCenter->saveCustomData($data_arr);
}
