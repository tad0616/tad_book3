<?php
//  ------------------------------------------------------------------------ //
// 本模組由 tad 製作
// 製作日期：2008-07-05
// $Id: function.php,v 1.1 2008/05/14 01:22:08 tad Exp $
// ------------------------------------------------------------------------- //

//---基本設定---//
//模組名稱
$modversion['name'] = _MI_TADBOOK3_NAME;
//模組版次
$modversion['version']	= '2.1';
//模組作者
$modversion['author'] = _MI_TADBOOK3_AUTHOR;
//模組說明
$modversion['description'] = _MI_TADBOOK3_DESC;
//模組授權者
$modversion['credits']	= _MI_TADBOOK3_CREDITS;
//模組版權
$modversion['license']		= "GPL see LICENSE";
//模組是否為官方發佈1，非官方2
$modversion['official']		= 2;
//模組圖示
$modversion['image']		= "images/logo.png";
//模組目錄名稱
$modversion['dirname']		= "tad_book3";

//---資料表架構---//
$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
$modversion['tables'][1] = "tad_book3";
$modversion['tables'][2] = "tad_book3_cate";
$modversion['tables'][3] = "tad_book3_docs";


//---安裝設定---//
$modversion['onInstall'] = "include/onInstall.php";
$modversion['onUpdate'] = "include/onUpdate.php";
$modversion['onUninstall'] = "include/onUninstall.php";


//---管理介面設定---//
$modversion['hasAdmin'] = 1;
$modversion['adminindex'] = "admin/index.php";
$modversion['adminmenu'] = "admin/menu.php";

//---使用者主選單設定---//
$modversion['hasMain'] = 1;


//---啟動後台管理界面選單---//
$modversion['system_menu'] = 1;

//---樣板設定---//

$modversion['templates'][1]['file'] = 'tb3_index_tpl.html';
$modversion['templates'][1]['description'] = _MI_TADBOOK3_TEMPLATE_DESC1;
$modversion['templates'][2]['file'] = 'page.html';
$modversion['templates'][2]['description'] = _MI_TADBOOK3_TEMPLATE_DESC2;

//---評論設定---//
$modversion['hasComments'] = 1;
$modversion['comments']['pageName'] = 'page.php';
$modversion['comments']['itemName'] = 'tbdsn';

//---搜尋設定---//
$modversion['hasSearch'] = 1;
$modversion['search']['file'] = "include/search.php";
$modversion['search']['func'] = "tadbook3_search";


//---區塊設定---//
$modversion['blocks'][1]['file'] = "tad_book3_random.php";
$modversion['blocks'][1]['name'] = _MI_TADBOOK3_BNAME1;
$modversion['blocks'][1]['description'] = _MI_TADBOOK3_BDESC1;
$modversion['blocks'][1]['show_func'] = "tad_book3_random";
$modversion['blocks'][1]['template'] = "tad_book3_random.html";
$modversion['blocks'][1]['edit_func'] = "tad_book3_random_edit";
$modversion['blocks'][1]['options'] = "1|1";

$modversion['blocks'][2]['file'] = "tad_book3_new_doc.php";
$modversion['blocks'][2]['name'] = _MI_TADBOOK3_BNAME2;
$modversion['blocks'][2]['description'] = _MI_TADBOOK3_BDESC2;
$modversion['blocks'][2]['show_func'] = "tad_book3_new_doc";
$modversion['blocks'][2]['template'] = "tad_book3_new_doc.html";
$modversion['blocks'][2]['edit_func'] = "tad_book3_new_doc_edit";
$modversion['blocks'][2]['options'] = "5";

$modversion['blocks'][3]['file'] = "tad_book3_list.php";
$modversion['blocks'][3]['name'] = _MI_TADBOOK3_BNAME3;
$modversion['blocks'][3]['description'] = _MI_TADBOOK3_BDESC3;
$modversion['blocks'][3]['show_func'] = "tad_book3_list";
$modversion['blocks'][3]['template'] = "tad_book3_list.html";
$modversion['blocks'][3]['edit_func'] = "tad_book3_list_edit";
$modversion['blocks'][3]['options'] = "5|create_date|desc";

$modversion['blocks'][4]['file'] = "tad_book3_index.php";
$modversion['blocks'][4]['name'] = _MI_TADBOOK3_BNAME4;
$modversion['blocks'][4]['description'] = _MI_TADBOOK3_BDESC4;
$modversion['blocks'][4]['show_func'] = "tad_book3_index";
$modversion['blocks'][4]['template'] = "tad_book3_index.html";

$modversion['blocks'][5]['file'] = "tad_book3_qrcode.php";
$modversion['blocks'][5]['name'] = _MI_QRCODE_BLOCKNAME;
$modversion['blocks'][5]['description'] = _MI_QRCODE_BLOCKDESC;
$modversion['blocks'][5]['show_func'] = "tad_book3_qrcode_show";
$modversion['blocks'][5]['template'] = "tad_book3_qrcode.html";

$modversion['config'][6]['name'] = 'facebook_comments_width';
$modversion['config'][6]['title'] = '_MI_FBCOMMENT_TITLE';
$modversion['config'][6]['description'] = '_MI_FBCOMMENT_TITLE_DESC';
$modversion['config'][6]['formtype'] = 'yesno';
$modversion['config'][6]['valuetype'] = 'int';
$modversion['config'][6]['default'] = '1';

$modversion['config'][7]['name'] = 'use_pda';
$modversion['config'][7]['title'] = '_MI_USE_PDA_TITLE';
$modversion['config'][7]['description'] = '_MI_USE_PDA_TITLE_DESC';
$modversion['config'][7]['formtype'] = 'yesno';
$modversion['config'][7]['valuetype'] = 'int';
$modversion['config'][7]['default'] = '1';

$modversion['config'][8]['name'] = 'use_social_tools';
$modversion['config'][8]['title'] = '_MI_SOCIALTOOLS_TITLE';
$modversion['config'][8]['description'] = '_MI_SOCIALTOOLS_TITLE_DESC';
$modversion['config'][8]['formtype'] = 'yesno';
$modversion['config'][8]['valuetype'] = 'int';
$modversion['config'][8]['default'] = '1';
?>
