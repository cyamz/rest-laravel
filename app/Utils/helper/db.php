<?php

use Illuminate\Support\Facades\DB;

/** 数据库事务 Fixme: app绑定DBTransaction::class */
if (!function_exists('begin')) {
    function begin()
    {
        DB::beginTransaction();
    }
}
if (!function_exists('commit')) {
    function commit()
    {
        DB::commit();
    }
}
if (!function_exists('rollback')) {
    function rollback()
    {
        DB::rollBack();
    }
}