<?php
use XoopsModules\Tadtools\CkEditor;
use XoopsModules\Tadtools\My97DatePicker;
use XoopsModules\Tadtools\TadDataCenter;
use XoopsModules\Tadtools\TadUpFiles;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tadtools\Wcag;

require __DIR__ . '/vendor/autoload.php';

//tad_book3_docs編輯表單
function tad_book3_docs_form($tbdsn = '', $tbsn = '')
{
    global $xoopsDB, $xoopsUser, $xoopsModule, $xoopsModuleConfig, $xoopsTpl;
    require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

    if ($xoopsUser) {
        $module_id = $xoopsModule->mid();
        $_SESSION['tad_book3_adm'] = $xoopsUser->isAdmin($module_id);
    } else {
        $_SESSION['tad_book3_adm'] = false;
    }

    //抓取預設值
    $tbsn = !isset($DBV['tbsn']) ? $tbsn : $DBV['tbsn'];

    $book = get_tad_book3($tbsn);
    if (!$_SESSION['tad_book3_adm']) {
        if (!chk_edit_power($book['author'])) {
            header('location:index.php');
            exit;
        }
    }

    if (!empty($tbdsn)) {
        $DBV = get_tad_book3_docs($tbdsn);
        $tbsn = $DBV['tbsn'];
    } else {
        $DBV = [];
        $DBV['read_group'] = $book['read_group'];
        $DBV['video_group'] = $book['video_group'];
    }

    $DBV['enable'] = !isset($DBV['enable']) ? '1' : $DBV['enable'];
    $DBV['read_group_arr'] = explode(',', $DBV['read_group']);
    $DBV['video_group_arr'] = explode(',', $DBV['video_group']);
    foreach ($DBV as $name => $value) {
        $xoopsTpl->assign($name, $value);
        $$name = $value;
    }

    $ck = new CkEditor('tad_book3', 'content', $content);
    $ck->setHeight(400);
    $ck->setContentCss(XOOPS_URL . '/modules/tad_book3/css/reset.css');
    $ck->setContentCss(XOOPS_URL . '/modules/tad_book3/css/modules.css');
    $editor = $ck->render();

    $op = (empty($tbdsn)) ? 'insert_tad_book3_docs' : 'update_tad_book3_docs';

    $xoopsTpl->assign('action', $_SERVER['PHP_SELF']);
    $xoopsTpl->assign('book_select', book_select($tbsn));
    $xoopsTpl->assign('op', $op);
    $xoopsTpl->assign('category_menu_category', category_menu($category));
    $xoopsTpl->assign('category_menu_page', category_menu($page));
    $xoopsTpl->assign('category_menu_paragraph', category_menu($paragraph));
    $xoopsTpl->assign('category_menu_sort', category_menu($sort));
    $xoopsTpl->assign('editor', $editor);
    $xoopsTpl->assign('from_tbdsn', $from_tbdsn);
    $xoopsTpl->assign('now', date("Y-m-d H:i:s"));
    $xoopsTpl->assign('groups', Utility::get_all_groups());

    Utility::mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_book3/$tbsn");
    Utility::mk_dir(XOOPS_ROOT_PATH . "/uploads/tad_book3/$tbsn/$uid");

    $TadUpFilesMp4 = new TadUpFiles("tad_book3", "/$tbsn/$uid");
    $TadUpFilesMp4->set_var("show_tip", false); //不顯示提示
    $TadUpFilesMp4->set_col('mp4', $tbdsn); //若 $show_list_del_file ==true 時一定要有
    $upform = $TadUpFilesMp4->upform(true, 'video', 1, true, '.mp4');
    $xoopsTpl->assign('upform', $upform);

    $TadUpFilesVtt = new TadUpFiles("tad_book3", "/$tbsn/$uid");
    $TadUpFilesVtt->set_col('vtt', $tbdsn);
    $upform_vtt = $TadUpFilesVtt->upform(true, 'video_vtt', 1, true, '.vtt');
    $xoopsTpl->assign('upform_vtt', $upform_vtt);

    $TadUpFilesPic = new TadUpFiles("tad_book3", "/$tbsn/$uid");
    $TadUpFilesPic->set_col('pic', $tbdsn);
    $upform_pic = '';
    if (empty($xoopsModuleConfig['ffmpeg_path']) || !file_exists($xoopsModuleConfig['ffmpeg_path'])) {
        $upform_pic = $TadUpFilesPic->upform(true, 'video_thumb', 1, true, '.jpg,.png');
    }
    $xoopsTpl->assign('upform_pic', $upform_pic);

    $SelectGroup = new \XoopsFormSelectGroup('', 'read_group', false, $read_group_arr, 5, true);
    $SelectGroup->setExtra("class='form-control'");
    $SelectGroup->addOption('', _MD_TADBOOK3_ALL_OPEN, false);
    $group_menu = $SelectGroup->render();

    $SelectGroup = new \XoopsFormSelectGroup('', 'video_group', false, $video_group_arr, 5, true);
    $SelectGroup->setExtra("class='form-control'");
    $SelectGroup->addOption('', _MD_TADBOOK3_ALL_OPEN, false);
    $video_group_menu = $SelectGroup->render();
    $xoopsTpl->assign('group_menu', $group_menu);
    $xoopsTpl->assign('video_group_menu', $video_group_menu);
    My97DatePicker::render();

    $TadDataCenter = new TadDataCenter('tad_book3');
    $TadDataCenter->set_col('video_tbdsn_date', $tbdsn);
    $xoopsTpl->assign('video_group_date', $TadDataCenter->getData());

    $TadDataCenter->set_col('read_tbdsn_date', $tbdsn);
    $xoopsTpl->assign('read_group_date', $TadDataCenter->getData());

}

