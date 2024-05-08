<?php

if (!function_exists('get_date_group')) {
    function get_date_group($start_day, $end_day)
    {
        $group = [];
        $date = $start_day;
        while($date <= $end_day) {
            $group[] = $date;
            $date = date('Y-m-d', strtotime($date . ' +1 days'));
        }

        return $group;
    }
}

/**
 * 时间友好显示
 * @param int $time 时间戳
 * @return string   友好时间字符串
 * @author  zhouzy
 */
if (!function_exists('get_friendly_time')) {
    function get_friendly_time($time)
    {
        //time=时间戳,timeCurr=当前时间,timeDiff=时间差,dayDiff=天数差,yearDiff=年数差
        if (!$time) {
            return '';
        }
        $timeCurr = time();
        $timeDiff = $timeCurr - $time;
        $dayDiff = intval(date("z", $timeCurr)) - intval(date("z", $time));
        $yearDiff = intval(date("Y", $timeCurr)) - intval(date("Y", $time));
        if ($timeDiff < 60) {
            if ($timeDiff < 10) {
                return '刚刚';
            } else {
                return floor($timeDiff) . "秒前";
            }
        } elseif ($timeDiff < 3600) { //1小时内
            return floor($timeDiff / 60) . "分钟前";
        } elseif ($yearDiff == 0 && $dayDiff == 0) { //今天内
            return '今天 ' . date('H:i', $time);
        } elseif ($yearDiff == 0) { //今年内
            return date("m月d日 H:i", $time);
        } else { //正常显示
            return date("Y-m-d H:i", $time);
        }
    }
}