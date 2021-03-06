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

/**
 * Ueberprueft ob das angegebene Droplet registriert ist
 *
 * @param string $droplet_name
 * @param string $register_type
 * @param integer $page_id - die PAGE_ID fuer die das Droplet registriert ist
 * @return boolean
 */
function is_registered_droplet($droplet_name, $register_type, $page_id) {
  global $database;

  // is it a TOPICS article?
  $topic_id = (defined('TOPIC_ID')) ? TOPIC_ID : -1;

  if (($topic_id > 0)) {
    $SQL = "SELECT `drop_topics_array` FROM `".TABLE_PREFIX."mod_droplets_extension` WHERE ".
        "`drop_droplet_name`='$droplet_name' AND `drop_type`='$register_type' AND ".
        "`drop_page_id`='$page_id'";
    $check = $database->get_one($SQL, MYSQL_ASSOC);
    if ($database->is_error()) {
      trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
      return false;
    }
    $arr = explode(',', $check);
    $result = (in_array($topic_id, $arr));
  }
  else {
    // regular check
    $SQL = "SELECT `drop_page_id` FROM `".TABLE_PREFIX."mod_droplets_extension` WHERE ".
      "`drop_droplet_name`='$droplet_name' AND `drop_type`='$register_type' AND ".
      "`drop_page_id`='$page_id'";
    $check = $database->get_one($SQL, MYSQL_ASSOC);
    if ($database->is_error()) {
      trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
      return false;
    }
    $result = ($check == $page_id) ? true : false;
  }
  if ($result)
    check_droplet_search_section($page_id);
  return $result;
} // is_registered_droplet()

/**
 * Ueberprueft ob das angegebene Droplet fuer die Droplet Suche registriert ist
 *
 * @param STR $droplet_name
 * @param INT REFRENCE $page_id - die PAGE_ID fuer die das Droplet registriert ist
 * @return BOOL
 */
function is_registered_droplet_search($droplet_name, $page_id) {
  return is_registered_droplet($droplet_name, 'search', $page_id);
} // is_registered_droplet_search()

/**
 * Ueberprueft ob das das angegebene Droplet fuer den Template Header registriert ist
 *
 * @param STR $droplet_name
 * @param INT REFERENCE $page_id
 * @return BOOL
 */
function is_registered_droplet_header($droplet_name, $page_id) {
  return is_registered_droplet($droplet_name, 'header', $page_id);
} // is_registered_droplet_header()

/**
 * Ueberpruefr ob fuer das angegebene Droplet CSS laden registriert ist
 *
 * @param STR $droplet_name
 * @param INT $page_id
 * @return BOOL
 */
function is_registered_droplet_css($droplet_name, $page_id) {
  return is_registered_droplet($droplet_name, 'css', $page_id);
} // is_registered_droplet_css()

/**
 * Ueberprueft ob fur das angegebene Droplet JavaSCript registriert ist
 *
 * @param STR $droplet_name
 * @param INT $page_id
 * @return BOOL
 */
function is_registered_droplet_js($droplet_name, $page_id) {
  return is_registered_droplet($droplet_name, 'javascript', $page_id);
} // is_registered_droplet_js()

/**
 * Registriert das angegebene Droplet
 *
 * @param string $droplet_name the name of the droplet
 * @param integer $page_id the PAGE_ID where this droplet is in use
 * @param string $module_directory the directory where the droplets.extension.php file of the module is hosted
 * @param string $file the name of the CSS or JS file
 * @param integer $topic_id the ID of the assigned TOPICS article (optional)
 * @return boolean
 */
