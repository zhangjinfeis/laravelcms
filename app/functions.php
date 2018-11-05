<?php

/**
 * 将数组按指定顺序排列
 * @author zjf ${date}
 * @param $target  目标数组
 * @param $sort_array  参照数组
 * @param $colume  目标数组的列
 * @return array
 */
function array_sortby_array($target = [],$sort_array = [],$colume = 'id'){
    $arr = [];
    foreach($sort_array as $key => $val){
        foreach($target as $k => $v){
            if($target[$k][$colume] == $val){
                array_push($arr,$v);
            }
        }
    }
    return $arr;
}

/**
 * 文件大小转换
 * @param unknown_type $size
 * @return 文件大小  如 44KB
 */
function format_bytes($size) {
    $units = array('B', 'KB', 'MB', 'G', 'T');
    for ($i = 0; $size >= 1024 && $i < 4; $i++) $size /= 1024;
    return round($size, 2).$units[$i];
}


/**
 * 将秒转换成时分秒
 * @author zjf ${date}
 * @param $s
 * @return string
 */
function his_format($s){
    //计算天数
    $days = intval($s/86400);
    //计算小时数
    $remain = $s%86400;
    $hours = intval($remain/3600);
    //计算分钟数
    $remain = $remain%3600;
    $mins = intval($remain/60);
    //计算秒数
    $secs = $remain%60;
    return ($hours?$hours.'小时':'').($mins?$mins.'分':'').($secs?$secs.'秒':'');
}

/**
 * 加密数组(用在跳转返回原页面)
 * @param $arr
 * @return string
 * Created by zjf
 * Time: 2018/11/1 15:48
 */
function encrypt_arr($arr){
    $arr = json_encode($arr);
    return encrypt($arr);
}

/**
 * 解密数组(用在跳转返回原页面)
 * @param $str
 * @return mixed
 * Created by zjf
 * Time: 2018/11/1 15:48
 */
function decrypt_arr($str){
    $str = decrypt($str);
    return json_decode($str,true);
}


/**
 * 将资源转化成另外命名，用于生成多规格图片
 * @param unknown_type $path  资源路径
 * @param unknown_type $fix  添加的后缀_mid _min
 */
function img_rename($path, $fix) {
    if($path){
        preg_match('/(.*)\.(.*)/', $path ,$a);
        return $a[1].$fix.'.'.$a[2];
    }else{
        return '';
    }
}
