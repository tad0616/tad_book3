<?php
global $xoopsConfig;
$modversion = [];

//---模組基本資訊---//
$modversion['name'] = _MI_TADBOOK3_NAME;
// $modversion['version'] = 4.06;
$modversion['version'] = $_SESSION['xoops_version'] >= 20511 ? '5.0.0-Stable' : '5.0';
$modversion['description'] = _MI_TADBOOK3_DESC;
$modversion['author'] = _MI_TADBOOK3_AUTHOR;
$modversion['credits'] = 'geek01 , Michael Beck';
$modversion['help'] = 'page=help';
$modversion['license'] = 'GNU GPL 2.0';
$modversion['license_url'] = 'www.gnu.org/licenses/gpl-2.0.html/';
$modversion['image'] = "images/logo_{$xoopsConfig['language']}.png";
$modversion['dirname'] = basename(__DIR__);

//---模組狀態資訊---//
$modversion['release_date'] = '2024-11-18';
$modversion['module_website_url'] = 'https://tad0616.net/';
$modversion['module_website_name'] = _MI_TAD_WEB;
$modversion['module_status'] = 'release';
$modversion['author_website_url'] = 'https://tad0616.net/';
$modversion['author_website_name'] = _MI_TAD_WEB;
$modversion['min_php'] = 5.4;
$modversion['min_xoops'] = '2.5.10';

//---paypal資訊---//
$modversion['paypal'] = [
    'business' => 'tad0616@gmail.com',
    'item_name' => 'Donation : ' . _MI_TAD_WEB,
    'amount' => 0,
    'currency_code' => 'USD',
];

//---資料表架構---//
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'] = [
    'tad_book3',
    'tad_book3_cate',
    'tad_book3_docs',
    'tad_book3_files_center',
    'tad_book3_data_center',
];

//---安裝設定---//
$modversion['onInstall'] = 'include/onInstall.php';
$modversion['onUpdate'] = 'include/onUpdate.php';
$modversion['onUninstall'] = 'include/onUninstall.php';

//---管理介面設定---//
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = 'admin/index.php';
$modversion['adminmenu'] = 'admin/menu.php';

//---使用者主選單設定---//
$modversion['hasMain'] = 1;

//---啟動後台管理界面選單---//
$modversion['system_menu'] = 1;

//---樣板設定---//
$modversion['templates'] = [
    ['file' => 'tadbook3_index.tpl', 'description' => 'tadbook3_index.tpl'],
    ['file' => 'tadbook3_admin.tpl', 'description' => 'tadbook3_admin.tpl'],
];

//---評論設定---//
$modversion['hasComments'] = 0;

//---搜尋設定---//
$modversion['hasSearch'] = 1;
$modversion['search'] = [
    'file' => 'include/search.php',
    'func' => 'tadbook3_search',
];

//---區塊設定 (索引為固定值，若欲刪除區塊記得補上索引，避免區塊重複)---//
$modversion['blocks'] = [
    1 => [
        'file' => 'tad_book3_random.php',
        'name' => _MI_TADBOOK3_BNAME1,
        'description' => _MI_TADBOOK3_BDESC1,
        'show_func' => 'tad_book3_random',
        'template' => 'tad_book3_block_random.tpl',
        'edit_func' => 'tad_book3_random_edit',
        'options' => '1|1',
    ],
    2 => [
        'file' => 'tad_book3_new_doc.php',
        'name' => _MI_TADBOOK3_BNAME2,
        'description' => _MI_TADBOOK3_BDESC2,
        'show_func' => 'tad_book3_new_doc',
        'template' => 'tad_book3_block_new_doc.tpl',
        'edit_func' => 'tad_book3_new_doc_edit',
        'options' => '5',
    ],
    3 => [
        'file' => 'tad_book3_list.php',
        'name' => _MI_TADBOOK3_BNAME3,
        'description' => _MI_TADBOOK3_BDESC3,
        'show_func' => 'tad_book3_list',
        'template' => 'tad_book3_block_list.tpl',
        'edit_func' => 'tad_book3_list_edit',
        'options' => '5|create_date|desc||0',
    ],
    4 => [
        'file' => 'tad_book3_index.php',
        'name' => _MI_TADBOOK3_BNAME4,
        'description' => _MI_TADBOOK3_BDESC4,
        'show_func' => 'tad_book3_index',
        'template' => 'tad_book3_block_index.tpl',
    ],
    5 => [
        'file' => 'tad_book3_content.php',
        'name' => _MI_TADBOOK3_BNAME5,
        'description' => _MI_TADBOOK3_BDESC5,
        'show_func' => 'tad_book3_content',
        'template' => 'tad_book3_block_content.tpl',
        'edit_func' => 'tad_book3_content_edit',
        'options' => '',
    ],
];

$modversion['config'] = [
    [
        'name' => 'use_social_tools',
        'title' => '_MI_SOCIALTOOLS_TITLE',
        'description' => '_MI_SOCIALTOOLS_TITLE_DESC',
        'formtype' => 'yesno',
        'valuetype' => 'int',
        'default' => '1',
    ],
    [
        'name' => 'ffmpeg_path',
        'title' => '_MI_FFMPEG_PATH',
        'description' => '_MI_FFMPEG_PATH_DESC',
        'formtype' => 'textbox',
        'valuetype' => 'text',
        'default' => '',
    ],
];
