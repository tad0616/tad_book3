<?php
use Xmf\Request;
use XoopsModules\Tadtools\CkEditor;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tad_book3\Tools;

/*-----------引入檔案區--------------*/
require_once __DIR__ . '/header.php';
$xoopsOption['template_main'] = 'tadbook3_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

/*-----------執行動作判斷區----------*/
$op          = Request::getString('op');
$tbsn        = Request::getInt('tbsn');
$tbdsn       = Request::getInt('tbdsn');
$enable      = Request::getInt('enable');
$tbcsn       = Request::getInt('tbcsn');
$update_sort = Request::getArray('update_sort');

switch ($op) {
    case 'check_passwd':
        check_passwd($tbsn);
        break;

    case 'list_docs':
        list_docs($tbsn);
        break;

    case 'change_enable':
        change_enable($enable, $tbdsn);
        header("location: {$_SERVER['PHP_SELF']}?op=list_docs&tbsn=$tbsn");
        exit;

    //新增資料
    case 'insert_tad_book3':
        insert_tad_book3();
        header("location: {$_SERVER['PHP_SELF']}");
        exit;

    //輸入表格
    case 'tad_book3_form':
        tad_book3_form($tbsn, $tbcsn);
        break;

    //匯入表格
    case 'import_form':
        import_form($tbsn);
        break;

    case 'import_book':
        $tbsn = import_book($tbcsn);
        header("location: index.php?op=list_docs&tbsn=$tbsn");
        exit;

    case 'update_tad_book3':
        update_tad_book3($tbsn);
        header("location: {$_SERVER['PHP_SELF']}");
        exit;

    //刪除文章
    case 'delete_tad_book3_docs':
        delete_tad_book3_docs($tbdsn);
        header("location: {$_SERVER['PHP_SELF']}?tbsn={$tbsn}");
        exit;

    //匯出書籍
    case 'tad_book3_export':
        tad_book3_export($tbsn);
        break;

    case 'update_docs_sort':
        update_docs_sort($update_sort);
        header("location: {$_SERVER['PHP_SELF']}?tbsn={$tbsn}");
        exit;

    default:
        if (!empty($tbsn)) {
            list_docs($tbsn);
            $op = 'list_docs';
        } else {
            list_all_cate_book();
            $op = 'list_all_cate_book';
        }
        break;
}

/*-----------秀出結果區--------------*/
$xoopsTpl->assign('toolbar', Utility::toolbar_bootstrap($interface_menu, false, $interface_icon));
$xoopsTpl->assign("tad_book3_adm", $tad_book3_adm);
$xoopsTpl->assign("now_op", $op);
$xoTheme->addStylesheet('modules/tad_book3/css/module.css');
require_once XOOPS_ROOT_PATH . '/footer.php';

/*-----------function區--------------*/

//更新狀態
function change_enable($enable, $tbdsn)
{
    global $xoopsDB;
    $sql = 'UPDATE `' . $xoopsDB->prefix('tad_book3_docs') . '` SET `enable` = ? WHERE `tbdsn` = ?';
    Utility::query($sql, 'si', [$enable, $tbdsn]) or Utility::web_error($sql, __FILE__, __LINE__);

}

