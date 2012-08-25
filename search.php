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

require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/interface.php');


function droplets_extension_search($search_params) {
  global $database;

  $SQL = "SELECT * FROM `".TABLE_PREFIX."mod_droplets_extension` WHERE `drop_type`='search'";
  if (null == ($query = $database->query($SQL))) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
    return false;
  }

  $result = false;
  while (false !== ($droplet = $query->fetchRow(MYSQL_ASSOC))) {
    if (droplet_exists($droplet['drop_droplet_name'], $droplet['drop_page_id'])) {
      if (file_exists(WB_PATH.'/modules/'.$droplet['drop_module_dir'].'/droplet.extension.php')) {
        include(WB_PATH.'/modules/'.$droplet['drop_module_dir'].'/droplet.extension.php');
        $user_func = $droplet['drop_module_dir'].'_droplet_search';
        if (function_exists($user_func)) {
          $page_url = '';
          $page_url = getURLbyPageID($droplet['drop_page_id']);
          $search_result = call_user_func($user_func, $droplet['drop_page_id'], $page_url);
          if (is_array($search_result)) {
            foreach ($search_result as $search) {
              $url = isset($search['url']) ? $search['url'] : '';
              $mod_vars = array(
                  'page_link' => $url,
                  'page_link_target' => isset($search['params']) && !empty($search['params']) ? sprintf('%s%s', (strpos($url, '?') === false) ? '?' : '&', $search['params']) : '',
                  'page_title' => isset($search['title']) ? $search['title'] : $search_params['page_title'],
                  'page_description' => isset($search['description']) ? $search['description'] : '',
                  'page_modified_when' => isset($search['modified_when']) ? $search['modified_when'] : '',
                  'page_modified_by' => isset($search['modified_by']) ? $search['modified_by'] : '',
                  'text' => isset($search['text']) ? $search['text'] : '');
              if (print_excerpt2($mod_vars, $search_params)) {
                $result = true;
              }
            }
          }
        }
      }
    }
    else {
      unregister_droplet_search($droplet['drop_droplet_name'], $search_params['page_id']);
    }
  }
  return $result;
} // droplet_extension_search()

?>