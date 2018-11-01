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
 * 加密数组
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
 * 解密数组
 * @param $str
 * @return mixed
 * Created by zjf
 * Time: 2018/11/1 15:48
 */
function decrypt_arr($str){
    $str = decrypt($str);
    return json_decode($str,true);
}
