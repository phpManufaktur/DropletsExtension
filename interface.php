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

require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/class.extension.php');
require_once(WB_PATH.'/modules/'.basename(dirname(__FILE__)).'/class.pages.php');
require_once(WB_PATH.'/modules/kit_tools/class.tools.php');

/**
 * Ueberprueft ob das angegebene Droplet registriert ist
 *
 * @param STR $droplet_name
 * @param STR $register_type
 * @param INT REFRENCE $page_id - die PAGE_ID fuer die das Droplet registriert ist
 * @return BOOL
 */
function is_registered_droplet($droplet_name, $register_type, $page_id) {
  $dbDropletExt = new dbDropletsExtension();
  $droplet_name = clear_droplet_name($droplet_name);
  $where = array(
      dbDropletsExtension::field_droplet_name => $droplet_name,
      dbDropletsExtension::field_type => $register_type,
      dbDropletsExtension::field_page_id => $page_id);
  $droplet = array();
  if (!$dbDropletExt->sqlSelectRecord($where, $droplet)) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbDropletExt->getError()), E_USER_ERROR);
    return false;
  }
  $result = (count($droplet) > 0) ? true : false;
  $page_id = ($result) ? $droplet[0][dbDropletsExtension::field_page_id] : -1;
  if ($page_id > 0) {
    // pruefen, ob eine droplet_search section existiert
    check_droplet_search_section($page_id);
  }
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
  return is_registered_droplet($droplet_name, dbDropletsExtension::type_search, $page_id);
} // is_registered_droplet_search()

/**
 * Ueberprueft ob das das angegebene Droplet fuer den Template Header registriert ist
 *
 * @param STR $droplet_name
 * @param INT REFERENCE $page_id
 * @return BOOL
 */
function is_registered_droplet_header($droplet_name, $page_id) {
  return is_registered_droplet($droplet_name, dbDropletsExtension::type_header, $page_id);
} // is_registered_droplet_header()

/**
 * Ueberpruefr ob fuer das angegebene Droplet CSS laden registriert ist
 *
 * @param STR $droplet_name
 * @param INT $page_id
 * @return BOOL
 */
function is_registered_droplet_css($droplet_name, $page_id) {
  return is_registered_droplet($droplet_name, dbDropletsExtension::type_css, $page_id);
} // is_registered_droplet_css()

/**
 * Ueberprueft ob fur das angegebene Droplet JavaSCript registriert ist
 *
 * @param STR $droplet_name
 * @param INT $page_id
 * @return BOOL
 */
function is_registered_droplet_js($droplet_name, $page_id) {
  return is_registered_droplet($droplet_name, dbDropletsExtension::type_javascript, $page_id);
} // is_registered_droplet_js()

/**
 * Registriert das angegebene Droplet
 *
 * @param STR $droplet_name - Name des Droplets
 * @param INT $page_id - PAGE_ID der Seite auf der das Droplet verwendet wird
 * @param STR $module_directory - Modul Verzeichnis in dem die Suche die Datei droplet.extension.php findet
 * @return BOOL
 */
function register_droplet($droplet_name, $page_id, $module_directory, $register_type, $file = '') {
  // zuerst pruefen, ob eine droplet_search section existiert
  if ($register_type == dbDropletsExtension::type_search)
    check_droplet_search_section($page_id);
  $droplet_name = clear_droplet_name($droplet_name);
  if (!droplet_exists($droplet_name, $page_id)) {
    return false;
  }
  $dbDropletExt = new dbDropletsExtension();

  if (is_registered_droplet($droplet_name, $register_type, $page_id)) {
    return true;
  }
  $module_directory = clear_module_directory($module_directory);
  $data = array(
      dbDropletsExtension::field_droplet_name => $droplet_name,
      dbDropletsExtension::field_page_id => $page_id,
      dbDropletsExtension::field_module_directory => $module_directory,
      dbDropletsExtension::field_type => $register_type,
      dbDropletsExtension::field_file => $file);
  if (!$dbDropletExt->sqlInsertRecord($data)) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbDropletExt->getError()), E_USER_ERROR);
    return false;
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
  return register_droplet($droplet_name, $page_id, $module_directory, dbDropletsExtension::type_search);
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
  return register_droplet($droplet_name, $page_id, $module_directory, dbDropletsExtension::type_header);
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
  return register_droplet($droplet_name, $page_id, $module_directory, dbDropletsExtension::type_css, $file);
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
  return register_droplet($droplet_name, $page_id, $module_directory, dbDropletsExtension::type_javascript, $file);
} // register_droplet_js()

