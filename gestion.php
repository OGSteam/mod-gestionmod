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
if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}

$s_sql = "SELECT `active` FROM `" . TABLE_MOD . "` WHERE `action` = 'gestion' AND `active` = 1 LIMIT 1;";

if (!$db->sql_numrows($db->sql_query($s_sql))) {
	die("Hacking attempt");
}

define("GESTION_MOD", true);

global $user_data;
$s_html = '';

// Récupération de la version
$s_sql = "SELECT `version` FROM `" . TABLE_MOD . "` WHERE `action` = 'gestion';";
$ta_resultat = $db->sql_fetch_assoc($db->sql_query($s_sql));

$version = $ta_resultat['version'];
$dir = "gestionmod";

require_once("views/page_header.php");
require_once("mod/" . $dir . "/function.php");

if ($user_data["user_admin"] <> 1 && $user_data["user_coadmin"] <> 1) {
	redirection("index.php?action=message&id_message=forbidden&info");
}

if (!isset($pub_subaction)) {
	$pub_subaction = 'list';
}

$s_sql = "SELECT * FROM `" . TABLE_MOD . "` WHERE `action` = 'autoupdate' and `active` = 1; ";
$r_sql = $db->sql_query($s_sql);
$isautoupdate = false;

if ($db->sql_numrows($r_sql) > 0) {
	$n_colonnes = 5;
	$row = $db->sql_fetch_assoc($r_sql);
	$lien = 'mod/' . $row['root'] . '/' . $row['link'];
	$isautoupdate = true;
}

$activeTagList  = ($pub_subaction == 'list') ? 'active' : '';
$activeTagGroup  = ($pub_subaction == 'group') ? 'active' : '';
$activeTagMod  = ($pub_subaction == 'mod') ? 'active' : '';


if ($pub_subaction <> 'list')
?>

<div class="ogspy-mod-header">
	<h2>Gestion Mod</h2>
</div>

<div class="nav-page-menu">
	<div class="nav-page-menu-item <?php echo $activeTagList; ?>">
		<a class="nav-page-menu-link" href="index.php?action=gestion&amp;subaction=list">
			Liste MOD
		</a>
	</div>
	<div class="nav-page-menu-item   <?php echo $activeTagGroup; ?>">
		<a class="nav-page-menu-link" href="index.php?action=gestion&amp;subaction=group">
			Gestion Groupes
		</a>
	</div>
	<div class="nav-page-menu-item  <?php echo $activeTagMod; ?> ">
		<a class="nav-page-menu-link" href="index.php?action=gestion&amp;subaction=mod">
			Renommeur de MOD
		</a>
	</div>
	<div class="nav-page-menu-item   ">
		<a class="nav-page-menu-link" href="index.php?action=administration&amp;subaction=mod">
			Administration des mods
		</a>
	</div>
	<?php if ($isautoupdate) : ?>
		<div class="nav-page-menu-item   ">
			<a class="nav-page-menu-link" href="index.php?action=AutoUpdate">
				AutoUpdate
			</a>
		</div>
	<?php endif; ?>
</div>






<?php





switch ($pub_subaction) {
	case 'list':
		require('mod/' . $dir . '/list.php');
		break;

	case 'group':
		require('mod/' . $dir . '/group.php');
		break;

	case 'mod':
		require('mod/' . $dir . '/rename.php');
		break;

	case 'modUpdate':
		//plus utilisé
		require($lien);
		break;

	case 'new_group':
		f_nouveau_groupe();
		break;

	case 'action_group':
		f_gerer_un_groupe();
		break;
	case 'action_mod':
		f_gerer_mod();
		break;

	default:
		require('mod/' . $dir . '/list.php');
}

?>
<?php if ($pub_subaction <> 'modUpdate') : ?>
	<div class="ogspy-mod-footer">
		<p>
			Gestion MOD (<?php echo $version; ?>)
			<br>Développé par <a href="mailto:kalnightmare@free.fr">Kal Nightmare</a> 2006 /
			Mise à jour par <a href="mailto:contact@epe-production.org?subject=gestionmod">xaviernuma</a> 2015
	</div>
	</p>
	</div>
<?php endif; ?>


<?php
require_once("views/page_tail.php");
