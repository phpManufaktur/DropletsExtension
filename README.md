### DropletsExtension

Extend Droplets for the Content Management Systems [WebsiteBaker] [1] and [LEPTON CMS] [2] with the ability to integrate into the CMS search function, to load CSS and JS files, rewrite page header informations and more.

#### Requirements

* minimum PHP 5.2.x
* using [WebsiteBaker] [1] _or_ using [LEPTON CMS] [2]

#### Installation

* download the actual [DropletsExtension] [5] installation archive
* in CMS backend select the file from "Add-ons" -> "Modules" -> "Install module"

#### First Steps

The DropletsExtension will automatically add a "droplets_extension" section to one (not specified) of your WYSIWYG pages. Don't care about it, the DropletsExtension need this section for technical reasons.

To enable DropletsExtension to modify header informations (page title, page description a.s.o.) - for your Droplets and also for the News Module or for TOPICS you must include a DropletsExtension function call into the head section of your template.

Please remove the following lines

    <meta name="description" content="<?php page_description(); ?>" />
    <meta name="keywords" content="<?php page_keywords(); ?>" />
    <title><?php page_title('', '[WEBSITE_TITLE]'); ?></title> 

from your template and replace them with

    <?php
      if (file_exists(WB_PATH.'/modules/droplets_extension/interface.php')) {
        require_once(WB_PATH.'/modules/droplets_extension/interface.php');
        print_page_head();
      }
      else { ?>
        <meta name="description" content="<?php page_description(); ?>" />
        <meta name="keywords" content="<?php page_keywords(); ?>" />
        <title><?php page_title('', '[WEBSITE_TITLE]'); ?></title>
      <?php }
    ?>

The function **print_page_head()** will work like Chio Maisrimls [simplepagehead()] [6] and is also a fully replacement for this tool.

Please visit the Homepage of [DropletsExtension] [7] to get more informations and join the [Addons Support Group] [8] of the phpManufaktur.  

[1]: http://websitebaker2.org "WebsiteBaker Content Management System"
[2]: http://lepton-cms.org "LEPTON CMS"
[5]: https://github.com/phpManufaktur/DropletsExtension/downloads
[6]: http://websitebaker.at/wb/module/simple-pagehead.html
[7]: https://addons.phpmanufaktur.de/de/name/dropletsextension.php
[8]: https://phpmanufaktur.de/support