function register_droplet($droplet_name, $page_id, $module_directory, $register_type, $file = '') {
  global $database;

  // is it a TOPICS article?
  $topic_id = (defined('TOPIC_ID')) ? TOPIC_ID : -1;

  // check if a droplet search section exists
  if ($register_type == 'search')
    check_droplet_search_section($page_id);
  // clear the droplet name
  $droplet_name = clear_droplet_name($droplet_name);
  // nothing to do if the droplet does not exists
  if (!droplet_exists($droplet_name, $page_id))
    return false;
  // nothing to do if the droplet is already registered
  if (is_registered_droplet($droplet_name, $register_type, $page_id, $topic_id))
    return true;
  // clear the module directory
  $module_directory = clear_module_directory($module_directory);

  // register the droplet
  if (($topic_id > 1)) {
    // this is possible an update - check first
    $SQL = "SELECT `drop_id`,`drop_topics_array` FROM `".TABLE_PREFIX."mod_droplets_extension` WHERE `drop_type`='$register_type' AND `drop_page_id`='$page_id' AND `drop_droplet_name`='$droplet_name'";
    if (null == ($query = $database->query($SQL))) {
      trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
      return false;
    }
    if ($query->numRows() > 0) {
      // this is an update!
      $droplet = $query->fetchRow(MYSQL_ASSOC);
      $arr = explode(',', $droplet['drop_topics_array']);
      if (is_array($arr)) {
        $arr[] = $topic_id;
        $topics_str = implode(',', $arr);
      }
      else
        $topics_str = $topic_id;
      $SQL = "UPDATE `".TABLE_PREFIX."mod_droplets_extension` SET `drop_topics_array`='$topics_str' WHERE `drop_id`='{$droplet['drop_id']}'";
      if (!$database->query($SQL)) {
        trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
        return false;
      }
    }
    else {
      // insert a new record
      $SQL = "INSERT INTO `".TABLE_PREFIX."mod_droplets_extension` (`drop_droplet_name`,".
          "`drop_page_id`,`drop_module_dir`,`drop_type`,`drop_file`,`drop_topics_array`) VALUES ".
          "('$droplet_name','$page_id','$module_directory','$register_type','$file','$topic_id')";
      if (!$database->query($SQL)) {
        trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
        return false;
      }
    }
  }
  else {
    $SQL = "INSERT INTO `".TABLE_PREFIX."mod_droplets_extension` (`drop_droplet_name`,".
      "`drop_page_id`,`drop_module_dir`,`drop_type`,`drop_file`) VALUES ".
      "('$droplet_name','$page_id','$module_directory','$register_type','$file')";
    if (!$database->query($SQL)) {
      trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
      return false;
    }
  }
  return true;
} // register_droplet()

/**
 * Registriert das angegegebene Droplet fuer die Suche
 *
 * @param STR $droplet_name
 * @param STR $page_id
 * @param STR $module_directory
 * @return BOOL
 */
function register_droplet_search($droplet_name, $page_id, $module_directory) {
  return register_droplet($droplet_name, $page_id, $module_directory, 'search');
} // register_droplet_search()

/**
 * Registriert das angegebene Droplet fuer den Template Header
 *
 * @param STR $droplet_name
 * @param STR $page_id
 * @param STR $module_directory
 * @return BOOL
 */
function register_droplet_header($droplet_name, $page_id, $module_directory) {
  return register_droplet($droplet_name, $page_id, $module_directory, 'header');
} // register_droplet_header()

/**
 * Registriert eine CSS Datei fuer das angegebene Droplet
 *
 * @param STR $droplet_name
 * @param STR $page_id
 * @param STR $module_directory
 * @param STR $file
 * @return BOOL
 */
function register_droplet_css($droplet_name, $page_id, $module_directory, $file) {
  return register_droplet($droplet_name, $page_id, $module_directory, 'css', $file);
} // register_droplet_css()

/**
 * Registriert eine JavaScript Datei fuer das angegebene Droplet
 *
 * @param STR $droplet_name
 * @param STR $page_id
 * @param STR $module_directory
 * @param STR $file
 * @return BOOL
 */
function register_droplet_js($droplet_name, $page_id, $module_directory, $file) {
  return register_droplet($droplet_name, $page_id, $module_directory, 'javascript', $file);
} // register_droplet_js()

/**
 * Entfernt das angegebene Droplet
 *
 * @param STR $droplet_name
 * @return BOOL
 */