/**
 * Entfernt das angegebene Droplet
 *
 * @param STR $droplet_name
 * @return BOOL
 */
function unregister_droplet($droplet_name, $register_type, $page_id) {
  $droplet_name = clear_droplet_name($droplet_name);
  $dbDropletExt = new dbDropletsExtension();
  $where = array(
      dbDropletsExtension::field_droplet_name => $droplet_name,
      dbDropletsExtension::field_type => $register_type,
      dbDropletsExtension::field_page_id => $page_id);
  if (!$dbDropletExt->sqlDeleteRecord($where)) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbDropletExt->getError()), E_USER_ERROR);
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
  return unregister_droplet($droplet_name, dbDropletsExtension::type_search, $page_id);
} // unregister_droplet_search()

/**
 * Entfernt das angegebene Droplet aus dem Template Header
 *
 * @param STR $droplet_name
 * @return BOOL
 */
function unregister_droplet_header($droplet_name, $page_id) {
  return unregister_droplet($droplet_name, dbDropletsExtension::type_header, $page_id);
} // unregister_droplet_header()

/**
 * Entfernt die CSS Registrierung fuer das angegebene Droplet
 *
 * @param STR $droplet_name
 * @return BOOL
 */
function unregister_droplet_css($droplet_name, $page_id) {
  return unregister_droplet($droplet_name, dbDropletsExtension::type_css, $page_id);
} // unregister_droplet_css()

/**
 * Entfernt die JavaScript Registrierung fuer das angegebene Droplet
 *
 * @param STR $droplet_name
 * @return BOOL
 */
function unregister_droplet_js($droplet_name, $page_id) {
  return unregister_droplet($droplet_name, dbDropletsExtension::type_javascript, $page_id);
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
  $dbWYSIWYG = new db_wb_mod_wysiwyg();
  $SQL = sprintf("SELECT * FROM %s WHERE %s='%s' AND ((%s LIKE '%%[[%s?%%') OR (%s LIKE '%%[[%s]]%%'))", $dbWYSIWYG->getTableName(), db_wb_mod_wysiwyg::field_page_id, $page_id, db_wb_mod_wysiwyg::field_text, $droplet_name, db_wb_mod_wysiwyg::field_text, $droplet_name);
  $result = array();
  if (!$dbWYSIWYG->sqlExec($SQL, $result)) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbWYSIWYG->getError()), E_USER_ERROR);
    return false;
  }
  if (count($result) > 0) {
    return true;
  }
  // moeglicher Weise TOPICs?
  $SQL = sprintf("SHOW TABLE STATUS LIKE '%smod_topics'", TABLE_PREFIX);
  $query = $database->query($SQL);
  if ($query->numRows() > 0) {
    // TOPICS ist installiert
    $SQL = sprintf("SELECT topic_id FROM %smod_topics WHERE page_id='%s' AND ((content_long LIKE '%%[[%s?%%') OR (content_long LIKE '%%[[%s]]%%'))", TABLE_PREFIX, $page_id, $droplet_name, $droplet_name);
    $query = $database->query($SQL);
    if ($query->numRows() > 0)
      return true;
  }
  return false;
} // droplet_exists()

/**
 * Prueft ob eine SECTION mit droplet_extension existiert und legt sie ggf. an.
 *
 * @param INT $page_id
 * @return BOOL
 */
