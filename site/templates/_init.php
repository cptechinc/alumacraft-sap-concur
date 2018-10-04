<?php

/**
 * Initialization file for template files 
 * 
 * This file is automatically included as a result of $config->prependTemplateFile
 * option specified in your /site/config.php. 
 * 
 * You can initialize anything you want to here. In the case of this beginner profile,
 * we are using it just to include another file with shared functions.
 *
 */

include_once("./_func.php"); // include our shared functions
include_once("./_dbfunc.php"); // include our shared functions

include_once($config->paths->vendor."cptechinc/dplus-processwire/vendor/autoload.php");

$page->fullURL = new \Purl\Url($page->httpUrl);
$page->fullURL->path = '';
if (!empty($config->filename) && $config->filename != '/') {
	$page->fullURL->join($config->filename);
}