function unregister_droplet($droplet_name, $register_type, $page_id) {
  global $database;

  // clear Droplet name
  $droplet_name = clear_droplet_name($droplet_name);

  // is it a TOPICS article?
  $topic_id = (defined('TOPIC_ID')) ? TOPIC_ID : -1;

  if (($topic_id > 0)) {
    $SQL = "SELECT `drop_id`, `drop_topics_array` FROM `".TABLE_PREFIX."mod_droplets_extension` WHERE ".
        "`drop_droplet_name`='$droplet_name' AND `drop_type`='$register_type' AND `drop_page_id`='$page_id'";
    if (null == ($query = $database->query($SQL))) {
      trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
      return false;
    }
    if ($query->numRows() > 0) {
      $droplet = $query->fetchRow(MYSQL_ASSOC);
      $topics_array = explode(',', $droplet['drop_topics_array']);
      if (count($topics_array) > 1) {
        // we have more than one entry - remove the topic id and update the record
        unset($topics_array[array_search($topic_id, $topics_array)]);
        $topics_str = implode(',', $topics_array);
        $SQL = "UPDATE `".TABLE_PREFIX."mod_droplets_extension` SET `drop_topics_array`='$topics_str' WHERE `drop_id`='{$droplet['drop_id']}'";
        if (!$database->query($SQL)) {
          trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
          return false;
        }
      }
      // all done - leave
      return true;
    }
  }
  // delete the record
  $SQL = "DELETE FROM `".TABLE_PREFIX."mod_droplets_extension` WHERE `drop_droplet_name`='$droplet_name' ".
      "AND `drop_type`='$register_type' AND `drop_page_id`='$page_id'";
  if (!$database->query($SQL)) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
    return false;
  }
  return true;
} // unregister_droplet()

/**
 * Entfernt das angegebene Droplet aus der Suche
 *
 * @param STR $droplet_name
 * @return BOOL
 */
function unregister_droplet_search($droplet_name, $page_id) {
  return unregister_droplet($droplet_name, 'search', $page_id);
} // unregister_droplet_search()

/**
 * Entfernt das angegebene Droplet aus dem Template Header
 *
 * @param STR $droplet_name
 * @return BOOL
 */
function unregister_droplet_header($droplet_name, $page_id) {
  return unregister_droplet($droplet_name, 'header', $page_id);
} // unregister_droplet_header()

/**
 * Entfernt die CSS Registrierung fuer das angegebene Droplet
 *
 * @param STR $droplet_name
 * @return BOOL
 */
function unregister_droplet_css($droplet_name, $page_id) {
  return unregister_droplet($droplet_name, 'css', $page_id);
} // unregister_droplet_css()

/**
 * Entfernt die JavaScript Registrierung fuer das angegebene Droplet
 *
 * @param STR $droplet_name
 * @return BOOL
 */
function unregister_droplet_js($droplet_name, $page_id) {
  return unregister_droplet($droplet_name, 'javascript', $page_id);
} // unregister_droplet_css()

/**
 * Bereinigt den angegebenen Droplet Namen
 *
 * @param STR $droplet_name
 * @return STR $droplet_name
 */
function clear_droplet_name($droplet_name) {
  $droplet_name = strtolower($droplet_name);
  $droplet_name = str_replace('[', '', $droplet_name);
  $droplet_name = str_replace(']', '', $droplet_name);
  $droplet_name = trim($droplet_name);
  return $droplet_name;
} // clear_droplet_name()

/**
 * Bereinigt den Namen das angegebenen Modul Verzeichnis
 *
 * @param STR $module_directory
 * @return STR $module_directory
 */
function clear_module_directory($module_directory) {
  $module_directory = str_replace('/', '', $module_directory);
  $module_directory = str_replace('\\', '', $module_directory);
  $module_directory = trim($module_directory);
  return $module_directory;
} // clear_module_directory()

/**
 * Ueberprueft ob das angegebene Droplet auf der Seite mit der PAGE_ID
 * verwendet wird
 *
 * @param STR $droplet_name
 * @param INT $page_id
 * @return BOOL
 */
function droplet_exists($droplet_name, $page_id) {
  global $database;

  $droplet_name = clear_droplet_name($droplet_name);
  // check if the droplet exists in a WYSIWYG section
  $SQL = "SELECT * FROM `".TABLE_PREFIX."mod_wysiwyg` WHERE `page_id`='$page_id' AND ".
    "((`text` LIKE '%[[$droplet_name?%') OR (`text` LIKE '%[[$droplet_name]]%'))";
  $query = $database->query($SQL);
  if ($database->is_error()) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
    return false;
  }
  if ($query->numRows() > 0)
    return true;

  // perhaps TOPICs?
  $SQL = sprintf("SHOW TABLE STATUS LIKE '%smod_topics'", TABLE_PREFIX);
  $query = $database->query($SQL);
  if ($database->is_error()) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
    return false;
  }
  if ($query->numRows() > 0) {
    $add = (defined('TOPIC_ID')) ? " AND `topic_id`='".TOPIC_ID."'" : '';
    // TOPICS is installed, so check if there is a TOPIC section at this page
    $SQL = "SELECT `topic_id` FROM `".TABLE_PREFIX."mod_topics` WHERE `page_id`='$page_id'$add ".
      "AND ((`content_long` LIKE '%[[$droplet_name?%') OR (`content_long` LIKE '%[[$droplet_name]]%'))";
    $query = $database->query($SQL);
    if ($database->is_error()) {
      trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
      return false;
    }
    if ($query->numRows() > 0)
      return true;
  }
  return false;
} // droplet_exists()

