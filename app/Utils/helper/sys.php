<?php

if (!function_exists('unset_limit')) {
    /**
     * 取消限制
     *
     * @param integer $limit_second
     */
    function unset_limit($limit_second = 0)
    {
        set_time_limit($limit_second);
        ini_set('memory_limit', -1);
        ini_set("max_execution_time", 0);
    }
}

if (!function_exists('is_mobile')) {
    /**
     * 是否移动端
     *
     * @return boolean
     */
    function is_mobile()
    {
        if (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap")) {
            return true;
        } elseif (isset($_SERVER['HTTP_ACCEPT']) && strpos(strtoupper($_SERVER['HTTP_ACCEPT']), "VND.WAP.WML")) {
            return true;
        } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            return true;
        } elseif (isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
        return false;
    }
}

if (!function_exists('get_ip')) {
    function get_ip()
    {
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            return $_SERVER["HTTP_CLIENT_IP"];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            return $ips[0];
        }
        return $_SERVER['REMOTE_ADDR'];
    }
}

if (!function_exists('traverse_directory')) {
    /**
     * 遍历文件夹
     *
     * @param string $directory
     * @return array
     */
    function traverse_directory($directory): array
    {
        $file_paths = [];
        $items = scandir($directory);

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $directory . '/' . $item;
            if (is_file($path)) {
                $file_paths[] = $path;
            } elseif (is_dir($path)) {
                $file_paths = array_merge($file_paths, traverse_directory($path));
            }
        }

        return $file_paths;
    }
}
