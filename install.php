<?php

/**
* install.php Fichier d'installation du MOD Gestion MOD
* @package Gestion MOD
* @author Kal Nightmare
* @update xaviernuma - 2012
* @link http://www.ogsteam.fr/
*/

if (!defined('IN_SPYOGAME')) 
{
    die("Hacking attempt");
}

global $db;
$mod_folder = "gestionmod";

$is_ok = install_mod($mod_folder);

if ($is_ok == true)
{
	// Si besoin de cr�er des tables.
}
else
{
	echo  '<script type="text/javascript">alert(\'D�sol�, un probl�me � eu lieu pendant l\\\'installation, corrigez les probl�mes survenus et r�essayez.\');</script>';
}
	
?>