<?php

/**
* gestion.php Fichier de gestion des différentes parties
* @package Gestion MOD
* @author Kal Nightmare
* @update xaviernuma - 2012
* @link http://www.ogsteam.fr/
*/
error_reporting(E_ALL);
ini_set("display_errors", 1);
if (!defined('IN_SPYOGAME')) 
{
	die("Hacking attempt");
}

$s_sql = "SELECT `active` FROM `".TABLE_MOD."` WHERE `action` = 'gestion' AND `active` = 1 LIMIT 1;";

if (!$db->sql_numrows($db->sql_query($s_sql)))
{
	die("Hacking attempt");
}

define("GESTION_MOD", true);

global $user_data;
$s_html = '';

// Récupération de la version
$s_sql = "SELECT `version` FROM `".TABLE_MOD."` WHERE `action` = 'gestion';";
$ta_resultat = $db->sql_fetch_assoc($db->sql_query($s_sql));

$version = $ta_resultat['version'];
$dir = "gestionmod";

require_once("views/page_header.php");
require_once("mod/".$dir."/function.php");

if($user_data["user_admin"] <> 1 && $user_data["user_coadmin"] <> 1)
{
	redirection("index.php?action=message&id_message=forbidden&info");
} 

if(!isset($pub_subaction)) 
{
	$pub_subaction = 'list';
}

$s_sql = "SELECT * FROM `".TABLE_MOD."` WHERE `action` = 'autoupdate' and `active` = 1; ";
$r_sql = $db->sql_query($s_sql);

if ($db->sql_numrows($r_sql) > 0) 
{
	$n_colonnes = 5;
	$row = $db->sql_fetch_assoc($r_sql);
	$lien = 'mod/'.$row['root'].'/'.$row['link'];
} 
else 
{
	$n_colonnes = 4;
}

$n_taille_colonne = floor(100/$n_colonnes);

$s_html .= '<table style="width:100%;">';
$s_html .= 	'<tr style="text-align:center;"><td class="c" colspan="'.$n_colonnes.'">GESTION MOD</td></tr>';
$s_html .= 	'<tr style="text-align:center;">';


if ($pub_subaction <> 'list')
{
	$s_html .= '<td class="c" style="width:'.$n_taille_colonne.'%;"><a href="index.php?action=gestion&subaction=list" style="color:lime;"';
}
else
{
	$s_html .= '<th style="width:'.$n_taille_colonne.'%;"><a';
}
$s_html .= '>Liste MOD</a></td>';

if ($pub_subaction <> 'group') 
{
	$s_html .= '<td class="c" style="width:'.$n_taille_colonne.'%;"><a href="index.php?action=gestion&subaction=group" style="color: lime;"';
}
else
{ 
	$s_html .= '<th style="width:'.$n_taille_colonne.'%;"><a';
}

$s_html .= '>Gestion Groupes</a></td>';

if ($pub_subaction <> 'mod') 
{
	$s_html .= '<td class="c" style="width:'.$n_taille_colonne.'%;"><a href="index.php?action=gestion&subaction=mod" style="color: lime;"';
}
else 
{
	$s_html .= '<th style="width:'.$n_taille_colonne.'%;"><a';
}
$s_html .= '>Renommeur de MOD</a></td>';


$s_html .= '<td class="c" style="width:'.$n_taille_colonne.'%;"><a href="index.php?action=administration&subaction=mod" style="color: lime;"';
$s_html .= '>Administration des mods</a></td>';

if ($n_colonnes == 5) 
{
	if ($pub_subaction <> 'modUpdate') 
	{
		$s_html .= '<td class="c" style="width:'.$n_taille_colonne.'%;"><a href="index.php?action=gestion&subaction=modUpdate" style="color: lime;"';
	}
	else 
	{
		$s_html .= '<th style="width:'.$n_taille_colonne.'%;"><a';
	}
	$s_html .= '>AutoUpdate</a></td>';
}

$s_html .= 	'</tr>';
$s_html .= '</table>';

echo $s_html;

switch ($pub_subaction) 
{
	case 'list':
		require('mod/'.$dir.'/list.php');
		break;

	case 'group':
		require('mod/'.$dir.'/group.php');
		break;

	case 'mod':
		require('mod/'.$dir.'/rename.php');
		break;

	case 'modUpdate':
		require($lien);
		break;
		
	case 'new_group':
		f_nouveau_groupe();
		break;

	case 'action_group':
		f_gerer_un_groupe();
		break;
	case 'action_mod' :
		f_gerer_mod();
		break;
		
	default:
		require('mod/'.$dir.'/list.php');
}

if($pub_subaction <> 'modUpdate')
{
	$s_html = '';
	$s_html .= '<div style="font-size:10px;width:400px;text-align:center;background-image:url(\'skin/OGSpy_skin/tableaux/th.png\');background-repeat:repeat;">Gestion MOD ('.$version.')';
	$s_html .= '<br>Développé par <a href="mailto:kalnightmare@free.fr">Kal Nightmare</a> 2006';
	$s_html .= '<br>Mise à jour par <a href="mailto:contact@epe-production.org?subject=gestionmod">xaviernuma</a> 2015</div>';

	echo $s_html;
}

require_once("views/page_tail.php");

?>