<?php

/**
 * DropletsExtension
 *
 * @author Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @link http://phpmanufaktur.de
 * @copyright 2011 - 2012
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License (GPL)
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

$module_directory = 'droplets_extension';
$module_name = 'dropletsExtension';
$module_function = 'page';
$module_version = '0.19';
$module_status = 'Stable';
$module_platform = '2.8';
$module_author = 'Ralf Hertsch, Berlin (Germany)';
$module_license = 'GNU General Public License';
$module_description = 'dropletsExtension - integrate droplets into the LEPTON CMS search function and more';
$module_home = 'https://addons.phpmanufaktur.de';
$module_guid = '2F1CCDD0-A922-4DDA-BDA8-FB0624F6C1FE';