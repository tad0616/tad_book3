<?php

global $xoopsConfig;
$modversion = [];

//---模組基本資訊---//
$modversion['name'] = _MI_TADBOOK3_NAME;
$modversion['version'] = 4.0;
$modversion['description'] = _MI_TADBOOK3_DESC;
$modversion['author'] = _MI_TADBOOK3_AUTHOR;
$modversion['credits'] = 'geek01 , Michael Beck';
$modversion['help'] = 'page=help';
$modversion['license'] = 'GNU GPL 2.0';
$modversion['license_url'] = 'www.gnu.org/licenses/gpl-2.0.html/';
$modversion['image'] = "images/logo_{$xoopsConfig['language']}.png";
$modversion['dirname'] = basename(__DIR__);

//---模組狀態資訊---//
$modversion['release_date'] = '2021-08-22';
$modversion['module_website_url'] = 'https://tad0616.net/';
$modversion['module_website_name'] = _MI_TAD_WEB;
$modversion['module_status'] = 'release';
$modversion['author_website_url'] = 'https://tad0616.net/';
$modversion['author_website_name'] = _MI_TAD_WEB;
$modversion['min_php'] = 5.4;
$modversion['min_xoops'] = '2.5';

//---paypal資訊---//
$modversion['paypal'] = [];
$modversion['paypal']['business'] = 'tad0616@gmail.com';
$modversion['paypal']['item_name'] = 'Donation : ' . _MI_TAD_WEB;
$modversion['paypal']['amount'] = 0;
$modversion['paypal']['currency_code'] = 'USD';

//---資料表架構---//
$modversion['sqlfile']['mysql'] = 'sql/mysql.sql';
$modversion['tables'][1] = 'tad_book3';
$modversion['tables'][2] = 'tad_book3_cate';
$modversion['tables'][3] = 'tad_book3_docs';

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
$modversion['templates'] = [];
$i = 1;
$modversion['templates'][$i]['file'] = 'tadbook3_index.tpl';
$modversion['templates'][$i]['description'] = 'tadbook3_index.tpl';

$i++;
$modversion['templates'][$i]['file'] = 'tadbook3_admin.tpl';
$modversion['templates'][$i]['description'] = 'tadbook3_admin.tpl';

//---評論設定---//
$modversion['hasComments'] = 1;
$modversion['comments']['pageName'] = 'page.php';
$modversion['comments']['itemName'] = 'tbdsn';

//---搜尋設定---//
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = 'include/search.php';
$modversion['search']['func'] = 'tadbook3_search';

//---區塊設定---//
$modversion['blocks'][] = [
    'file' => 'tad_book3_random.php',
    'name' => _MI_TADBOOK3_BNAME1,
    'description' => _MI_TADBOOK3_BDESC1,
    'show_func' => 'tad_book3_random',
    'template' => 'tad_book3_block_random.tpl',
    'edit_func' => 'tad_book3_random_edit',
    'options' => '1|1',
];

$modversion['blocks'][] = [
    'file' => 'tad_book3_new_doc.php',
    'name' => _MI_TADBOOK3_BNAME2,
    'description' => _MI_TADBOOK3_BDESC2,
    'show_func' => 'tad_book3_new_doc',
    'template' => 'tad_book3_block_new_doc.tpl',
    'edit_func' => 'tad_book3_new_doc_edit',
    'options' => '5',
];

$modversion['blocks'][] = [
    'file' => 'tad_book3_list.php',
    'name' => _MI_TADBOOK3_BNAME3,
    'description' => _MI_TADBOOK3_BDESC3,
    'show_func' => 'tad_book3_list',
    'template' => 'tad_book3_block_list.tpl',
    'edit_func' => 'tad_book3_list_edit',
    'options' => '5|create_date|desc||0',
];

$modversion['blocks'][] = [
    'file' => 'tad_book3_index.php',
    'name' => _MI_TADBOOK3_BNAME4,
    'description' => _MI_TADBOOK3_BDESC4,
    'show_func' => 'tad_book3_index',
    'template' => 'tad_book3_block_index.tpl',
];

$modversion['blocks'][] = [
    'file' => 'tad_book3_content.php',
    'name' => _MI_TADBOOK3_BNAME5,
    'description' => _MI_TADBOOK3_BDESC5,
    'show_func' => 'tad_book3_content',
    'template' => 'tad_book3_block_content.tpl',
    'edit_func' => 'tad_book3_content_edit',
    'options' => '',
];

//---偏好設定---//
$modversion['config'][] = [
    'name' => 'facebook_comments_width',
    'title' => '_MI_FBCOMMENT_TITLE',
    'description' => '_MI_FBCOMMENT_TITLE_DESC',
    'formtype' => 'yesno',
    'valuetype' => 'int',
    'default' => '0',
];

$modversion['config'][] = [
    'name' => 'use_pda',
    'title' => '_MI_USE_PDA_TITLE',
    'description' => '_MI_USE_PDA_TITLE_DESC',
    'formtype' => 'yesno',
    'valuetype' => 'int',
    'default' => '0',
];

$modversion['config'][] = [
    'name' => 'use_social_tools',
    'title' => '_MI_SOCIALTOOLS_TITLE',
    'description' => '_MI_SOCIALTOOLS_TITLE_DESC',
    'formtype' => 'yesno',
    'valuetype' => 'int',
    'default' => '1',
];

$modversion['config'][] = [
    'name' => 'ffmpeg_path',
    'title' => '_MI_FFMPEG_PATH',
    'description' => '_MI_FFMPEG_PATH_DESC',
    'formtype' => 'textbox',
    'valuetype' => 'text',
    'default' => '',
];
