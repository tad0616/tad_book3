<?php
function tadbook3_search($queryarray, $andor, $limit, $offset, $userid)
{
    global $xoopsDB;
    $myts = \MyTextSanitizer::getInstance();
    if (is_array($queryarray)) {
        foreach ($queryarray as $k => $v) {
            $arr[$k] = $xoopsDB->escape($v);
        }
        $queryarray = $arr;
    } else {
        $queryarray = [];
    }

    $sql = 'SELECT tbsn,tbdsn,title,last_modify_date,uid FROM ' . $xoopsDB->prefix('tad_book3_docs') . " WHERE enable='1'";
    if (0 != $userid) {
        $sql .= ' AND uid=' . $userid . ' ';
    }
    if (is_array($queryarray) && $count = count($queryarray)) {
        $sql .= " AND ((title LIKE '%$queryarray[0]%' OR content LIKE '%$queryarray[0]%')";
        for ($i = 1; $i < $count; $i++) {
            $sql .= " $andor ";
            $sql .= "( title LIKE '%$queryarray[$i]%' OR content LIKE '%$queryarray[$i]%')";
        }
        $sql .= ') ';
    }
    $sql .= 'ORDER BY last_modify_date DESC';
    $result = $xoopsDB->query($sql, $limit, $offset);
    $ret = [];
    $i = 0;
    while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
        $ret[$i]['image'] = 'images/copy.png';
        $ret[$i]['link'] = 'page.php?tbsn=' . $myrow['tbsn'] . '&tbdsn=' . $myrow['tbdsn'];
        $ret[$i]['title'] = $myrow['title'];
        $last_modify_date = xoops_getUserTimestamp($myrow['last_modify_date']);
        $ret[$i]['time'] = $last_modify_date;
        $ret[$i]['uid'] = $myrow['uid'];
        $i++;
    }

    return $ret;
}
