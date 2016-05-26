<?php
function tadbook3_search($queryarray, $andor, $limit, $offset, $userid)
{
    global $xoopsDB;
    //處理許功蓋
    if (get_magic_quotes_gpc()) {
        if (is_array($queryarray)) {
            foreach ($queryarray as $k => $v) {
                $arr[$k] = addslashes($v);
            }
            $queryarray = $arr;
        } else {
            $queryarray = array();
        }
    }
    $sql = "SELECT tbdsn,title,last_modify_date,uid FROM " . $xoopsDB->prefix("tad_book3_docs") . " WHERE enable='1'";
    if ($userid != 0) {
        $sql .= " AND uid=" . $userid . " ";
    }
    if (is_array($queryarray) && $count = count($queryarray)) {
        $sql .= " AND ((title LIKE '%$queryarray[0]%' OR content LIKE '%$queryarray[0]%')";
        for ($i = 1; $i < $count; $i++) {
            $sql .= " $andor ";
            $sql .= "( title LIKE '%$queryarray[$i]%' OR content LIKE '%$queryarray[$i]%')";
        }
        $sql .= ") ";
    }
    $sql .= "ORDER BY last_modify_date DESC";
    $result = $xoopsDB->query($sql, $limit, $offset);
    $ret    = array();
    $i      = 0;
    while ($myrow = $xoopsDB->fetchArray($result)) {
        $ret[$i]['image'] = "images/copy.png";
        $ret[$i]['link']  = "page.php?tbdsn=" . $myrow['tbdsn'];
        $ret[$i]['title'] = $myrow['title'];
        $last_modify_date = xoops_getUserTimestamp($myrow['last_modify_date']);
        $ret[$i]['time']  = $last_modify_date;
        $ret[$i]['uid']   = $myrow['uid'];
        $i++;
    }
    return $ret;
}
