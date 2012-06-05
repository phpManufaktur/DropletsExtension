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

class db_wb_pages extends dbConnectLE {

  const field_page_id = 'page_id';
  const field_parent = 'parent';
  const field_root_parent = 'root_parent';
  const field_level = 'level';
  const field_link = 'link';
  const field_target = 'target';
  const field_page_title = 'page_title';
  const field_menu_title = 'menu_title';
  const field_description = 'description';
  const field_keywords = 'keywords';
  const field_page_trail = 'page_trail';
  const field_template = 'template';
  const field_visibility = 'visibility';
  const field_position = 'position';
  const field_menu = 'menu';
  const field_language = 'language';
  const field_searching = 'searching';
  const field_admin_groups = 'admin_groups';
  const field_admin_users = 'admin_users';
  const field_viewing_groups = 'viewing_groups';
  const field_viewing_users = 'viewing_users';
  const field_modified_when = 'modified_when';
  const field_modified_by = 'modified_by';

  public function __construct() {
    parent::__construct();
    $this->setTableName('pages');
    $this->addFieldDefinition(self::field_page_id, "INT(11) NOT NULL AUTO_INCREMENT", true);
    $this->addFieldDefinition(self::field_parent, "INT(11) NOT NULL DEFAULT '0'");
    $this->addFieldDefinition(self::field_root_parent, "INT(11) NOT NULL DEFAULT '0'");
    $this->addFieldDefinition(self::field_level, "INT(11) NOT NULL DEFAULT '0'");
    $this->addFieldDefinition(self::field_link, "TEXT NOT NULL DEFAULT ''");
    $this->addFieldDefinition(self::field_target, "VARCHAR(7) NOT NULL DEFAULT ''");
    $this->addFieldDefinition(self::field_page_title, "VARCHAR(255) NOT NULL DEFAULT ''");
    $this->addFieldDefinition(self::field_menu_title, "VARCHAR(255) NOT NULL DEFAULT ''");
    $this->addFieldDefinition(self::field_description, "TEXT NOT NULL DEFAULT ''");
    $this->addFieldDefinition(self::field_keywords, "TEXT NOT NULL DEFAULT ''");
    $this->addFieldDefinition(self::field_page_trail, "TEXT NOT NULL DEFAULT ''");
    $this->addFieldDefinition(self::field_template, "VARCHAR(255) NOT NULL DEFAULT ''");
    $this->addFieldDefinition(self::field_visibility, "VARCHAR(255) NOT NULL DEFAULT ''");
    $this->addFieldDefinition(self::field_position, "INT(11) NOT NULL DEFAULT '0'");
    $this->addFieldDefinition(self::field_menu, "INT(11) NOT NULL DEFAULT '0'");
    $this->addFieldDefinition(self::field_language, "VARCHAR(5) NOT NULL DEFAULT ''");
    $this->addFieldDefinition(self::field_searching, "INT(11) NOT NULL DEFAULT '0'");
    $this->addFieldDefinition(self::field_admin_groups, "TEXT NOT NULL DEFAULT ''");
    $this->addFieldDefinition(self::field_admin_users, "TEXT NOT NULL DEFAULT ''");
    $this->addFieldDefinition(self::field_viewing_groups, "TEXT NOT NULL DEFAULT ''");
    $this->addFieldDefinition(self::field_viewing_users, "TEXT NOT NULL DEFAULT ''");
    $this->addFieldDefinition(self::field_modified_when, "INT(11) NOT NULL DEFAULT '0'");
    $this->addFieldDefinition(self::field_modified_by, "INT(11) NOT NULL DEFAULT '0'");
    $this->checkFieldDefinitions();
  } // __construct()

} // class db_wb_pages

class db_wb_sections extends dbConnectLE {

  const field_section_id = 'section_id';
  const field_page_id = 'page_id';
  const field_position = 'position';
  const field_module = 'module';
  const field_block = 'block';
  const field_publ_start = 'publ_start';
  const field_publ_end = 'publ_end';

