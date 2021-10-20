<?php
use Xmf\Request;
use XoopsModules\Tadtools\SyntaxHighlighter;
use XoopsModules\Tadtools\TadUpFiles;
use XoopsModules\Tadtools\Utility;
use XoopsModules\Tadtools\VideoJs;

/*-----------引入檔案區--------------*/
require __DIR__ . '/header.php';
$xoopsOption['template_main'] = 'tadbook3_index.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
/*-----------function區--------------*/

//觀看某一頁
function view_page($tbdsn = '')
{
    global $xoopsDB, $xoopsModuleConfig, $xoopsTpl, $xoopsUser;

    add_counter($tbdsn);
    $all = get_tad_book3_docs($tbdsn);
    foreach ($all as $key => $value) {
        $$key = $value;
    }

    if (empty($content) and empty($from_tbdsn)) {
        header("location: index.php?op=list_docs&tbsn=$tbsn#doc{$tbdsn}");
        exit;
    }

    if (!empty($from_tbdsn)) {
        $form_page = get_tad_book3_docs($from_tbdsn);
        $content .= $form_page['content'];
    }

    $book = get_tad_book3($tbsn);

    if (!chk_power($book['read_group'], $read_group)) {
        redirect_header('index.php', 3, _MD_TADBOOK3_CANT_READ);
    } else {
        $now = time();
        $start_ts = get_start_ts($tbdsn, 'read', $read_group);
        if ($start_ts && $start_ts > $now) {
            $start_time = date('Y-m-d H:i:s', $start_ts);
            redirect_header('index.php', 3, sprintf(_MD_TADBOOK3_READ_DATE, $start_time));
        }
    }

    $needpasswd = 0;
    if (!empty($book['passwd']) and $_SESSION['passwd'] != $book['passwd']) {
        $needpasswd = 1;
    }

    $doc_select = doc_select($tbsn, $tbdsn);
    $near_docs = near_docs($tbsn, $tbdsn);
    $prev = explode(';', $near_docs['prev']);
    $next = explode(';', $near_docs['next']);

    $p = (empty($prev[1])) ? '' : "<a href='page.php?tbsn={$tbsn}&tbdsn={$prev[0]}' style='text-decoration: none;'><img src='images/arrow_left.png' alt='prev' title='Prev' border='0' align='absmiddle' hspace=4>{$prev[1]}</a>";
    $n = (empty($next[1])) ? '' : "<a href='page.php?tbsn={$tbsn}&tbdsn={$next[0]}' style='text-decoration: none;'>{$next[1]}<img src='images/arrow_right.png' alt='next' title='next' border='0' align='absmiddle' hspace=4></a>";

    $doc_sort = mk_category($category, $page, $paragraph, $sort);

    $facebook_comments = Utility::facebook_comments($xoopsModuleConfig['facebook_comments_width'], 'tad_book3', 'page.php', 'tbdsn', $tbdsn);

    //高亮度語法
    $SyntaxHighlighter = new SyntaxHighlighter();
    $SyntaxHighlighter->render();

    $xoopsTpl->assign('tbsn', $tbsn);
    $xoopsTpl->assign('book_title', $book['title']);
    $xoopsTpl->assign('doc_sort_main', $doc_sort['main']);
    $xoopsTpl->assign('title', $title);
    $xoopsTpl->assign('doc_sort_level', $doc_sort['level']);
    $xoopsTpl->assign('content', $content);
    $xoopsTpl->assign('p', $p);
    $xoopsTpl->assign('n', $n);
    $xoopsTpl->assign('doc_select', $doc_select);
    $xoopsTpl->assign('facebook_comments', $facebook_comments);
    $xoopsTpl->assign('push_url', Utility::push_url());
    $xoopsTpl->assign('tbdsn', $tbdsn);
    $xoopsTpl->assign('needpasswd', $needpasswd);
    $xoopsTpl->assign('view_video', chk_power($book['video_group'], $video_group));
    $video_start_ts = get_start_ts($tbdsn, 'video', $video_group);
    $xoopsTpl->assign('view_video_ts', $video_start_ts);
    $xoopsTpl->assign('view_video_date', date('Y-m-d H:i:s', $video_start_ts));
    $xoopsTpl->assign('now', time());
    $xoopsTpl->assign('video_group_txt', Utility::txt_to_group_name($book['video_group'], '', _MD_TADBOOK3_AND_SYMBOL));
    $xoopsTpl->assign('use_social_tools', $xoopsModuleConfig['use_social_tools']);

    $xoopsTpl->assign('fb_title', $title);
    $xoopsTpl->assign('fb_description', mb_substr(strip_tags($content), 0, 150));
    $xoopsTpl->assign('xoops_pagetitle', $title);

    $now_uid = $xoopsUser ? $xoopsUser->uid() : 0;
    $xoopsTpl->assign('now_uid', $now_uid);

    $TadUpFilesMp4 = new TadUpFiles("tad_book3", "/$tbsn/$uid");
    $TadUpFilesMp4->set_col('mp4', $tbdsn);
    $mp4_path = $TadUpFilesMp4->get_pic_file('file', 'url', '', true);
    if ($mp4_path) {
        $TadUpFilesPic = new TadUpFiles("tad_book3", "/$tbsn/$uid");
        $TadUpFilesPic->set_col('pic', $tbdsn);
        $video_thumb = $TadUpFilesPic->get_pic_file('images', 'url');

        if (empty($video_thumb)) {
            $video_thumb = XOOPS_URL . "/uploads/tad_book3/{$tbsn}/{$uid}/image/{$tbdsn}.jpg";
        }
        $xoopsTpl->assign('video_thumb', $video_thumb);
        $VideoJs = new VideoJs('tad_book3_video', $mp4_path, $video_thumb, '', false, false);

        // 找出字幕
        $TadUpFilesVtt = new TadUpFiles("tad_book3", "/$tbsn/$uid");
        $TadUpFilesVtt->set_col('vtt', $tbdsn);
        $video_vtt = $TadUpFilesVtt->get_pic_file('file', 'url', '', true);

        if ($video_vtt) {
            $VideoJs->set_var('vtt', $video_vtt);
        }
        $length['col_name'] = $tbsn;
        $length['col_sn'] = $tbdsn;
        $log['col_name'] = $now_uid;
        $log['col_sn'] = $tbdsn;

        $start_from = get_video_start($tbdsn);
        $VideoJs->set_var('start', $start_from);
        $player = $VideoJs->render('tad_book3', XOOPS_URL . "/modules/tad_book3/ajax_file.php", $length, $log);
    } else {
        $player = '';
    }
    $xoopsTpl->assign('player', $player);

}