//tad_book3編輯表單
function import_form($tbsn = '')
{
    global $xoopsDB, $xoopsUser, $xoopsTpl;
    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    //抓取預設值
    if (!empty($tbsn)) {
        $DBV = get_tad_book3($tbsn);
    } else {
        $DBV = [];
    }

    //預設值設定

    $tbsn        = (!isset($DBV['tbsn'])) ? '' : $DBV['tbsn'];
    $tbcsn       = (!isset($DBV['tbcsn'])) ? '' : $DBV['tbcsn'];
    $sort        = (!isset($DBV['sort'])) ? get_max_doc_sort($tbcsn) : $DBV['sort'];
    $title       = (!isset($DBV['title'])) ? '' : $DBV['title'];
    $description = (!isset($DBV['description'])) ? '' : $DBV['description'];
    $author      = (!isset($DBV['author'])) ? '' : $DBV['author'];
    $read_group  = (!isset($DBV['read_group'])) ? '' : $DBV['read_group'];
    $video_group = (!isset($DBV['video_group'])) ? '' : $DBV['video_group'];
    $passwd      = (!isset($DBV['passwd'])) ? '' : $DBV['passwd'];
    $enable      = (!isset($DBV['enable'])) ? '1' : $DBV['enable'];
    $pic_name    = (!isset($DBV['pic_name'])) ? '' : $DBV['pic_name'];
    $counter     = (!isset($DBV['counter'])) ? '' : $DBV['counter'];
    $create_date = (!isset($DBV['create_date'])) ? '' : $DBV['create_date'];

    $ck = new CkEditor('tad_book3', 'description', $description);
    $ck->setHeight(400);
    $editor = $ck->render();

    $author_arr = (empty($author)) ? [$xoopsUser->uid()] : explode(',', $author);

    $cate_select = cate_select($tbcsn);

    $memberHandler = xoops_getHandler('member');
    $usercount     = $memberHandler->getUserCount(new \Criteria('level', 0, '>'));

    if ($usercount < 1000) {
        $select = new \XoopsFormSelect('', 'author', $author_arr, 5, true);
        $select->setExtra("class='form-control'");
        $memberHandler = xoops_getHandler('member');
        $criteria      = new \CriteriaCompo();
        $criteria->setSort('uname');
        $criteria->setOrder('ASC');
        $criteria->setLimit(1000);
        $criteria->setStart(0);

        $select->addOptionArray($memberHandler->getUserList($criteria));
        $user_menu = $select->render();
    } else {
        $user_menu = "<textarea name='author_str' style='width:100%;'>$author</textarea>
    <div>user uid, ex:\"1,27,103\"</div>";
    }

    $group_arr   = (empty($read_group)) ? [''] : explode(',', $read_group);
    $SelectGroup = new \XoopsFormSelectGroup('', 'read_group', false, $group_arr, 5, true);
    $SelectGroup->addOption('', _MD_TADBOOK3_ALL_OPEN, false);
    $SelectGroup->setExtra("class='form-control'");
    $group_menu = $SelectGroup->render();

    $video_group_arr = (empty($video_group)) ? [''] : explode(',', $video_group);
    $SelectGroup     = new \XoopsFormSelectGroup('', 'video_group', false, $video_group_arr, 5, true);
    $SelectGroup->setExtra("class='form-control'");
    $SelectGroup->addOption('', _MD_TADBOOK3_ALL_OPEN, false);
    $video_group_menu = $SelectGroup->render();

    $xoopsTpl->assign('action', $_SERVER['PHP_SELF']);
    $xoopsTpl->assign('tbsn', $tbsn);
    $xoopsTpl->assign('cate_select', $cate_select);
    $xoopsTpl->assign('user_menu', $user_menu);
    $xoopsTpl->assign('group_menu', $group_menu);
    $xoopsTpl->assign('now_op', 'import_form');
    $xoopsTpl->assign('upload_note', sprintf(_MD_TADBOOK3_UL_FILE, XOOPS_ROOT_PATH . '/uploads/tad_book3/'));
    $xoopsTpl->assign('new_path', sprintf(_MD_TADBOOK3_ABS_PATH, XOOPS_URL));
    $XOOPS_URL = str_replace('//', '', XOOPS_URL);

    if (false !== mb_strpos('/', $XOOPS_URL)) {
        $xoopsTpl->assign('checked', '');
    } else {
        $xoopsTpl->assign('checked', 'checked');
    }
}

