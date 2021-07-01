<?php
use XoopsModules\Tadtools\CkEditor;
use XoopsModules\Tadtools\TadUpFiles;
use XoopsModules\Tadtools\Utility;
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
    if (!empty($tbdsn)) {
        $DBV = get_tad_book3_docs($tbdsn);
        $tbsn = $DBV['tbsn'];
    } else {
        $DBV = [];
    }

    if (!$_SESSION['tad_book3_adm']) {
        $book = get_tad_book3($tbsn);
        if (!chk_edit_power($book['author'])) {
            header('location:index.php');
        }
    }

    $DBV['enable'] = !isset($DBV['enable']) ? '1' : $DBV['enable'];
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

}

//新增資料到tad_book3_docs中
function insert_tad_book3_docs()
{
    global $xoopsDB, $xoopsUser, $xoopsModuleConfig;
    $time = time();
    //$time=xoops_getUserTimestamp(time());

    $myts = \MyTextSanitizer::getInstance();
    $title = $myts->addSlashes($_POST['title']);
    $content = $myts->addSlashes($_POST['content']);
    $from_tbdsn = (int) $_POST['from_tbdsn'];

    $category = (int) $_POST['category'];
    $page = (int) $_POST['page'];
    $paragraph = (int) $_POST['paragraph'];
    $sort = (int) $_POST['sort'];
    $tbsn = (int) $_POST['tbsn'];

    check_update_cpps_add($tbsn, $category, $page, $paragraph, $sort);

    $uid = $xoopsUser->uid();
    $sql = 'insert into ' . $xoopsDB->prefix('tad_book3_docs') . " (`tbsn`,`category`,`page`,`paragraph`,`sort`,`title`,`content`,`add_date`,`last_modify_date`,`uid`,`count`,`enable`,`from_tbdsn`) values('{$tbsn}','{$category}','{$page}','{$paragraph}','{$sort}','{$title}','{$content}','{$time}','{$time}','{$uid}','0','{$_POST['enable']}','{$from_tbdsn}')";
    $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
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
    //$time=xoops_getUserTimestamp(time());
    $myts = \MyTextSanitizer::getInstance();
    $title = $myts->addSlashes($_POST['title']);
    $content = $myts->addSlashes($_POST['content']);
    $from_tbdsn = (int) $_POST['from_tbdsn'];

    $category = (int) $_POST['category'];
    $page = (int) $_POST['page'];
    $paragraph = (int) $_POST['paragraph'];
    $sort = (int) $_POST['sort'];
    $tbsn = (int) $_POST['tbsn'];

    check_update_cpps_add($tbsn, $category, $page, $paragraph, $sort, $tbdsn);

    $sql = 'update ' . $xoopsDB->prefix('tad_book3_docs') . " set  `tbsn` = '{$tbsn}', `category` = '{$category}', `page` = '{$page}', `paragraph` = '{$paragraph}', `sort` = '{$sort}', `title` = '{$title}', `content` = '{$content}', `last_modify_date` = '{$time}', `enable` = '{$_POST['enable']}', `from_tbdsn` = '{$from_tbdsn}' where tbdsn='$tbdsn'";
    $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);

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
    $and_tbdsn = $tbdsn ? "and `tbdsn`!='{$tbdsn}'" : '';
    $sql = 'select tbdsn from ' . $xoopsDB->prefix('tad_book3_docs') . " where tbsn='{$tbsn}' and `category`='{$category}' and `page`='{$page}' and `paragraph`='{$paragraph}' and `sort`='{$sort}' {$and_tbdsn}";
    $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
    list($tbdsn) = $xoopsDB->fetchRow($result);

    if (!empty($tbdsn)) {
        if (!empty($category) and !empty($page) and !empty($paragraph) and !empty($sort)) {
            $sql = 'update ' . $xoopsDB->prefix('tad_book3_docs') . " set `sort` = `sort` + 1 where  tbsn='{$tbsn}' and `category` = '{$category}' and `page` = '{$page}' and `paragraph` = '{$paragraph}' and `sort` >= '{$sort}'";
            $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
        } elseif (!empty($category) and !empty($page) and !empty($paragraph) and empty($sort)) {
            $sql = 'update ' . $xoopsDB->prefix('tad_book3_docs') . " set `paragraph` = `paragraph` + 1 where tbsn='{$tbsn}' and  `category` = '{$category}' and `page` = '{$page}' and `paragraph` >= '{$paragraph}'";
            $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
        } elseif (!empty($category) and !empty($page) and empty($paragraph) and empty($sort)) {
            $sql = 'update ' . $xoopsDB->prefix('tad_book3_docs') . " set `page` = `page` + 1 where  tbsn='{$tbsn}' and `category` = '{$category}' and `page` >= '{$page}'";
            $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
        } elseif (!empty($category) and empty($page) and empty($paragraph) and empty($sort)) {
            $sql = 'update ' . $xoopsDB->prefix('tad_book3_docs') . " set `category` = `category` + 1 where tbsn='{$tbsn}' and  `category` >= '{$category}' ";
            $result = $xoopsDB->query($sql) or Utility::web_error($sql, __FILE__, __LINE__);
        }
    }
}