/**
 * Check if a section for droplets_extension exists and create it if needed
 *
 * @param INT $page_id
 * @return BOOL
 */
function check_droplet_search_section($page_id = -1) {
  global $database;

  if ($page_id < 1) return false;

  // check for a droplets_extension section
  //$SQL = "SELECT * FROM `".TABLE_PREFIX."sections` WHERE `module`='droplets_extension'";
  $SQL = "SELECT `section_id` FROM `".TABLE_PREFIX."sections`, `".TABLE_PREFIX."pages` WHERE `module`='droplets_extension' AND `visibility`='public'";
  if (null == ($query = $database->query($SQL))) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
    return false;
  }
  if ($query->numRows() > 0)
    return true;

  // we need a published page for including the search section
  $SQL = "SELECT `visibility` FROM `".TABLE_PREFIX."pages` WHERE `page_id`='$page_id'";
  $visibility = $database->get_one($SQL, MYSQL_ASSOC);
  if ($database->is_error()) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
    return false;
  }
  if ($visibility != 'public')
    // this page is not usable for the search section
    return false;

  // get the position of the last section
  $SQL = "SELECT `position` FROM `".TABLE_PREFIX."sections` WHERE `page_id`='$page_id' ORDER BY `position` DESC LIMIT 1";
  $position = $database->get_one($SQL, MYSQL_ASSOC);
  if ($database->is_error()) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
    return false;
  }

  // insert a new section for droplets_extension
  $position++;
  $SQL = "INSERT INTO `".TABLE_PREFIX."sections` (`block`,`publ_end`,`publ_start`,`module`,`page_id`,`position`) ".
      "VALUES ('1','0','0','droplets_extension','$page_id','$position')";
  if (!$database->query($SQL)) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
    return false;
  }
  return true;
} // check_droplet_search_section()

/**
 * Unsanitize a text variable and prepare it for output
 *
 * @param string $text
 * @return string
 */
function unsanitizeText($text) {
  $text = stripcslashes($text);
  $text = str_replace(array("&lt;","&gt;","&quot;","&#039;"), array("<",">","\"","'"), $text);
  return $text;
} // unsanitizeText()


function getURLbyPageID($page_id) {
  global $database;
  global $post_id;

  if (defined('TOPIC_ID')) {
    // this is a TOPICS page
    $SQL = "SELECT `link` FROM `".TABLE_PREFIX."mod_topics` WHERE `topic_id`='".TOPIC_ID."'";
    $link = $database->get_one($SQL);
    if ($database->is_error()) {
      trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
      return false;
    }
    // include TOPICS settings
    global $topics_directory;
    include_once WB_PATH . '/modules/topics/module_settings.php';
    return WB_URL . $topics_directory . $link . PAGE_EXTENSION;
  }

  if (defined('POST_ID') || !is_null($post_id)) {
      // this is a NEWS page
      $id = defined('POST_ID') ? POST_ID : $post_id;
      $SQL = "SELECT `link` FROM `".TABLE_PREFIX."mod_news_posts` WHERE `post_id`='$id'";
      $link = $database->get_one($SQL);
      if ($database->is_error()) {
          trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
          return false;
      }
      return WB_URL.PAGES_DIRECTORY.$link.PAGE_EXTENSION;
  }

  $SQL = "SELECT `link` FROM `".TABLE_PREFIX."pages` WHERE `page_id`='$page_id'";
  $link = $database->get_one($SQL, MYSQL_ASSOC);
  if ($database->is_error()) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
    return false;
  }
  return WB_URL.PAGES_DIRECTORY.$link.PAGE_EXTENSION;
}

