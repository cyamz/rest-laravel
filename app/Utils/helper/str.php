<?php

if (!function_exists('str_filter')) {
    /**
     * 字符串过滤
     */
    function str_filter($str)
    {
        $special_str = ['`', '·', '~', '!', '！', '@', '#', '$', '￥', '%', '^', '…', '&', '*', '(', ')', '（', '）', '-', '_', '—', '+',
            '=', '|', '[', ']', '【', '】', '{', '}', ';', '；', ':', '：', '"', '“', '”', ',', '，', '<', '>', '《', '》', '.', '。', '/', '、',
            '?', '？', '’', '"', '“', '”', ',', '，', '<', '>', '《', '》', '.', '。', '/', '、', '?', '？', '’', '\\', '\'', "\r", "\n",
        ];
        for ($i = 127; $i < 256; $i++) {
            $special_str[] = chr($i);
        }
        return trim(str_replace($special_str, '', $str));
    }
}

if (!function_exists('id_hash')) {
    /**
     * 将ID拆分为文件夹路径
     *
     * @param integer $id
     * @param integer $length 往左补零长度
     * @param integer $slice 划分片数
     * @param integer $per_length 每片长度
     * @return string
     */
    function id_hash($id, $length = 8, $slice = 4, $per_length = 2): string
    {
        $id = str_pad($id, $length, '0', STR_PAD_LEFT);

        $arr = [];
        for ($i = 0; $i < $slice; $i++) {
            $num = substr($id, 0 - $per_length);
            array_unshift($arr, $num);
            $length -= $per_length;
            if ($length <= 0) {
                break;
            }
            $id = substr($id, 0, $length);
        }

        return implode('/', $arr);
    }
}

if (!function_exists('comma2array')) {
    /**
     * 逗号字符串转数组
     *
     * @param string $comma_string
     * @return array
     */
    function comma2array($comma_string, $trim = false, $dont_allow_empty = false, $filter = false)
    {
        $comma_string = str_replace('，', ',', $comma_string);

        $arr = explode(',', $comma_string);

        if ($trim || $dont_allow_empty || $filter) {
            foreach ($arr as $index => &$item) {
                if ($trim) {
                    $item = trim($item);
                }
                if ($dont_allow_empty) {
                    if ($item == '') {
                        unset($arr[$index]);
                        continue;
                    }
                }
                if ($filter) {
                    $item = str_filter($item);
                }
            }
        }
        
        return $arr;
    }
}
