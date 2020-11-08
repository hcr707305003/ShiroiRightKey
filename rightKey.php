<?php

$filename = __DIR__.'/rightKey.json';
if(isPost()) {
    $tableData = array_filter(tdToArray($_POST['element']));
    foreach ($tableData as $k=>$v) $tableData[$k] = array_splice($v,0,2);
    file_put_contents($filename,json_encode(array_values($tableData)));
} else{
    echo file_get_contents($filename);
}


function tdToArray($table) {
    $table = preg_replace("'<table[^>]*?>'si","",$table);
    $table = preg_replace("'<tr[^>]*?>'si","",$table);
    $table = preg_replace("'<td[^>]*?>'si","",$table);
    $table = str_replace("</tr>","{tr}",$table);
    $table = str_replace("</td>","{td}",$table);
    //去掉 HTML 标记
    $table = preg_replace("'<[/!]*?[^<>]*?>'si","",$table);
    //去掉空白字符
    $table = preg_replace("'([rn])[s]+'","",$table);
    $table = str_replace(" ","",$table);
    $table = str_replace(" ","",$table);
    $table = str_replace("\n","",$table);
    $table = explode('{tr}', $table);
    array_pop($table);
    foreach ($table as $key=>$tr) {
        // 自己可添加对应的替换
        $td = explode('{td}', $tr);
        array_pop($td);
        $td_array[] = $td;
    }
    return $td_array;
}

function isPost() {
    return $_SERVER['REQUEST_METHOD'] == 'POST' ? true : false;
}