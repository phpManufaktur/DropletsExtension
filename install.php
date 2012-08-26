<?php

/**
 * DropletsExtension
 *
 * @author Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @link http://phpmanufaktur.de
 * @copyright 2011 - 2012
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

// include class.secure.php to protect this file and the whole CMS!
if (defined('WB_PATH')) {
  if (defined('LEPTON_VERSION'))
    include(WB_PATH.'/framework/class.secure.php');
}
else {
  $oneback = "../";
  $root = $oneback;
  $level = 1;
  while (($level < 10) && (!file_exists($root.'/framework/class.secure.php'))) {
    $root .= $oneback;
    $level += 1;
  }
  if (file_exists($root.'/framework/class.secure.php')) {
    include($root.'/framework/class.secure.php');
  }
  else {
    trigger_error(sprintf("[ <b>%s</b> ] Can't include class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
  }
}
// end include class.secure.php

global $admin;
global $database;

// create the regular WYSIWYG table without any changes
$SQL = "CREATE TABLE IF NOT EXISTS `".TABLE_PREFIX."mod_droplets_extension` ( ".
    "`drop_id` INT(11) NOT NULL AUTO_INCREMENT, ".
    "`drop_droplet_name` VARCHAR(255) NOT NULL DEFAULT '', ".
    "`drop_page_id` INT(11) NOT NULL DEFAULT '-1', ".
    "`drop_module_dir` VARCHAR(255) NOT NULL DEFAULT '', ".
    "`drop_type` ENUM('css','search','header','javascript','undefined') NOT NULL DEFAULT 'undefined', ".
    "`drop_file` VARCHAR(255) NOT NULL DEFAULT '', ".
    "`drop_topics_array` VARCHAR(255) NOT NULL DEFAULT '', ".
    "`drop_timestamp` TIMESTAMP, ".
    "PRIMARY KEY (`drop_id`), ".
    "KEY (`drop_droplet_name`, `drop_page_id`) ".
    ") ENGINE=MyIsam AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci";

if (!$database->query($SQL))
  $admin->print_error($database->get_error());