//新增資料到tad_book3_docs中
function insert_tad_book3_docs()
{
    global $xoopsDB, $xoopsUser, $xoopsModuleConfig;
    $time = time();
    //$time=xoops_getUserTimestamp(time());

    $title = $_POST['title'];
    $content = $_POST['content'];
    $content = Wcag::amend($content);
    $from_tbdsn = (int) $_POST['from_tbdsn'];

    $category = (int) $_POST['category'];
    $page = (int) $_POST['page'];
    $paragraph = (int) $_POST['paragraph'];
    $sort = (int) $_POST['sort'];
    $tbsn = (int) $_POST['tbsn'];

    check_update_cpps_add($tbsn, $category, $page, $paragraph, $sort);

    $read_group = (in_array('', $_POST['read_group'])) ? '' : implode(',', $_POST['read_group']);
    $video_group = (in_array('', $_POST['video_group'])) ? '' : implode(',', $_POST['video_group']);

    $uid = $xoopsUser->uid();
    $sql = 'INSERT INTO `' . $xoopsDB->prefix('tad_book3_docs') . '` (`tbsn`,`category`,`page`,`paragraph`,`sort`,`title`,`content`,`add_date`,`last_modify_date`,`uid`,`count`,`enable`,`read_group`,`video_group`,`from_tbdsn`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
    Utility::query($sql, 'iiiiissssiisssi', [$tbsn, $category, $page, $paragraph, $sort, $title, $content, $time, $time, $uid, 0, $_POST['enable'], $read_group, $video_group, $from_tbdsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    //取得最後新增資料的流水編號
    $tbdsn = $xoopsDB->getInsertId();

    $TadUpFilesMp4 = new TadUpFiles("tad_book3", "/$tbsn/$uid");
    $TadUpFilesMp4->set_col('mp4', $tbdsn);
    $TadUpFilesMp4->upload_file('video', null, null, null, $title, true, true);

    $TadUpFilesVtt = new TadUpFiles("tad_book3", "/$tbsn/$uid");
    $TadUpFilesVtt->set_col('vtt', $tbdsn);
    $TadUpFilesVtt->set_col('vtt', $tbdsn);
    $TadUpFilesVtt->upload_file('video_vtt', null, null, null, $title, true, true);

    if (empty($xoopsModuleConfig['ffmpeg_path']) || !file_exists($xoopsModuleConfig['ffmpeg_path'])) {
        $TadUpFilesPic = new TadUpFiles("tad_book3", "/$tbsn/$uid");
        $TadUpFilesPic->set_col('pic', $tbdsn);
        $TadUpFilesPic->upload_file('video_thumb', 1920, 480, null, $title, true, false);
    } else {
        $mp4_path = $TadUpFilesMp4->get_pic_file('file', 'dir', '', true);
        // 建立物件
        $ffmpeg = FFMpeg\FFMpeg::create(array(
            'ffmpeg.binaries' => $xoopsModuleConfig['ffmpeg_path'],
            'ffprobe.binaries' => dirname($xoopsModuleConfig['ffmpeg_path']) . '/ffprobe.exe',
            'timeout' => 3600, // 底層進程的時間上限
            'ffmpeg.threads' => 12, // FFMpeg 應該使用的線程數
        ));

        // 開啟影片
        $video = $ffmpeg->open($mp4_path);
        // 擷取第N秒的話格，並存檔
        $video
            ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
            ->save(XOOPS_ROOT_PATH . "/uploads/tad_book3/{$tbsn}/{$uid}/image/{$tbdsn}.jpg");
    }
    return $tbdsn;
}

//更新tad_book3_docs某一筆資料
function update_tad_book3_docs($tbdsn = '')
{
    global $xoopsDB, $xoopsUser, $xoopsModuleConfig;
    $time = time();

    $title = $_POST['title'];
    $content = $_POST['content'];
    $content = Wcag::amend($content);
    $from_tbdsn = (int) $_POST['from_tbdsn'];

    $category = (int) $_POST['category'];
    $page = (int) $_POST['page'];
    $paragraph = (int) $_POST['paragraph'];
    $sort = (int) $_POST['sort'];
    $tbsn = (int) $_POST['tbsn'];
    $update_child_power = (int) $_POST['update_child_power'];
    check_update_cpps_add($tbsn, $category, $page, $paragraph, $sort, $tbdsn);
    $read_group = (in_array('', $_POST['read_group'])) ? '' : implode(',', $_POST['read_group']);
    $video_group = (in_array('', $_POST['video_group'])) ? '' : implode(',', $_POST['video_group']);

    $sql = 'UPDATE `' . $xoopsDB->prefix('tad_book3_docs') . '` SET `tbsn` = ?, `category` = ?, `page` = ?, `paragraph` = ?, `sort` = ?, `title` = ?, `content` = ?, `last_modify_date` = ?, `enable` = ?, `read_group` = ?, `video_group` = ?, `from_tbdsn` = ? WHERE `tbdsn` = ?';
    Utility::query($sql, 'iiiiissssssii', [$tbsn, $category, $page, $paragraph, $sort, $title, $content, $time, $_POST['enable'], $read_group, $video_group, $from_tbdsn, $tbdsn]) or Utility::web_error($sql, __FILE__, __LINE__);

    // 修改子文件權限
    if ($update_child_power) {
        update_child_power($tbsn, $category, $page, $paragraph, $read_group, $video_group);
    }

    // 修改可觀看日期
    update_view_date($tbdsn, $_POST['read_group_date'], $_POST['video_group_date']);
    if ($update_child_power) {
        update_child_date($tbsn, $category, $page, $paragraph, $_POST['read_group_date'], $_POST['video_group_date']);
    }

    $uid = $xoopsUser->uid();
    $TadUpFilesMp4 = new TadUpFiles("tad_book3", "/$tbsn/$uid");
    $TadUpFilesMp4->set_col('mp4', $tbdsn);
    $TadUpFilesMp4->upload_file('video', null, null, null, $title, true, true);

    $TadUpFilesVtt = new TadUpFiles("tad_book3", "/$tbsn/$uid");
    $TadUpFilesVtt->set_col('vtt', $tbdsn);
    $TadUpFilesVtt->upload_file('video_vtt', null, null, null, $title, true, true);

    if (empty($xoopsModuleConfig['ffmpeg_path']) || !file_exists($xoopsModuleConfig['ffmpeg_path'])) {
        $TadUpFilesPic = new TadUpFiles("tad_book3", "/$tbsn/$uid");
        $TadUpFilesPic->set_col('pic', $tbdsn);
        $TadUpFilesPic->upload_file('video_thumb', 1920, 480, null, $title, true, false);
    } else {
        $mp4_path = $TadUpFilesMp4->get_pic_file('file', 'dir', '', true);
        // 建立物件
        $ffmpeg = FFMpeg\FFMpeg::create(array(
            'ffmpeg.binaries' => $xoopsModuleConfig['ffmpeg_path'],
            'ffprobe.binaries' => dirname($xoopsModuleConfig['ffmpeg_path']) . '/ffprobe.exe',
            'timeout' => 3600, // 底層進程的時間上限
            'ffmpeg.threads' => 12, // FFMpeg 應該使用的線程數
        ));

        // 開啟影片
        $video = $ffmpeg->open($mp4_path);
        // 擷取第N秒的話格，並存檔
        $video
            ->frame(FFMpeg\Coordinate\TimeCode::fromSeconds(10))
            ->save(XOOPS_ROOT_PATH . "/uploads/tad_book3/{$tbsn}/{$uid}/image/{$tbdsn}.jpg");
    }

    return $tbdsn;
}

//檢查是否有相同的章節數，若有其他章節往後移動（插入之意）
function check_update_cpps_add($tbsn = 0, $category = 0, $page = 0, $paragraph = 0, $sort = 0, $tbdsn = 0)
{
    global $xoopsDB;
    $and_tbdsn = $tbdsn ? 'AND `tbdsn` != ?' : '';
    $sql = 'SELECT `tbdsn` FROM `' . $xoopsDB->prefix('tad_book3_docs') . '` WHERE `tbsn` = ? AND `category` = ? AND `page` = ? AND `paragraph` = ? AND `sort` = ? ' . $and_tbdsn;
    $params = $tbdsn ? [$tbsn, $category, $page, $paragraph, $sort, $tbdsn] : [$tbsn, $category, $page, $paragraph, $sort];
    $types = $tbdsn ? 'iiiiii' : 'iiiii'; // 根據 tbdsn 是否存在決定類型字串和參數数量
    $result = Utility::query($sql, $types, $params) or Utility::web_error($sql, __FILE__, __LINE__);

    list($tbdsn) = $xoopsDB->fetchRow($result);

    if (!empty($tbdsn)) {
        $updateField = '';
        $whereConditions = ["`tbsn` = ?"];
        $params = [$tbsn];

        if (!empty($category)) {
            if (!empty($page)) {
                if (!empty($paragraph)) {
                    $updateField = '`sort` = `sort` + 1';
                    $whereConditions[] = "`category` = ? AND `page` = ? AND `paragraph` = ? AND `sort` >= ?";
                    $params = array_merge($params, [$category, $page, $paragraph, $sort]);
                } else {
                    $updateField = '`paragraph` = `paragraph` + 1';
                    $whereConditions[] = "`category` = ? AND `page` = ? AND `paragraph` >= ?";
                    $params = array_merge($params, [$category, $page, $paragraph]);
                }
            } else {
                $updateField = '`category` = `category` + 1';
                $whereConditions[] = "`category` >= ?";
                $params[] = $category;
            }
        } else {
            $updateField = '`page` = `page` + 1';
            $whereConditions[] = "`page` >= ?";
            $params[] = $page;
        }

        if ($updateField) {
            $sql = 'UPDATE `' . $xoopsDB->prefix('tad_book3_docs') . '`
                    SET ' . $updateField . '
                    WHERE ' . implode(' AND ', $whereConditions);

            $result = Utility::query($sql, str_repeat('i', count($params)), $params) or Utility::web_error($sql, __FILE__, __LINE__);
        }
    }

}

//檢查底下的章節數，若有父編號要跟著變觀看起始日期
function update_child_date($tbsn, $category, $page, $paragraph, $read_group_date = [], $video_group_date = [])
{
    global $xoopsDB;
    $conditions = ["`tbsn` = ?"];
    $params = [$tbsn];

    if (!empty($category)) {
        $conditions[] = "`category` = ?";
        $params[] = $category;

        if (!empty($page)) {
            $conditions[] = "`page` = ?";
            $params[] = $page;

            if (!empty($paragraph)) {
                $conditions[] = "`paragraph` = ?";
                $params[] = $paragraph;
            }
        }
    }

    $sql = 'SELECT `tbdsn` FROM `' . $xoopsDB->prefix('tad_book3_docs') . '` WHERE ' . implode(' AND ', $conditions);
    $result = Utility::query($sql, str_repeat('i', count($params)), $params) or Utility::web_error($sql, __FILE__, __LINE__);

    while (list($tbdsn) = $xoopsDB->fetchRow($result)) {
        update_view_date($tbdsn, $read_group_date, $video_group_date);
    }

}

//更新觀看起始日期
function update_view_date($tbdsn, $read_group_date = [], $video_group_date = [])
{
    $TadDataCenter = new TadDataCenter('tad_book3');
    $TadDataCenter->set_col('read_tbdsn_date', $tbdsn);
    foreach ($read_group_date as $gid => $read_start_date) {
        $read_group_date_arr[$gid] = [$read_start_date];
    }
    $TadDataCenter->saveCustomData($read_group_date_arr);

    $TadDataCenter->set_col('video_tbdsn_date', $tbdsn);
    foreach ($video_group_date as $gid => $video_start_date) {
        $video_group_date_arr[$gid] = [$video_start_date];
    }
    $TadDataCenter->saveCustomData($video_group_date_arr);
}

//檢查底下的章節數，若有父編號要跟著變
function update_child_power($tbsn, $category, $page, $paragraph, $read_group, $video_group)
{
    global $xoopsDB;
    $conditions = ["`tbsn` = ?"];
    $params = [$tbsn];

    if (!empty($category)) {
        $conditions[] = "`category` = ?";
        $params[] = $category;

        if (!empty($page)) {
            $conditions[] = "`page` = ?";
            $params[] = $page;

            if (!empty($paragraph)) {
                $conditions[] = "`paragraph` = ?";
                $params[] = $paragraph;
            }
        }
    }

    $sql = 'SELECT `tbdsn` FROM `' . $xoopsDB->prefix('tad_book3_docs') . '` WHERE ' . implode(' AND ', $conditions);
    $result = Utility::query($sql, str_repeat('i', count($params)), $params) or Utility::web_error($sql, __FILE__, __LINE__);

    while (list($tbdsn) = $xoopsDB->fetchRow($result)) {
        $sql = 'UPDATE `' . $xoopsDB->prefix('tad_book3_docs') . '` SET `read_group` = ?, `video_group` = ? WHERE `tbdsn` = ?';
        Utility::query($sql, 'ssi', [$read_group, $video_group, $tbdsn]) or Utility::web_error($sql, __FILE__, __LINE__);
    }
}

//檢查底下的章節數，若有父編號要跟著變
function check_update_child($tbsn, $category, $page, $paragraph, $category_old, $page_old, $paragraph_old)
{
    global $xoopsDB;

    if ($category == $category_old && $page == $page_old && $paragraph != $paragraph_old) {
        // 2-5-4 改為 2-5-5（要把 2-5-4-1 改為 2-5-5-1）
        $sql = 'SELECT `tbdsn` FROM `' . $xoopsDB->prefix('tad_book3_docs') . '` WHERE `tbsn`=? AND `category`=? AND `page`=? AND `paragraph`=?';
        $result = Utility::query($sql, 'iiii', [$tbsn, $category, $page, $paragraph_old]) or Utility::web_error($sql, __FILE__, __LINE__);

        while (list($tbdsn) = $xoopsDB->fetchRow($result)) {
            $sql = 'UPDATE `' . $xoopsDB->prefix('tad_book3_docs') . '` SET `paragraph` =? WHERE `tbdsn` =?';
            Utility::query($sql, 'ii', [$paragraph, $tbdsn]) or Utility::web_error($sql, __FILE__, __LINE__);

        }
    } elseif ($category == $category_old && $page != $page_old && $paragraph == $paragraph_old) {
        // 2-5-4 改為 2-6-4（要把 2-5-4-1 改為 2-6-4-1）
        $sql = 'SELECT `tbdsn` FROM `' . $xoopsDB->prefix('tad_book3_docs') . '` WHERE `tbsn`=? AND `category`=? AND `page`=?';
        $result = Utility::query($sql, 'iii', [$tbsn, $category, $page_old]) or Utility::web_error($sql, __FILE__, __LINE__);

        while (list($tbdsn) = $xoopsDB->fetchRow($result)) {
            $sql = 'UPDATE `' . $xoopsDB->prefix('tad_book3_docs') . '` SET `page` = ? WHERE `tbdsn` = ?';
            Utility::query($sql, 'ii', [$page, $tbdsn]) or Utility::web_error($sql, __FILE__, __LINE__);

        }
    } elseif ($category != $category_old && $page == $page_old && $paragraph == $paragraph_old) {
        // 2-5-4 改為 3-5-4（要把 2-5-4-1 改為 3-5-4-1）
        $sql = 'SELECT `tbdsn` FROM `' . $xoopsDB->prefix('tad_book3_docs') . '` WHERE `tbsn`=? AND `category`=?';
        $result = Utility::query($sql, 'ii', [$tbsn, $category_old]) or Utility::web_error($sql, __FILE__, __LINE__);

        while (list($tbdsn) = $xoopsDB->fetchRow($result)) {
            $sql = 'UPDATE `' . $xoopsDB->prefix('tad_book3_docs') . '` SET `category` = ? WHERE `tbdsn` = ?';
            Utility::query($sql, 'ii', [$category, $tbdsn]) or Utility::web_error($sql, __FILE__, __LINE__);

        }
    } elseif ($category != $category_old && $page != $page_old && $paragraph == $paragraph_old) {
        // 2-5-4 改為 3-6-4（要把 2-5-4-1 改為 3-5-4-1）
        $sql = 'SELECT `tbdsn` FROM `' . $xoopsDB->prefix('tad_book3_docs') . '` WHERE `tbsn`=? AND `category`=? AND `page`=?';
        $result = Utility::query($sql, 'iii', [$tbsn, $category_old, $page_old]) or Utility::web_error($sql, __FILE__, __LINE__);

        while (list($tbdsn) = $xoopsDB->fetchRow($result)) {
            $sql = 'UPDATE `' . $xoopsDB->prefix('tad_book3_docs') . '` SET `category` = ?, `page` = ? WHERE `tbdsn` = ?';
            Utility::query($sql, 'iii', [$category, $page, $tbdsn]) or Utility::web_error($sql, __FILE__, __LINE__);

        }
    }
}