  public function __construct() {
    parent::__construct();
    $this->setTableName('sections');
    $this->addFieldDefinition(self::field_section_id, "INT(11) NOT NULL AUTO_INCREMENT", true);
    $this->addFieldDefinition(self::field_page_id, "INT(11) NOT NULL DEFAULT '0'");
    $this->addFieldDefinition(self::field_position, "INT(11) NOT NULL DEFAULT '0'");
    $this->addFieldDefinition(self::field_module, "VARCHAR(255) NOT NULL DEFAULT ''");
    $this->addFieldDefinition(self::field_block, "VARCHAR(255) NOT NULL DEFAULT ''");
    $this->addFieldDefinition(self::field_publ_start, "VARCHAR(255) NOT NULL DEFAULT '0'");
    $this->addFieldDefinition(self::field_publ_end, "VARCHAR(255) NOT NULL DEFAULT '0'");
    $this->checkFieldDefinitions();
  } // __construct()

} // class db_wb_sections

class db_wb_mod_wysiwyg extends dbConnectLE {

  const field_section_id = 'section_id';
  const field_page_id = 'page_id';
  const field_content = 'content';
  const field_text = 'text';

  public function __construct() {
    parent::__construct();
    $this->setTableName('mod_wysiwyg');
    $this->addFieldDefinition(self::field_section_id, "INT(11) NOT NULL DEFAULT '0'", true);
    $this->addFieldDefinition(self::field_page_id, "INT(11) NOT NULL DEFAULT '0'");
    $this->addFieldDefinition(self::field_content, "LONGTEXT NOT NULl DEFAULT ''");
    $this->addFieldDefinition(self::field_text, "LONGTEXT NOT NULL DEFAULT ''");
    $this->checkFieldDefinitions();
  } // __construct()

} // class db_wb_mod_wysiwyg

class db_wb_mod_code extends dbConnectLE {

  const field_section_id = 'section_id';
  const field_page_id = 'page_id';
  const field_content = 'content';

  public function __construct() {
    parent::__construct();
    $this->setTableName('mod_code');
    $this->addFieldDefinition(self::field_section_id, "INT(11) NOT NULL DEFAULT '0'", true);
    $this->addFieldDefinition(self::field_page_id, "INT(11) NOT NULL DEFAULT '0'");
    $this->addFieldDefinition(self::field_content, "TEXT NOT NULl DEFAULT ''");
    $this->checkFieldDefinitions();
  } // __construct()

} // class db_wb_mod_code

class handlePages {

	private $error = '';

  /**
   * Set $this->error to $error
   *
   * @param STR $error
   */
  public function setError($error) {
    $this->error = $error;
  } // setError()

  /**
   * Get Error from $this->error;
   *
   * @return STR $this->error
   */
  public function getError() {
    return $this->error;
  } // getError()

  /**
   * Check if $this->error is empty
   *
   * @return BOOL
   */
  public function isError() {
    return (bool) !empty($this->error);
  } // isError