//更新頁面計數器
function add_counter($tbdsn = '')
{
    global $xoopsDB;
    $sql = 'update ' . $xoopsDB->prefix('tad_book3_docs') . " set  `count` = `count`+1 where tbdsn='$tbdsn'";
    $xoopsDB->queryF($sql) or Utility::web_error($sql, __FILE__, __LINE__);
}

//觀看紀錄
function view_log($tbsn = '')
{
    global $xoopsTpl;

    $book = get_tad_book3($tbsn);
    $xoopsTpl->assign('book', $book);

    if (!chk_power($book['read_group'], $read_group)) {
        redirect_header('index.php', 3, _MD_TADBOOK3_CANT_READ);
    } else {
        $now = time();
        $start_ts = get_start_ts($tbdsn, 'read', $read_group);
        if ($start_ts && $start_ts > $now) {
            $start_time = date('Y-m-d H:i:s', $start_ts);
            redirect_header('index.php', 3, sprintf(_MD_TADBOOK3_READ_DATE, $start_time));
        }
    }

    // 找出可閱讀群組及使用者
    $video_group_arr = explode(',', $book['video_group']);
    $member_handler = xoops_gethandler('member');
    $group_handler = xoops_getHandler('group');
    $group_users = $logs = [];
    foreach ($video_group_arr as $group_id) {
        $group = $group_handler->get($group_id);
        $group_name = $group->name();
        $users_uid = $member_handler->getUsersByGroup($group_id);
        foreach ($users_uid as $uid) {
            $group_users[$group_name][$uid]['name'] = $member_handler->getUser($uid)->name();
            $group_users[$group_name][$uid]['log'] = $logs[$group_name][$uid] = get_user_logs($tbsn, $uid);
        }

    }
    $xoopsTpl->assign('group_users', $group_users);

    // 紀錄哪個單元被誰讀過
    $tbdsn_log = $uids_times = [];
    foreach ($logs as $group_name => $group_uids) {
        foreach ($group_uids as $uid => $log) {
            foreach ($log as $tbdsn => $time) {
                $tbdsn_log[$tbdsn][$group_name][$uid] = $time;
                $uids_times[$uid] += (int) $time;
            }
        }
    }

    // 找出所本書所有單元
    $category_log = [];
    list($docs, $total_time, $total_view) = get_docs($tbsn, true);
    $xoopsTpl->assign('docs', $docs);
    $level = $count1 = $count2 = [];
    foreach ($docs as $doc) {
        $tbdsn = $doc['tbdsn'];
        $category = $doc['category'];
        $page = $doc['page'];
        $paragraph = $doc['paragraph'];
        $sort = $doc['sort'];
        $level[$category][$page][$paragraph][$sort] = $doc;

        if ($tbdsn_log[$tbdsn]) {
            foreach ($tbdsn_log[$tbdsn] as $group_name => $group_uids) {
                foreach ($group_uids as $uid => $time) {
                    $category_log[$group_name][$category][$uid] = $time;
                }
            }
        }

        $count1[$category]++;
        $count2[$category][$page]++;
    }

    rsort($uids_times);

    $xoopsTpl->assign('level', $level);
    $xoopsTpl->assign('count1', $count1);
    $xoopsTpl->assign('count2', $count2);
    $xoopsTpl->assign('category_log', $category_log);
    $xoopsTpl->assign('category_log', $category_log);

    if ($_GET['test']) {
        Utility::dd($category_log);
    }

}

/*-----------執行動作判斷區----------*/
$op = Request::getString('op');
$tbsn = Request::getInt('tbsn');
$tbdsn = Request::getInt('tbdsn');

switch ($op) {
    case 'check_passwd':
        check_passwd($tbsn);
        break;

    case 'view_log':
        view_log($tbsn);
        break;

    default:
        view_page($tbdsn);
        $op = 'view_page';
        break;
}

/*-----------秀出結果區--------------*/

$xoopsTpl->assign('toolbar', Utility::toolbar_bootstrap($interface_menu));
$xoopsTpl->assign("now_op", $op);
$xoTheme->addStylesheet(XOOPS_URL . '/modules/tad_book3/css/reset.css');
$xoTheme->addStylesheet(XOOPS_URL . '/modules/tad_book3/css/module.css');
require_once XOOPS_ROOT_PATH . '/footer.php';