function print_page_head($open_graph=false) {
  global $database;
  global $wb;
  global $page_id;
  global $post_id;

  $title = trim($wb->page_title);
  $description = trim($wb->page_description);
  $keywords = trim($wb->page_keywords);

  if (defined('TOPIC_ID')) {
    // Es handelt sich um eine TOPICS Seite
    $SQL = sprintf("SELECT title, description, keywords FROM %smod_topics WHERE topic_id='%d'", TABLE_PREFIX, TOPIC_ID);
    if (false !== ($topics = $database->query($SQL))) {
      if (false !== ($topic = $topics->fetchRow(MYSQL_ASSOC))) {
        if (isset($topic['title']) && !empty($topic['title']))
          $title = $topic['title'];
        if (isset($topic['description']) && !empty($topic['description']))
          $description = $topic['description'];
        if (isset($topic['keywords']) && !empty($topic['keywords']))
          $keywords = $topic['keywords'];
      }
      else {
        trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
        return false;
      }
    }
    else {
      trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
      return false;
    }
  }
  elseif (defined('POST_ID') || !is_null($post_id)) {
    // Es handelt sich um eine NEWS Seite
      $id = defined('POST_ID') ? POST_ID : $post_id;
    $SQL = sprintf("SELECT title, content_short FROM %smod_news_posts WHERE post_id='%d'", TABLE_PREFIX, $id);
    if (false !== ($news = $database->query($SQL))) {
      if (false !== ($new = $news->fetchRow(MYSQL_ASSOC))) {
        if (isset($new['title']) && !empty($new['title']))
          $title = $new['title'];
        if (isset($new['content_short']) && !empty($new['content_short'])) {
          $words = explode(' ', strip_tags($new['content_short']));
          $description = '';
          foreach ($words as $word) {
            if (!empty($description))
              $description .= ' ';
            $description .= $word;
            if (strlen($description) > 220) {
              $description .= ' ...';
              break;
            }
          }
        }
      }
      else {
        trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
        return false;
      }
    }
  }

  $SQL = "SELECT `drop_module_dir`,`drop_droplet_name` FROM `".TABLE_PREFIX."mod_droplets_extension` WHERE `drop_type`='header' AND `drop_page_id`='$page_id' LIMIT 1";
  if (null == ($query = $database->query($SQL))) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
    return false;
  }
  if ($query->numRows() > 0) {
    $droplet = $query->fetchRow(MYSQL_ASSOC);
    if (droplet_exists($droplet['drop_droplet_name'], $page_id)) {
      // the droplet exists
      if (file_exists(WB_PATH.'/modules/'.$droplet['drop_module_dir'].'/droplet.extension.php')) {
        // we have to use the header informations from the droplet!
        include(WB_PATH.'/modules/'.$droplet['drop_module_dir'].'/droplet.extension.php');
        $user_func = $droplet['drop_module_dir'].'_droplet_header';
        if (function_exists($user_func)) {
          $header = call_user_func($user_func, $page_id);
          if (is_array($header)) {
            if (isset($header['title']) && !empty($header['title']))
              $title = $header['title'];
            if (isset($header['description']) && !empty($header['description']))
              $description = $header['description'];
            if (isset($header['keywords']) && !empty($header['keywords']))
              $keywords = $header['keywords'];
          }
        }
      }
    }
    else {
      // the droplet does not exists, so unregister it to avoid an overhead
      unregister_droplet_header($droplet['drop_droplet_name'], $page_id);
    }
  }

  // check if we have to load css or javascript files
  $load_css = '';
  $load_js = '';

  $SQL = "SELECT * FROM `".TABLE_PREFIX."mod_droplets_extension` WHERE (`drop_type`='css' OR `drop_type`='javascript') AND `drop_page_id`='$page_id'";
  if (null == ($query = $database->query($SQL))) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
    return false;
  }

  while (false !== ($droplet = $query->fetchRow(MYSQL_ASSOC))) {
    // go only ahead if the droplet exists
    if (droplet_exists($droplet['drop_droplet_name'], $droplet['drop_page_id'])) {
      // check if this droplet is loaded by a TOPICS article
      $topics_array = explode(',',$droplet['drop_topics_array']);
      if (defined('TOPIC_ID')) {
        if (!in_array(TOPIC_ID, $topics_array))
          // the droplet is not registered for this TOPIC_ID so continue
          continue;
      }
      elseif (!empty($droplet['drop_topics_array']) && !defined('TOPIC_ID')) {
        // the droplet is not registered for this TOPIC_ID so continue
        continue;
      }
      $checked = false;
      // first check if there exists a custom.* file ...
      $file = WB_PATH.'/modules/'.$droplet['drop_module_dir'].'/custom.'.$droplet['drop_file'];
      if (file_exists($file))
        $checked = true;
      else {
        // check for the regular file ...
        $file = WB_PATH.'/modules/'.$droplet['drop_module_dir'].'/'.$droplet['drop_file'];
        if (file_exists($file))
          $checked = true;
      }
      if ($checked) {
        // load the file
        $file = str_replace(WB_PATH, WB_URL, $file);
        if ($droplet['drop_type'] == 'css') {
          // CSS
          $load_css .= sprintf('  <link rel="stylesheet" type="text/css" href="%s" media="screen" />', $file);
        }
        else {
          // JavaScript
          $load_js .= sprintf('  <script type="text/javascript" src="%s"></script>', $file);
        }
      }
    }
    else {
      // unregister the droplet to prevent overhead
      unregister_droplet($droplet['drop_droplet_name'], $droplet['drop_type'], $page_id);
      // also unregister droplet search
      unregister_droplet_search($droplet['drop_droplet_name'], $page_id);
    }
  }

  // set the default values for the Open Graph support
  $image = '';
  $site_name = WEBSITE_TITLE;
  $og_type = 'article';
  $exec_droplets = true;

  // check if a configuration file exists
  if (file_exists(WB_PATH.'/modules/droplets_extension/config.json'))
    $config = json_decode(file_get_contents(WB_PATH.'/modules/droplets_extension/config.json'), true);

  if (isset($config['og:website_title']))
    $site_name = $config['og:website_title'];

  if (!empty($keywords)) {
    // process the keywords
    $ka = explode(',', $keywords);
    $keyword_array = array();
    foreach ($ka as $keyword) {
      $keyword = trim($keyword);
      if (false !== strpos($keyword, '=')) {
        list($command, $value) = explode('=', $keyword);
        $command = trim(strtolower($command));
        $value = trim($value);
        switch ($command) {
          case 'og:image':
             $image = $value;
             continue;
          case 'og:type':
            $og_type = strtolower($value);
            continue;
          case 'og:droplet':
            if (strtolower($value) == 'false')
              $exec_droplets = false;
            continue;
          case 'og:exec':
            if (strtolower($value) == 'false')
              $open_graph = false;
            continue;
        }
      }
      $keyword_array[] = $keyword;
    }
    // at least rewrite the keywords
    $keywords = implode(',', $keyword_array);
  }

  if (isset($config['og:droplets'])) {
    if (trim(strtolower($config['og:droplets'])) == 'false')
      $exec_droplets = false;
  }

  if (empty($image)) {
    // try to get the first image from the content
    if (false !== ($test = getFirstImageFromContent($page_id, $exec_droplets)))
      $image = $test;
  }

  if (empty($image)) {
    // if no image is available look if a image is defined in config.json
    if (isset($config['og:image']))
      $image = $config['og:image'];
  }

  if (!empty($image)) {
    $ext = pathinfo(WB_PATH.substr($image, strlen(WB_URL)), PATHINFO_EXTENSION);
    if (!in_array(strtolower($ext), array('png','gif','jpg','jpeg')))
      $image = '';
  }

  if ($open_graph && !empty($image)) {
    $url = getURLbyPageID($page_id);
    $image_dimensions = '';
    if (substr($image, 0, strlen(WB_URL)) == WB_URL) {
      list($width,$height) = getimagesize(WB_PATH.substr($image, strlen(WB_URL)));
$image_dimensions = <<<EOD
  <meta property="og:image:width" content="$width" />
  <meta property="og:image:height" content="$height" />
EOD;

$head = <<<EOD
  <!-- dropletsExtension -->
  <meta name="description" content="$description" />
  <meta name="keywords" content="$keywords" />
  <title>$title</title>
  <meta property="og:image" content="$image" />
  $image_dimensions
  <meta property="og:type" content="$og_type" />
  <meta property="og:title" content="$title" />
  <meta property="og:description" content="$description" />
  <meta property="og:url" content="$url" />
  <meta property="og:site_name" content="$site_name" />
  <link rel="image_src" href="$image" />
  $load_css
  $load_js
  <!-- /dropletsExtension -->
EOD;
    }
    else {
$head = <<<EOD
  <!-- dropletsExtension -->
  <meta name="description" content="$description" />
  <meta name="keywords" content="$keywords" />
  <title>$title</title>
  <meta property="og:type" content="$og_type" />
  <meta property="og:title" content="$title" />
  <meta property="og:description" content="$description" />
  <meta property="og:url" content="$url" />
  <meta property="og:site_name" content="$site_name">
  $load_css
  $load_js
  <!-- /dropletsExtension -->
EOD;
    }
  }
  else {
$head = <<<EOD
  <!-- dropletsExtension -->
  <meta name="description" content="$description" />
  <meta name="keywords" content="$keywords" />
  <title>$title</title>$load_css$load_js
  <!-- /dropletsExtension -->
EOD;
  }
  echo $head;
} // print_page_head()