  public function createPage($title, $parent, $module, $visibility, $admin_groups, $viewing_groups) {
    global $database;
    // admin object initialisieren
    require_once(WB_PATH.'/framework/class.admin.php');
    require_once(WB_PATH.'/framework/functions.php');
    require_once(WB_PATH.'/framework/class.order.php');

    $admin = new admin('Pages', 'pages_add', false, false);
    $title = htmlspecialchars($title);
    // sicherstellen, dass Admin in der Admin-Gruppe und in der Betrachter-Gruppe existiert
    if (!in_array(1, $admin_groups))
      $admin_groups[] = 1;
    if (!in_array(1, $viewing_groups))
      $viewing_groups[] = 1;

    // Leerer Titel?
    if (($title == '') || (substr($title, 0, 1) == '.')) {
      $this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, kit_error_blank_title));
      return false;
    }

    // pruefen, ob die Seite ueber die erforderlichen Rechte verfuegt
    if (!in_array(1, $admin->get_groups_id())) {
      $admin_perm_ok = false;
      foreach ($admin_groups as $adm_group) {
        if (in_array($adm_group, $admin->get_groups_id())) {
          $admin_perm_ok = true;
        }
      }
      if ($admin_perm_ok == false) {
        $this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, kit_error_insufficient_permissions));
        return false;
      }
      $admin_perm_ok = false;
      foreach ($viewing_groups as $view_group) {
        if (in_array($view_group, $admin->get_groups_id())) {
          $admin_perm_ok = true;
        }
      }
      if ($admin_perm_ok == false) {
        $this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, kit_error_insufficient_permissions));
        return false;
      }
    }

    $admin_groups = implode(',', $admin_groups);
    $viewing_groups = implode(',', $viewing_groups);

    // Dateinamen erstellen
    if ($parent == '0') {
      $link = '/'.page_filename($title);
      // Dateinamen 'index' und 'intro' umbenennen um Kollisionen zu vermeiden
      if (($link == '/index') || ($link == '/intro')) {
        $link .= '_0';
        $filename = WB_PATH.PAGES_DIRECTORY.'/'.page_filename($title).'_0'.PAGE_EXTENSION;
      }
      else {
        $filename = WB_PATH.PAGES_DIRECTORY.'/'.page_filename($title).PAGE_EXTENSION;
      }
    }
    else {
      $parent_section = '';
      $parent_titles = array_reverse(get_parent_titles($parent));
      foreach ($parent_titles as $parent_title) {
        $parent_section .= page_filename($parent_title).'/';
      }
      if ($parent_section == '/')
        $parent_section = '';
      $page_filename = page_filename($title);
      $page_filename = str_replace('_', '-', $page_filename);
      $link = '/'.$parent_section.$page_filename;
      $filename = WB_PATH.PAGES_DIRECTORY.'/'.$parent_section.$page_filename.PAGE_EXTENSION;
      make_dir(WB_PATH.PAGES_DIRECTORY.'/'.$parent_section);
    }

    // prufen, ob bereits eine Datei mit dem gleichen Dateinamen existiert
    $dbPages = new db_wb_pages();
    $where = array();
    $where[db_wb_pages::field_link] = $link;
    $pages = array();
    if (!$dbPages->sqlSelectRecord($where, $pages)) {
      $this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbPages->getError()));
      return false;
    }
    if ((sizeof($pages) > 0) || (file_exists(WB_PATH.PAGES_DIRECTORY.$link.PAGE_EXTENSION)) || (file_exists(WB_PATH.PAGES_DIRECTORY.$link.'/'))) {
      $this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, sprintf(kit_error_page_exists, $link)));
      return false;
    }

    // include the ordering class
    $order = new order(TABLE_PREFIX.'pages', 'position', 'page_id', 'parent');
    // clean order
    $order->clean($parent);
    // get the new order
    $position = $order->get_new($parent);

    // Template und Sprache der uebergeordneten Seite ermitteln
    $where = array();
    $where[db_wb_pages::field_page_id] = $parent;
    $pages = array();
    if (!$dbPages->sqlSelectRecord($where, $pages)) {
      $this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbPages->getError()));
      return false;
    }
    if (sizeof($pages) > 0) {
      $template = $pages[0][db_wb_pages::field_template];
      $language = $pages[0][db_wb_pages::field_language];
    }
    else {
      $template = '';
      $language = DEFAULT_LANGUAGE;
    }

    // Neue Seite in Tabelle einfuegen
    $data = array();
    $data[db_wb_pages::field_page_title] = $title;
    $data[db_wb_pages::field_menu_title] = $title;
    $data[db_wb_pages::field_parent] = $parent;
    $data[db_wb_pages::field_template] = $template;
    $data[db_wb_pages::field_target] = '_top';
    $data[db_wb_pages::field_position] = $position;
    $data[db_wb_pages::field_visibility] = $visibility;
    $data[db_wb_pages::field_searching] = 1;
    $data[db_wb_pages::field_menu] = 1;
    $data[db_wb_pages::field_language] = $language;
    $data[db_wb_pages::field_admin_groups] = $admin_groups;
    $data[db_wb_pages::field_viewing_groups] = $viewing_groups;
    $data[db_wb_pages::field_modified_when] = time();
    $data[db_wb_pages::field_modified_by] = $admin->get_user_id();
    $page_id = -1;
    if (!$dbPages->sqlInsertRecord($data, $page_id)) {
      $this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbPages->getError()));
      return false;
    }

    // work out the level
    $level = level_count($page_id);
    // work out root parent
    $root_parent = root_parent($page_id);
    // work out page trail
    $page_trail = get_page_trail($page_id);

    $where = array();
    $where[db_wb_pages::field_page_id] = $page_id;
    $data = array();
    $data[db_wb_pages::field_link] = $link;
    $data[db_wb_pages::field_level] = $level;
    $data[db_wb_pages::field_root_parent] = $root_parent;
    $data[db_wb_pages::field_page_trail] = $page_trail;
    if (!$dbPages->sqlUpdateRecord($data, $where)) {
      $this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbPages->getError()));
      return false;
    }

    // create a new file in the /pages directory
    create_access_file($filename, $page_id, $level);

    // add position 1 to new page
    $position = 1;

    // add a new record to section table
    $dbSections = new db_wb_sections();
    $data = array();
    $data[db_wb_sections::field_page_id] = $page_id;
    $data[db_wb_sections::field_position] = $position;
    $data[db_wb_sections::field_module] = $module;
    $data[db_wb_sections::field_block] = 1;
    $section_id = -1;
    if (!$dbSections->sqlInsertRecord($data, $section_id)) {
      $this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbSections->getError()));
      return false;
    }

    if (file_exists(WB_PATH.'/modules/'.$module.'/add.php')) {
      require(WB_PATH.'/modules/'.$module.'/add.php');
    }
    if ($database->is_error()) {
      $this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $database->get_error()));
      return false;
    }
    return $page_id;
  } // createPage()

  private function deletePage($page_id) {
    global $database;
    $dbPages = new db_wb_pages();
    $where = array();
    $where[db_wb_pages::field_page_id] = $page_id;
    $pages = array();
    if (!$dbPages->sqlSelectRecord($where, $pages)) {
      $this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbPages->getError()));
      return false;
    }
    if (sizeof($pages) == 0) {
      $this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, sprintf(kit_error_page_not_found, $page_id)));
      return false;
    }
    $parent = $pages[0][db_wb_pages::field_parent];
    $link = $pages[0][db_wb_pages::field_link];

    $dbSections = new db_wb_sections();
    $where = array();
    $where[db_wb_sections::field_page_id] = $page_id;
    $sections = array();
    if (!$dbSections->sqlSelectRecord($where, $sections)) {
      $this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbSections->getError()));
      return false;
    }
    foreach ($sections as $section) {
      $section_id = $section[db_wb_sections::field_section_id];
      // Include the modules delete file if it exists
      if (file_exists(WB_PATH.'/modules/'.$section[db_wb_sections::field_module].'/delete.php')) {
        require(WB_PATH.'/modules/'.$section[db_wb_sections::field_module].'/delete.php');
      }
    }

    $where = array();
    $where[db_wb_pages::field_page_id] = $page_id;
    if (!$dbPages->sqlDeleteRecord($where)) {
      $this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbPages->getError()));
      return false;
    }

    $where = array();
    $where[db_wb_sections::field_page_id] = $page_id;
    if (!$dbSections->sqlDeleteRecord($where)) {
      $this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, $dbSections->getError()));
      return false;
    }

    // Include the ordering class or clean-up ordering
    $order = new order(TABLE_PREFIX.'pages', 'position', 'page_id', 'parent');
    $order->clean($parent);

    // Unlink the page access file and directory
    $directory = WB_PATH.PAGES_DIRECTORY.$link;
    $filename = $directory.PAGE_EXTENSION;
    $directory .= '/';
    if (file_exists($filename)) {
      if (!is_writable(WB_PATH.PAGES_DIRECTORY.'/')) {
        $this->setError(sprintf('[%s - %s] %s', __METHOD__, __LINE__, sprintf(kit_error_delete_access_file, $filename)));
        return false;
      }
      else {
        unlink($filename);
        if (file_exists($directory) && rtrim($directory, '/') != WB_PATH.PAGES_DIRECTORY && substr($link, 0, 1) != '.') {
          rm_full_dir($directory);
        }
      }
    }
    return true;
  } // deletePage()

}

?>