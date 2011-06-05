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
 */

// prevent this file from being accessed directly
if (!defined('WB_PATH')) die('invalid call of '.$_SERVER['SCRIPT_NAME']);

$module_directory     = 'droplets_extension';
$module_name          = 'dropletsExtension';
$module_function      = 'page';
$module_version       = '0.13';
$module_status        = 'Beta';
$module_platform      = '2.8'; 
$module_author        = 'Ralf Hertsch, Berlin (Germany)';
$module_license       = 'GNU General Public License';
$module_description   = 'dropletsExtension - integrate droplets into the WebsiteBaker search function'; 
$module_home          = 'http://phpmanufaktur.de/droplets_extension';
$module_guid          = '2F1CCDD0-A922-4DDA-BDA8-FB0624F6C1FE';

/**
 * Version and Release Notes
 * 
 * 0.13 - 2011-06-05
 * - fixed: problem setting header informations at TOPICs articles
 * 
 * 0.12 - 2011-06-04
 * - added: support for topics
 * 
 * 0.11 - 2011-06-04
 * - fixed: some minor bugs
 * - changed: DropletExtension can now handle multiple instances of the same droplet
 * - added: JavaScript Support - register_droplet_js()
 * 
 * 0.10 - first BETA-Release
 * 
 * 
 */
?>