/**
 * Gibt die URL des ersten Bildes aus dem Inhalt der aktiven Seite zurueck.
 * Dies kann ein WYSIWYG Abschnitt oder ein TOPICs Artikel sein.
 *
 * @param INT $page_id
 * @return STR URL oder BOOL FALSE
 */
function getFirstImageFromContent($page_id, $exec_droplets=true) {
  global $database;
  global $post_id;

  $img = array();
  $content = '';
  if (defined('TOPIC_ID')) {
    // this is a TOPICS article so get content from the TOPICS
    $SQL = "SELECT `content_long` FROM `".TABLE_PREFIX."mod_topics` WHERE `topic_id`='".TOPIC_ID."'";
    $result = $database->get_one($SQL, MYSQL_ASSOC);
    if ($database->is_error()) {
      trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
      return false;
    }
    if (is_string($result))
      $content = unsanitizeText($result);
  }
  elseif (!is_null($post_id) || defined('POST_ID')) {
      // this is a NEWS article so get the content from the NEWS
      $id = (defined('POST_ID')) ? POST_ID : $post_id;
      $SQL = "SELECT `content_long` FROM `".TABLE_PREFIX."mod_news_posts` WHERE `post_id`='$id'";
      $result = $database->get_one($SQL, MYSQL_ASSOC);
      if ($database->is_error()) {
          trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
          return false;
      }
      if (is_string($result))
          $content = unsanitizeText($result);
  }
  else {
    // this is a regular WYSIWYG article
    $SQL = "SELECT `section_id` FROM `".TABLE_PREFIX."sections` WHERE `page_id`='$page_id' AND `module`='wysiwyg' ORDER BY `position` ASC LIMIT 1";
    $section_id = $database->get_one($SQL, MYSQL_ASSOC);
    if ($database->is_error()) {
      trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
      return false;
    }

    $SQL = "SELECT `content` FROM `".TABLE_PREFIX."mod_wysiwyg` WHERE `section_id`='$section_id'";
    $result = $database->get_one($SQL, MYSQL_ASSOC);
    if ($database->is_error()) {
      trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $database->get_error()), E_USER_ERROR);
      return false;
    }
    if (is_string($result))
      $content = unsanitizeText($result);
  }
  if (!empty($content)) {
    // scan content for images
    if ($exec_droplets && file_exists(WB_PATH .'/modules/droplets/droplets.php')) {
      // we must process the droplets to get the real output content
      $_SESSION['DROPLET_EXECUTED_BY_DROPLETS_EXTENSION'] = true;
      ob_start();
        include_once(WB_PATH .'/modules/droplets/droplets.php');
        if (function_exists('evalDroplets')) {
          try {
            evalDroplets($content);
          } catch (Exception $e) {
            trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $e->getMessage()), E_USER_ERROR);
          }
        }
      ob_end_clean();
      unset($_SESSION['DROPLET_EXECUTED_BY_DROPLETS_EXTENSION']);
    }
    if (preg_match('/<img[^>]*>/', $content, $matches)) {
      preg_match_all('/([a-zA-Z]*[a-zA-Z])\s{0,3}[=]\s{0,3}("[^"\r\n]*)"/', $matches[0], $attr);
      foreach ($attr as $attributes) {
        foreach ($attributes as $attribut) {
          if (strpos($attribut, "=") !== false) {
            list($key, $value) = explode("=", $attribut);
            $value = trim($value);
            $value = substr($value, 1, strlen($value) - 2);
            $img[strtolower(trim($key))] = trim($value);
          }
        }
      }
    }
  }
  if (isset($img['src'])) {
    // es wurde ein Bild gefunden und ausgelesen
    $image = $img['src'];
    if (strpos($image, '..') !== false) {
      $image = substr($image, strpos($image, MEDIA_DIRECTORY.'/'));
      $image = WB_URL.$image;
    }
    return $image;
  }
  else {
    return false;
  }
} // getFirstImageFromContent()

?>
