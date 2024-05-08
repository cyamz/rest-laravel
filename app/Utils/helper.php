<?php

/**
 * db
 * @method null begin()
 * @method null commit()
 * @method null rollback()
 * execel
 * @method null export_csv($data, $filename, $file_dir = '')
 * str
 * @method string str_filter($str)
 * @method string id_hash()
 * sys
 * @method null unset_limit()
 * @method bool is_mobile()
 * user_ids
 * @method int user_id()
 * @method int admin_id()
 * @method int school_id()
 * @method int student_id()
 * date
 * @method array get_date_group($start_day, $end_day)
 * @method string get_friendly_time()
 */

require_once('helper/db.php');
require_once('helper/excel.php');
require_once('helper/str.php');
require_once('helper/sys.php');
require_once('helper/date.php');