function check_droplet_search_section($page_id = -1) {
  $dbSections = new db_wb_sections();
  $SQL = sprintf("SELECT * FROM %s WHERE %s='%s'", $dbSections->getTableName(), db_wb_sections::field_module, 'droplets_extension');
  $result = array();
  if (!$dbSections->sqlExec($SQL, $result)) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbSections->getError()), E_USER_ERROR);
    return false;
  }
  if (count($result) > 0)
    return true;
  if ($page_id < 1)
    return false;
  $SQL = sprintf("SELECT * FROM %s WHERE %s='%s' ORDER BY %s DESC LIMIT 1", $dbSections->getTableName(), db_wb_sections::field_page_id, $page_id, db_wb_sections::field_position);
  $result = array();
  if (!$dbSections->sqlExec($SQL, $result)) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbSections->getError()), E_USER_ERROR);
    return false;
  }
  if (count($result) < 1)
    return false;
  $x = $result[0][db_wb_sections::field_position];
  $data = array(
      db_wb_sections::field_block => 1,
      db_wb_sections::field_publ_end => 0,
      db_wb_sections::field_publ_start => 0,
      db_wb_sections::field_module => 'droplets_extension',
      db_wb_sections::field_page_id => $page_id,
      db_wb_sections::field_position => $x + 1);
  if (!$dbSections->sqlInsertRecord($data)) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbSections->getError()), E_USER_ERROR);
    return false;
  }
  return true;
} // check_droplet_search_section()

