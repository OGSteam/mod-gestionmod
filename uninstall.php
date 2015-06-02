<?php

/**
* uninstall.php Fichier désinstallation du MOD Gestion MOD
* @package Gestion MOD
* @author Kal Nightmare
* @update xaviernuma - 2015
* @link http://www.ogsteam.fr/
*/

if (!defined('IN_SPYOGAME')) 
{
    die("Hacking attempt");
}

global $db;

$mod_uninstall_name = "gestionmod";
$mod_uninstall_table = '';

uninstall_mod($mod_uninstall_name, $mod_uninstall_table);

?>