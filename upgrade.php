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

if (file_exists(WB_PATH.'/modules/droplets_extension/class.extension.php'))
  @unlink(WB_PATH.'/modules/droplets_extension/class.extension.php');

if (file_exists(WB_PATH.'/modules/droplets_extension/class.pages.php'))
  @unlink(WB_PATH.'/modules/droplets_extension/class.pages.php');

/**
 * Check if the specified $field in table mod_droplets_extension exists
 *
 * @param string $field
 * @return boolean
 */
function fieldExists($field) {
  global $database;
  global $admin;

  if (null === ($query = $database->query("DESCRIBE `".TABLE_PREFIX."mod_droplets_extension`")))
    $admin->print_error($database->get_error());
  while (false !== ($data = $query->fetchRow(MYSQL_ASSOC)))
    if ($data['Field'] == $field) return true;
  return false;
} // sqlFieldExists()

if (!fieldExists('drop_topics_array')) {
  // add field drop_topics_array
  $SQL = "ALTER TABLE `".TABLE_PREFIX."mod_droplets_extension` ADD `drop_topics_array` VARCHAR(255) NOT NULL DEFAULT '' AFTER `drop_file`";
  if (!$database->query($SQL))
    $admin->print_error($database->get_error());
}
