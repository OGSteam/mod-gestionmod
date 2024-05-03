<?php

/**
 * list.php Fichier de gestion pour ordonner les mods
 * @package Gestion MOD
 * @author Kal Nightmare
 * @update xaviernuma - 2012
 * @link http://www.ogsteam.fr/
 */

if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}

$s_html = '';
$liste_table_mods = f_lister_la_table_mod();
$s_html_normal = '';
$s_html_admin = '';

// On parcourt tout les mods
for ($i = 1; $i <= count($liste_table_mods); $i++) {
	switch ($liste_table_mods[$i]['type']) {
		case 0: // Mod classique
			if ($liste_table_mods[$i]['active'] == 1) {
				if ($liste_table_mods[$i]['admin_only'] == 1) // Si c'est un mod de type admin alors...
				{
					$s_html_admin .= "<tr id=" . $liste_table_mods[$i]['id'] . " class='lime'>";
					$s_html_admin .= "<td><span class='og-alert'>" . strip_tags($liste_table_mods[$i]['menu']) . "</span></td></tr>";
				} else {
					$s_html_normal .= "<tr id=" . $liste_table_mods[$i]['id'] . " class='lime'>";
					$s_html_normal .= "<td><span class=''>" . strip_tags($liste_table_mods[$i]['menu']) . "</span></td></tr>";
				}
			} else {
				if ($liste_table_mods[$i]['admin_only'] == 1) // Si c'est un mod de type admin alors...
				{
					$s_html_admin .= "<tr id=" . $liste_table_mods[$i]['id'] . ">";
					$s_html_admin .= "<td><span class='og-alert'>" . strip_tags($liste_table_mods[$i]['menu']) . "</span></td></tr>";
				} else {
					$s_html_normal .= "<tr id=" . $i . " >";
					$s_html_normal .= "<td><span class=''>" . strip_tags($liste_table_mods[$i]['menu']) . "</span></td></tr>";
				}
			}
			break;
		case 1: // Groupe
			if ($liste_table_mods[$i]['admin_only'] == 1) // Si c'est un mod de type admin alors...
			{
				$s_html_admin .= "<tr id=" . $liste_table_mods[$i]['id'] . " >";
				$s_html_admin .= "<td><span class='og-highlight'>Groupe : " . strip_tags(f_nom_du_groupe($liste_table_mods[$i]['menu'])) . "</span></td></tr>";
			} else {
				$s_html_normal .= "<tr id=" . $liste_table_mods[$i]['id'] . " >";
				$s_html_normal .= "<td><span class='og-highlight'>Groupe : " . strip_tags(f_nom_du_groupe($liste_table_mods[$i]['menu'])) . "</span></td></tr>";
			}
			break;
	}
}
?>

<style type="text/css">
	.lime {
		color: lime;
	}

	.red {
		color: red;
	}

	.blue {
		color: #5CCCE8;
	}
</style>

<script type="text/javascript" src="mod/<?php echo $dir; ?>/classe.tablednd.js"></script>

<script type="text/javascript">
	function f_submit(page, id, position, place_limite, place_voulue, ordre) {
		var t_id = new Array();
		var n_i_tableau = 0;

		var tablenormal = document.getElementById('table-normal');
		var tableadmin = document.getElementById('table-admin');

		var rows = tablenormal.tBodies[0].rows;
		for (var i = 0; i < rows.length; i++) {
			t_id[n_i_tableau] = rows[i].getAttribute("id");
			n_i_tableau++;
		}

		var rows = tableadmin.tBodies[0].rows;
		for (var i = 0; i < rows.length; i++) {
			t_id[n_i_tableau] = rows[i].getAttribute("id");
			n_i_tableau++;
		}

		document.getElementById('module_range').value = t_id;
		document.getElementById('formulaire_deplacement').submit();
	}
</script>

<?php
$s_html .= '<br>';
$s_html .= "<h2 style=\"text-align:center;\" class=\"og-highlight\">Normal</h2>";
//$s_html .= '<div style="width:645px;text-align:left;color:white;font-weight:bold;padding-left:3px;font-size:11px;border:1px solid #F0ECED;font-family:Trebuchet MS,Arial,Helvetica,sans-serif;background-image:url(\'skin/OGSpy_skin/tableaux/td_c.png\');background-repeat:repeat-y;">Normal</div>';
$s_html .= '<table class="og-table og-little-table" id="table-normal" >';
$s_html .= '<tbody>';
$s_html .= 	$s_html_normal;
$s_html .= '</tbody>';
$s_html .= '</table>';

$s_html .= '<br>';

$s_html .= "<h2 style=\"text-align:center;\" class=\"og-highlight\">Admin</h2>";
//$s_html .= 	'<div style="width:645px;text-align:left;color:white;font-weight:bold;padding-left:3px;font-size:11px;border:1px solid #F0ECED;font-family:Trebuchet MS,Arial,Helvetica,sans-serif;background-image:url(\'skin/OGSpy_skin/tableaux/td_c.png\');background-repeat:repeat-y;">Admin</div>';
$s_html .= '<table  class="og-table og-little-table" id="table-admin" ">';
$s_html .= 	$s_html_admin;
$s_html .= '</table>';

$s_html .= '<br>';


echo $s_html;
?>
<script type="text/javascript">
	var table = document.getElementById('table-normal');
	var tableDnD = new TableDnD();
	tableDnD.init(table);

	var table2 = document.getElementById('table-admin');
	var tableDnD2 = new TableDnD();
	tableDnD2.init(table2);
</script>

				<form style="text-align:center;" id="formulaire_deplacement" method="post" action="index.php?action=gestion&subaction=action_mod">
					<input type="hidden" name="module_range" id="module_range" value="" />
					<input type="hidden" name="ordre" id="ordre" value="maj" />
					<input style=" vertical-align: middle;" class=og-button type="button" onclick="javascript:f_submit();" name="Mettre à jour" value="Mettre à jour" />
				</form>