//匯入書籍
function import_book($tbcsn)
{
    global $xoopsDB;
    if (!empty($_POST['new_tbcsn'])) {
        $tbcsn = add_tad_book3_cate();
    } else {
        $tbcsn = (int) $_POST['tbcsn'];
    }

    $tadbook3_dir = XOOPS_ROOT_PATH . '/uploads/tad_book3';
    if (!empty($_POST['author_str'])) {
        $author = (string) $_POST['author_str'];
    } else {
        $author = implode(',', $_POST['author']);
    }
    $read_group  = (in_array('', $_POST['read_group'])) ? '' : implode(',', $_POST['read_group']);
    $video_group = (in_array('', $_POST['video_group'])) ? '' : implode(',', $_POST['video_group']);

    $book_sql = file_get_contents($_FILES['book']['tmp_name']);
    $book_sql = str_replace('`tad_book3`', '`' . $xoopsDB->prefix('tad_book3') . '`', $book_sql);
    $book_sql = str_replace('{{tbcsn}}', $tbcsn, $book_sql);
    $book_sql = str_replace('{{author}}', $author, $book_sql);
    $book_sql = str_replace('{{read_group}}', $read_group, $book_sql);
    $book_sql = str_replace('{{video_group}}', $video_group, $book_sql);
    $xoopsDB->queryF($book_sql) or Utility::web_error($sql, __FILE__, __LINE__);
    //取得最後新增資料的流水編號
    $tbsn = $xoopsDB->getInsertId();

    //取出亂數資料夾內容
    $sql    = 'SELECT `pic_name` FROM `' . $xoopsDB->prefix('tad_book3') . '` WHERE `tbsn`=?';
    $result = Utility::query($sql, 'i', [$tbsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    list($rand) = $xoopsDB->fetchRow($result);

    //修改書籍封面圖
    $sql = 'UPDATE `' . $xoopsDB->prefix('tad_book3') . '`
    SET `pic_name` = ?
    WHERE `tbsn` = ?';

    $params = ["book_{$tbsn}.png", $tbsn];
    $result = Utility::query($sql, 'si', $params) or Utility::web_error($sql, __FILE__, __LINE__);

    //產生書籍封面圖
    copy("{$tadbook3_dir}/file/{$rand}/book.png", "{$tadbook3_dir}/book_{$tbsn}.png");

    $docs_sql = file_get_contents($_FILES['docs']['tmp_name']);
    $docs_sql = str_replace('`tad_book3_docs`', '`' . $xoopsDB->prefix('tad_book3_docs') . '`', $docs_sql);
    $docs_sql = str_replace('{{tbsn}}', $tbsn, $docs_sql);

    if ('1' == $_POST['abs_path']) {
        $docs_sql = str_replace('{{path}}', XOOPS_URL, $docs_sql);
    } else {
        $docs_sql = str_replace('{{path}}', '', $docs_sql);
    }

    $docs_sql_arr = explode('--tad_book3_import_doc--', $docs_sql);
    foreach ($docs_sql_arr as $docs_sql) {
        $sql = trim($docs_sql);
        if (!empty($sql)) {
            $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);
        }
    }

    return $tbsn;
}

function tad_book3_export($tbsn = '')
{
    global $xoopsDB, $xoopsUser;
    if ($xoopsUser) {
        $uid = $xoopsUser->uid();
    } else {
        $uid = 0;
    }

    //輸出書籍設定
    $sql    = 'SELECT * FROM `' . $xoopsDB->prefix('tad_book3') . '` WHERE `tbsn`=?';
    $result = Utility::query($sql, 'i', [$tbsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    $book = $xoopsDB->fetchArray($result);

    //共同編輯者
    $author_arr = explode(',', $book['author']);
    if (!in_array($uid, $author_arr)) {
        redirect_header($_SERVER['PHP_SELF'], 3, _MD_TADBOOK3_NEED_AUTHOR);
    }

    $rand = Utility::randStr();

    $tadbook3_dir     = XOOPS_ROOT_PATH . '/uploads/tad_book3';
    $import_dir       = str_replace(['|', '%', ' ', '<', '>'], '', "{$tadbook3_dir}/import_{$tbsn}");
    $from_file_dir    = "{$tadbook3_dir}/file";
    $from_image_dir   = "{$tadbook3_dir}/image";
    $import_file_dir  = "{$import_dir}/file/{$rand}";
    $import_image_dir = "{$import_dir}/image/{$rand}";
    $bookfile         = "{$import_dir}/1_book.sql";
    $docsfile         = "{$import_dir}/2_docs.sql";
    Utility::rrmdir($import_dir);
    Utility::mk_dir($import_dir);
    Utility::mk_dir($import_dir . '/file');
    Utility::mk_dir($import_dir . '/image');
    Utility::mk_dir($import_file_dir);
    Utility::mk_dir($import_image_dir);

    copy($tadbook3_dir . "/{$book['pic_name']}", $import_file_dir . '/book.png');

    $cols = $vals = '';
    foreach ($book as $col => $val) {
        if ('tbsn' === $col) {
            continue;
        }

        if ('tbcsn' === $col) {
            $val = '{{tbcsn}}';
        } elseif ('author' === $col) {
            $val = '{{author}}';
        } elseif ('read_group' === $col) {
            $val = '{{read_group}}';
        } elseif ('pic_name' === $col) {
            $val = $rand;
        } else {
            $val = $xoopsDB->escape($val);
        }
        $cols .= "`{$col}`, ";
        $vals .= "'{$val}', ";
    }
    $cols    = mb_substr($cols, 0, -2);
    $vals    = mb_substr($vals, 0, -2);
    $current = "insert into `tad_book3` ({$cols}) values({$vals});\n";

    file_put_contents($bookfile, $current);

    //輸出文章設定
    $current = '';
    $sql     = 'SELECT * FROM `' . $xoopsDB->prefix('tad_book3_docs') . '` WHERE `tbsn`=? ORDER BY `category`, `page`, `paragraph`, `sort`';
    $result  = Utility::query($sql, 'i', [$tbsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    $all = '';
    while (false !== ($doc = $xoopsDB->fetchArray($result))) {
        $cols = $vals = '';
        foreach ($doc as $col => $val) {
            if ('tbdsn' === $col) {
                continue;
            }

            if ('tbsn' === $col) {
                $val = '{{tbsn}}';
            } else {
                if (false !== mb_strpos($val, '/uploads/tad_book3/image')) {
                    preg_match_all('/src="([^"]+)/', $val, $match);
                    foreach ($match[1] as $image_url) {
                        $strpos = mb_strpos($image_url, '/uploads/tad_book3/image');
                        if (false !== $strpos) {
                            $image = '{{path}}' . mb_substr($image_url, $strpos);

                            $val = str_replace($image_url, str_replace('tad_book3/image', "tad_book3/image/{$rand}", $image), $val);

                            $form_image = XOOPS_ROOT_PATH . $image;
                            $new_image  = XOOPS_ROOT_PATH . str_replace('tad_book3/image', "tad_book3/import_{$tbsn}/image/{$rand}", $image);
                            $image_dir  = mb_substr(dirname(str_replace($from_image_dir, '', $form_image)), 1);
                            $dirs       = explode('/', $image_dir);
                            if (is_array($dirs)) {
                                $new_import_image_dir = $import_image_dir;
                                foreach ($dirs as $d) {
                                    $new_import_image_dir = $new_import_image_dir . '/' . $d;
                                    Utility::mk_dir($new_import_image_dir);
                                }
                            }

                            if (file_exists($form_image)) {
                                if (copy($form_image, $new_image)) {
                                    $all .= "<li>[{$image}] {$form_image}→{$new_image}</li>";
                                } else {
                                    $all .= "<li style='color:red'>{$form_image}→{$new_image} 複製失敗！</li>";
                                }
                            }
                        }
                    }
                }

                if (false !== mb_strpos($val, '/uploads/tad_book3/file')) {
                    preg_match_all('/href="([^"]+)/', $val, $match2);
                    foreach ($match2[1] as $file_url) {
                        $strpos = mb_strpos($file_url, '/uploads/tad_book3/file');
                        if (false !== $strpos) {
                            $file = '{{path}}' . mb_substr($file_url, $strpos);

                            $val = str_replace($file_url, str_replace('tad_book3/file', "tad_book3/file/{$rand}", $file), $val);

                            $form_file = XOOPS_ROOT_PATH . $file;
                            $new_file  = XOOPS_ROOT_PATH . str_replace('tad_book3/file', "tad_book3/import_{$tbsn}/file/{$rand}", $file);
                            $file_dir  = mb_substr(dirname(str_replace($from_file_dir, '', $form_file)), 1);
                            $dirs      = explode('/', $file_dir);
                            if (is_array($dirs)) {
                                $new_import_file_dir = $import_file_dir;
                                foreach ($dirs as $d) {
                                    $new_import_file_dir = $new_import_file_dir . '/' . $d;
                                    Utility::mk_dir($new_import_file_dir);
                                }
                            }

                            if (file_exists($form_file)) {
                                if (copy($form_file, $new_file)) {
                                    $all .= "<li>[{$file}] {$form_file}→{$new_file}</li>";
                                } else {
                                    $all .= "<li style='color:red'>{$form_file}→{$new_file} 複製失敗！</li>";
                                }
                            }
                        }
                    }
                }

                $val = $xoopsDB->escape($val);
            }
            $cols .= "`{$col}`, ";
            $vals .= "'{$val}', ";
        }
        $cols = mb_substr($cols, 0, -2);
        $vals = mb_substr($vals, 0, -2);
        $current .= "insert into `tad_book3_docs` ({$cols}) values({$vals});\n--tad_book3_import_doc--\n";
    }

    file_put_contents($docsfile, $current);

    $zip_name = str_replace(['|', '%', ' ', '<', '>'], '', XOOPS_ROOT_PATH . "/uploads/tad_book3/import_{$tbsn}.zip");
    if (file_exists($zip_name)) {
        unlink($zip_name);
    }

    $msg = shell_exec("zip -r -j {$zip_name} $import_dir");

    if (file_exists($zip_name)) {
        header('location:' . XOOPS_URL . "/uploads/tad_book3/import_{$tbsn}.zip");
    } else {
        require_once __DIR__ . '/class/pclzip.lib.php';
        $zipfile = new PclZip($zip_name);
        $v_list  = $zipfile->create($import_dir, PCLZIP_OPT_REMOVE_PATH, XOOPS_ROOT_PATH . '/uploads/tad_book3');

        if (0 == $v_list) {
            die('Error : ' . $archive->errorInfo(true));
        }
        header('location:' . XOOPS_URL . "/uploads/tad_book3/import_{$tbsn}.zip");
    }

    exit;
    die("<ol>$all</ol>");
    //http://120.115.2.90/uploads/tad_book3/file/school_news_20140815.zip
}

//更新排序
function update_docs_sort($update_sort = [])
{
    global $xoopsDB;
    foreach ($update_sort as $tbdsn => $doc_sort) {
        $doc_sort_arr = decode_category($doc_sort);
        $sql          = 'UPDATE `' . $xoopsDB->prefix('tad_book3_docs') . '` SET `category` = ?, `page` = ?, `paragraph` = ?, `sort` = ? WHERE `tbdsn` = ?';
        Utility::query($sql, 'iiiii', [$doc_sort_arr['category'], $doc_sort_arr['page'], $doc_sort_arr['paragraph'], $doc_sort_arr['sort'], $tbdsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    }
}
