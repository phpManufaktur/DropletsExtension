<?php

/**
 * dropletsExtension
 * 
 * @author Ralf Hertsch (ralf.hertsch@phpmanufaktur.de)
 * @link http://phpmanufaktur.de
 * @copyright 2011
 * @license GNU GPL (http://www.gnu.org/licenses/gpl.html)
 * @version $Id$
 * 
 * FOR VERSION- AND RELEASE NOTES PLEASE LOOK AT INFO.TXT!
 */

// try to include LEPTON class.secure.php to protect this file and the whole CMS!
if (defined('WB_PATH')) {	
	if (defined('LEPTON_VERSION')) include(WB_PATH.'/framework/class.secure.php');
} elseif (file_exists($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php')) {
	include($_SERVER['DOCUMENT_ROOT'].'/framework/class.secure.php'); 
} else {
	$subs = explode('/', dirname($_SERVER['SCRIPT_NAME']));	$dir = $_SERVER['DOCUMENT_ROOT'];
	$inc = false;
	foreach ($subs as $sub) {
		if (empty($sub)) continue; $dir .= '/'.$sub;
		if (file_exists($dir.'/framework/class.secure.php')) { 
			include($dir.'/framework/class.secure.php'); $inc = true;	break; 
		} 
	}
	if (!$inc) trigger_error(sprintf("[ <b>%s</b> ] Can't include LEPTON class.secure.php!", $_SERVER['SCRIPT_NAME']), E_USER_ERROR);
}
// end include LEPTON class.secure.php

if ('á' != "\xc3\xa1") {
	// important: language files must be saved as UTF-8 (without BOM)
	trigger_error('The language file <b>'.basename(__FILE__).'</b> is damaged, it must be saved <b>UTF-8</b> encoded!', E_USER_ERROR);
}

// Module Description
$module_description 	= 'dropletExtension - Integriert Droplets in die WebsiteBaker Suchfunktion';
// name of the person(s) who translated and edited this language file
$module_translation_by = 'Ralf Hertsch (phpManufaktur)';

define('drop_ext_modify_prompt',				'<div style="border:1px solid #000;padding:15px;color:#000;background-color:#ffffcc;text-align:center;"><p>Für die einwandfreie Funktion des Addon <b>dropletExtension</b> muss wenigstens ein Abschnitt vom Typ <b>dropletExtension</b> existieren - auf welcher Seite sich dieser befindet spielt keinerlei Rolle, es sind darüber hinaus keine weiteren Einstellungen erforderlich.</p></div>');

?>