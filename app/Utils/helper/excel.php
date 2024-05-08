<?php

if (!function_exists('export_csv')) {
    /**
     * csv 下载/生成
     * @param array $data 需要填入表格的二维数组
     * @param string $file_name 文件名，不需加csv后缀
     * @param string $file_dir 文件夹，不填则下载到浏览器
     *
     * @return string|void
     */
    function export_csv($data, $file_name, $file_dir = '')
    {
        $file_name .= '.csv';

        @ob_end_clean();
        ob_start();
        if (!$file_dir) {
            @header("Content-Type: text/csv");
            @header("Content-Disposition:filename=$file_name");
            $fp = fopen('php://output', 'w');
        } else {
            if (!is_dir($file_dir)) {
                mkdir($file_dir, 0777, true);
            }
            $fp = fopen($file_dir . $file_name, 'w');
        }
        fwrite($fp, chr(0xEF) . chr(0xBB) . chr(0xBF)); //转码 防止乱码
        $index = 0;
        foreach ($data as $item) {
            if (++$index % 1000 == 0) {
                ob_flush();
                flush();
            }
            fputcsv($fp, (array) $item);
        }

        ob_flush();
        flush();
        @ob_end_clean();
        fclose($fp);

        if (!$file_dir) {
            exit;
        }

        return $file_dir . $file_name;
    }
}