function print_page_head($facebook = false) {
  global $database;
  global $wb;
  global $page_id;

  $title = $wb->page_title;
  $description = $wb->page_description;
  $keywords = $wb->page_keywords;
  $dbDropletExt = new dbDropletsExtension();

  if (defined('TOPIC_ID')) {
    // Es handelt sich um eine TOPICS Seite
    $SQL = sprintf("SELECT title, short_description, keywords FROM %smod_topics WHERE topic_id='%d'", TABLE_PREFIX, TOPIC_ID);
    if (false !== ($topics = $database->query($SQL))) {
      if (false !== ($topic = $topics->fetchRow(MYSQL_ASSOC))) {
        if (isset($topic['title']) && !empty($topic['title']))
          $title = $topic['title'];
        if (isset($topic['short_description']) && !empty($topic['short_description']))
          $description = $topic['short_description'];
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
  elseif (defined('POST_ID')) {
    // Es handelt sich um eine NEWS Seite
    $SQL = sprintf("SELECT title, content_short FROM %smod_news_posts WHERE post_id='%d'", TABLE_PREFIX, POST_ID);
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

  $params = array(
      'title' => $title,
      'description' => $description,
      'keywords' => $keywords);
  // Droplets pruefen
  $SQL = sprintf("SELECT * FROM %s WHERE %s='%s' AND %s='%s'", $dbDropletExt->getTableName(), dbDropletsExtension::field_type, dbDropletsExtension::type_header, dbDropletsExtension::field_page_id, $page_id);
  $droplet = array();
  if (!$dbDropletExt->sqlExec($SQL, $droplet)) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbDropletExt->getError()), E_USER_ERROR);
    return false;
  }
  if (count($droplet) > 0) {
    // es ist ein Droplet angemeldet
    $droplet = $droplet[0];
    if (droplet_exists($droplet[dbDropletsExtension::field_droplet_name], $page_id)) {
      // das Droplet existiert
      if (file_exists(WB_PATH.'/modules/'.$droplet[dbDropletsExtension::field_module_directory].'/droplet.extension.php')) {
        include(WB_PATH.'/modules/'.$droplet[dbDropletsExtension::field_module_directory].'/droplet.extension.php');
        $user_func = $droplet[dbDropletsExtension::field_module_directory].'_droplet_header';
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
      // das Droplet existiert nicht...
      unregister_droplet_header($droplet[dbDropletsExtension::field_droplet_name], $page_id);
    }
  }

  // Pruefen ob CSS Dateien geladen werden sollen
  $load_css = '';
  $load_js = '';
  $SQL = sprintf("SELECT * FROM %s WHERE (%s='%s' OR %s='%s') AND `%s`='%s'", $dbDropletExt->getTableName(), dbDropletsExtension::field_type, dbDropletsExtension::type_css, dbDropletsExtension::field_type, dbDropletsExtension::type_javascript, dbDropletsExtension::field_page_id, $page_id);
  $droplets = array();
  if (!$dbDropletExt->sqlExec($SQL, $droplets)) {
    trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $dbDropletExt->getError()), E_USER_ERROR);
    return false;
  }
  foreach ($droplets as $droplet) {
    if (droplet_exists($droplet[dbDropletsExtension::field_droplet_name], $droplet[dbDropletsExtension::field_page_id])) {
      // das Droplet existiert
      $checked = false;

      // first check if there exists a custom.* file ...
      $file = WB_PATH.'/modules/'.$droplet[dbDropletsExtension::field_module_directory].'/custom.'.$droplet[dbDropletsExtension::field_file];
      if (file_exists($file)) {
        $checked = true;
      }
      else {
        // check for the regular file ...
        $file = WB_PATH.'/modules/'.$droplet[dbDropletsExtension::field_module_directory].'/'.$droplet[dbDropletsExtension::field_file];
        if (file_exists($file))
          $checked = true;
      }
      if ($checked) {
        // load the file
        $file = str_replace(WB_PATH, WB_URL, $file);
        if ($droplet[dbDropletsExtension::field_type] == dbDropletsExtension::type_css) {
          // CSS
          $load_css .= sprintf('<link rel="stylesheet" type="text/css" href="%s" media="screen" />'."\n", $file);
        }
        else {
          // JavaScript
          $load_js .= sprintf('<script type="text/javascript" src="%s"></script>'."\n", $file);
        }
      }
    }
    elseif ($droplet[dbDropletsExtension::field_type] == dbDropletsExtension::type_css) {
      // das Droplet existiert nicht...
      unregister_droplet_css($droplet[dbDropletsExtension::field_droplet_name], $page_id);
    }
    else {
      // JavaScript ...
      unregister_droplet_js($droplet[dbDropletsExtension::field_droplet_name], $page_id);
    }
  }

  $head = sprintf('<meta name="description" content="%s" />'."\n".'<meta name="keywords" content="%s" />'."\n".'<title>%s</title>'."\n".'%s%s', $description, $keywords, $title, $load_css, $load_js);
  if ($facebook) {
    if (false !== ($image = getFirstImageFromContent($page_id))) {
      $tools = new kitToolsLibrary();
      $url = '';
      $tools->getUrlByPageID($page_id, $url);
      $head .= sprintf('<meta property="og:image" content="%s" />'."\n".'<meta property="og:type" content="article" />'."\n".'<meta property="og:title" content="%s" />'."\n".'<meta property="og:description" content="%s" />'."\n".'<meta property="og:url" content="%s" />', $image, $title, $description, $url);
    }
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
function getFirstImageFromContent($page_id) {
  global $database;
  $img = array();
  if (defined('TOPIC_ID')) {
    // es handelt sich um einen TOPIC Artikel
    $SQL = sprintf("SELECT content_long FROM %smod_topics WHERE topic_id='%d'", TABLE_PREFIX, TOPIC_ID);
    if (false !== ($topics = $database->query($SQL))) {
      if (false !== ($topic = $topics->fetchRow(MYSQL_ASSOC))) {
        if (isset($topic['content_long']) && !empty($topic['content_long'])) {
          $content = $topic['content_long'];
        }
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
  else {
    // es handelt sich um einen normalen WYSIWYG Artikel
    $db_wysiwyg = new db_wb_mod_wysiwyg();
    $SQL = sprintf("SELECT %s FROM %s WHERE %s='%s' LIMIT 1", db_wb_mod_wysiwyg::field_content, $db_wysiwyg->getTableName(), db_wb_mod_wysiwyg::field_page_id, $page_id);
    $result = array();
    if (!$db_wysiwyg->sqlExec($SQL, $result)) {
      trigger_error(sprintf('[%s - %s] %s', __FUNCTION__, __LINE__, $db_wysiwyg->getError()));
      return false;
    }
    if (count($result) == 1) {
      $content = $result[0][db_wb_mod_wysiwyg::field_content];
    }
  }
  if (!empty($content)) {
    // Inhalt durchsuchen
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