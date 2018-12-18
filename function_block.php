<?php

//判斷本文是否允許該用戶之所屬群組觀看
if (!function_exists('chk_power')) {
    function chk_power($enable_group = "")
    {
        global $xoopsDB, $xoopsUser;
        if (empty($enable_group)) {
            return true;
        }

        //取得目前使用者的所屬群組
        if ($xoopsUser) {
            $User_Groups = $xoopsUser->getGroups();
        } else {
            $User_Groups = array();
        }

        $news_enable_group = explode(",", $enable_group);
        foreach ($User_Groups as $gid) {
            if (in_array($gid, $news_enable_group)) {
                return true;
            }
        }
        return false;
    }
}
