<?php

/**
* rename.php Renommeur de MOD
* @package Gestion MOD
* @author Kal Nightmare
* @update xaviernuma - 2012
* @link http://www.ogsteam.fr/
*/

if (!defined('IN_SPYOGAME')) 
{
	die("Hacking attempt");
}

$s_html = '';
$s_html .= '<script type="text/javascript">';
$s_html .= 	'function f_submit(page, id, menu, ordre)';
$s_html .= 	'{';
$s_html .= 		'document.getElementById(\'page\').value = page;';
$s_html .= 		'document.getElementById(\'id\').value = id;';
$s_html .= 		'document.getElementById(\'menu\').value = document.getElementById(menu).value;';
$s_html .= 		'document.getElementById(\'form_renommage_mod\').submit();';
$s_html .= 	'}';
$s_html .= '</script>';
$s_html .= '<br/>';
$s_html .= '<table>';
$s_html .= 	'<tr><td class="c" colspan="3">Liste des MODS</td></tr>';
$s_html .=	'<tr>';
$s_html .= 		'<th>Nom du MOD (version)</th>';
$s_html .= 		'<th>Nom affiché sur le menu</th>';
$s_html .= 		'<th>Renommer</th>';
$s_html .= 	'</tr>';

$ta_liste_table_mods = f_lister_la_table_mod();
for($i = 1 ; $i <= count($ta_liste_table_mods) ; $i++) 
{
	// On affiche que les mods sans les groupes
	if ($ta_liste_table_mods[$i]['type'] == 0) 
	{
		$s_html .= '<tr>';
		$s_html .= 	'<th style="width:150px;">'.$ta_liste_table_mods[$i]['title'].'<br>('.$ta_liste_table_mods[$i]['version'].')</th>';
		$s_html .= 	'<th><textarea style="width:650px;height:50px;" name="menu'.$i.'" id="menu'.$i.'">';
		$s_html .= 		$ta_liste_table_mods[$i]['menu'];
		$s_html .= 	'</textarea></th>';
		$s_html .= 	'<th><input type="button" name="ordre" value="Renommer" onclick="javascript:f_submit(\''.$pub_subaction.'\', \''.$ta_liste_table_mods[$i]['id'].'\', \'menu'.$i.'\');" /></th>';
		$s_html .= '</tr>';		
	}
}

$s_html .= '</table>';

$s_html .= '<form id="form_renommage_mod" method="post" action="index.php?action=gestion&subaction=action_mod">';
$s_html .= 	'<input type="hidden" name="page" id="page" value="" />';
$s_html .= 	'<input type="hidden" name="id" id="id" value="" />';
$s_html .= 	'<input type="hidden" name="menu" id="menu" value="" />';
$s_html .= 	'<input type="hidden" name="ordre" value="Renommer" />';
$s_html .= '</form>';

$s_html .= f_aide_html();

$s_html .= '<br/>';

echo $s_html;

?>