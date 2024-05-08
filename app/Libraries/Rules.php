<?php

namespace App\Libraries;

class Rules
{
    protected static $errors = [];

    public static function getErrors()
    {
        return self::$errors;
    }

    public static function phone($phone): bool
    {
        self::$errors = [];

        if (strlen($phone) != 11) {
            self::$errors = ['长度错误'];
            return false;
        }
        if (!is_numeric($phone)) {
            self::$errors = ['需要为纯数字'];
            return false;
        }

        return preg_match('#^(1[3-9])\\d{9}$#', $phone) ? true : false;
    }

    public static function idcard($idcard): bool
    {
        self::$errors = [];

        // [1-9]\d{5}                                   1~6                  地区,第一位不能为0
        // \d{2}                                        7~8                  出生年份后两位00-99
        // ((0[1-9])|(10|11|12))                        9~10                 月份，01-12月
        // (([0-2][1-9])|10|20|30|31)                   11~12                日期，01-31天
        // \d{3}                                        13~15                顺序码三位
        $regular15 = '/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$/';

        // [1-9]\d{5}                               1~6             地区,第一位不能为0
        // (18|19|([23]\d))\d{2}                    7~10            出身年份，覆盖范围为 1800-3999 年
        // ((0[1-9])|(10|11|12))                    10~13           月份，01-12月
        // (([0-2][1-9])|10|20|30|31)               14~15           日期，01-31天
        // \d{3}[0-9xX]                             16~18           顺序码三位 + 一位校验码(Xx)
        $regular18 = '/^[1-9]\d{5}(18|19|([23]\d))\d{2}((0[1-9])|(10|11|12))(([0-2][1-9])|10|20|30|31)\d{3}[0-9xX]$/';

        return preg_match($regular15, $idcard) || preg_match($regular18, $idcard);
    }

    public static function password($password): bool
    {
        // 密码至少包含：数字,英文,字符中的两种以上，长度8-20
        $regular = '/^(?![0-9]+$)(?![a-z]+$)(?![A-Z]+$)(?!([^(0-9a-zA-Z)])+$).{8,20}$/';

        return preg_match($regular, $password);
    }

}
