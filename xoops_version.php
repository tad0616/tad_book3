<?php
$modversion = array();

//---模組基本資訊---//
$modversion['name'] = _MI_TADBOOK3_NAME;
$modversion['version'] = 3.31;
$modversion['description'] = _MI_TADBOOK3_DESC;
$modversion['author'] = _MI_TADBOOK3_AUTHOR;
$modversion['credits'] = "geek01";
$modversion['help'] = 'page=help';
$modversion['license'] = 'GNU GPL 2.0';
$modversion['license_url'] = 'www.gnu.org/licenses/gpl-2.0.html/';
$modversion['image'] = "images/logo_{$xoopsConfig['language']}.png";
$modversion['dirname'] = basename(dirname(__FILE__));

//---模組狀態資訊---//
$modversion['release_date'] = '2014/09/25';
$modversion['module_website_url'] = 'http://tad0616.net/';
$modversion['module_website_name'] = _MI_TAD_WEB;
$modversion['module_status'] = 'release';
$modversion['author_website_url'] = 'http://tad0616.net/';
$modversion['author_website_name'] = _MI_TAD_WEB;
$modversion['min_php']=5.2;
$modversion['min_xoops']='2.5';

//---paypal資訊---//
$modversion ['paypal'] = array();
$modversion ['paypal']['business'] = 'tad0616@gmail.com';
$modversion ['paypal']['item_name'] = 'Donation : ' . _MI_TAD_WEB;
$modversion ['paypal']['amount'] = 0;
$modversion ['paypal']['currency_code'] = 'USD';


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
$modversion['templates'] = array();
$i=1;
$modversion['templates'][$i]['file'] = 'tadbook3_index.html';
$modversion['templates'][$i]['description'] = 'tadbook3_index.html';

$i++;
$modversion['templates'][$i]['file'] = 'tadbook3_index_b3.html';
$modversion['templates'][$i]['description'] = 'tadbook3_index_b3.html';

$i++;
$modversion['templates'][$i]['file'] = 'tadbook3_page.html';
$modversion['templates'][$i]['description'] = 'tadbook3_page.html';

$i++;
$modversion['templates'][$i]['file'] = 'tadbook3_page_b3.html';
$modversion['templates'][$i]['description'] = 'tadbook3_page_b3.html';

$i++;
$modversion['templates'][$i]['file'] = 'tadbook3_post.html';
$modversion['templates'][$i]['description'] = 'tadbook3_post.html';

$i++;
$modversion['templates'][$i]['file'] = 'tadbook3_post_b3.html';
$modversion['templates'][$i]['description'] = 'tadbook3_post_b3.html';

$i++;
$modversion['templates'][$i]['file'] = 'tadbook3_adm_main.html';
$modversion['templates'][$i]['description'] = 'tadbook3_adm_main.html';

$i++;
$modversion['templates'][$i]['file'] = 'tadbook3_adm_main_b3.html';
$modversion['templates'][$i]['description'] = 'tadbook3_adm_main_b3.html';

$i++;
$modversion['templates'][$i]['file'] = 'tadbook3_adm_cate.html';
$modversion['templates'][$i]['description'] = 'tadbook3_adm_cate.html';

$i++;
$modversion['templates'][$i]['file'] = 'tadbook3_adm_cate_b3.html';
$modversion['templates'][$i]['description'] = 'tadbook3_adm_cate_b3.html';


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
