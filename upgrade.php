<?php

/**
 * dropletsExtension
 *
 * @author Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @link http://phpmanufaktur.de
 * @copyright 2011-2012
 * @license http://www.gnu.org/licenses/gpl.html GNU Public License
 * @version $Id$
 *
 * FOR VERSION- AND RELEASE NOTES PLEASE LOOK AT INFO.TXT!
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

require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/class.extension.php');

global $admin;

$error = '';

$dbDropletsExt = new dbDropletsExtension();

if ($dbDropletsExt->sqlFieldExists('drop_css_file')) {
  if (!$dbDropletsExt->sqlAlterTableChangeField('drop_css_file', dbDropletsExtension::field_file, "VARCHAR(255) NOT NULL DEFAULT ''")) {
    $error .= sprintf('[UPGRADE] %s', $dbDropletsExt->getError());
  }
}

// Prompt Errors
if (!empty($error)) {
  $admin->print_error($error);
}