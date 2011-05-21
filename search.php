<?php
/**
 * dropletExtension
 * 
 * @author Ralf Hertsch (ralf.hertsch@phpmanufaktur.de)
 * @link http://phpmanufaktur.de
 * @copyright 2011
 * @license GNU GPL (http://www.gnu.org/licenses/gpl.html)
 * @version $Id$
 */

// prevent this file from being accessed directly
if (!defined('WB_PATH')) die('invalid call of '.$_SERVER['SCRIPT_NAME']);



function droplet_extension_search($func_vars) {
	extract($func_vars, EXTR_PREFIX_ALL, 'func');
	$result = false;
//	var_dump($func_vars);
	
//	print_R($func_search_words);
$mod_vars = array(
				'page_link' => '', //$res['link'], // use direct link to news-item
				'page_link_target' => "&result=treffer&droplet=mama",
				'page_title' => $func_page_title,
				'page_description' =>  "DROPLET TRREFFER!!!", // $res['title'], // use news-title as description
				//'page_modified_when' => $res['posted_when'],
				'page_modified_by' => 1,//$res['posted_by'],
				'text' => "Die digitale Revolution  ist längst nicht abgeschlossen Dennoch sind ihre Auswirkungen allgegenwärtig – in jedem Büro steht heute ein Computer der mit Software zur Textverarbeitung, Kalkulation sowie Kommunikation ausgestattet ist. Standardisierte Software in Verbindung mit immer leistungsfähigerer Hardware sind der Schlüssel für die rasante Entwicklung, die uns mitreißt und prägt.
Parallel dazu findet eine Entwicklung statt, der wir uns ebenfalls nicht entziehen können und die am einfachsten unter dem Begriff Web 2.0  zusammengefasst werden kann. Mittlerweile kommt kein Freelancer oder KMU mehr um eine Präsenz im Internet herum.
Während auf den Firmen- und Behördenrechnern nach wie vor urheberrechtlich geschützte Software  dominiert, wird der überwiegende Teil der Webserver, Datenbanken, Content Management Systeme sowie Shops im Internet mit freier Software  betrieben.
				"
			);
			if(print_excerpt2($mod_vars, $func_vars)) { 
				$result = true;
			}
	return $result;
} 

?>