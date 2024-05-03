<?php

/**
 * rename.php Renommeur de MOD
 * @package Gestion MOD
 * @author Kal Nightmare
 * @update xaviernuma - 2015
 * @link http://www.ogsteam.fr/
 */

if (!defined('IN_SPYOGAME')) {
	die("Hacking attempt");
}
?>

<script type="text/javascript">
	function f_submit(page, id, menu, ordre) {
		document.getElementById('page').value = page;
		document.getElementById('id').value = id;
		document.getElementById('menu').value = document.getElementById(menu).value;
		document.getElementById('form_renommage_mod').submit();
	}
</script>

<table class='og-table og-medium-table'>
	<thead>
		<tr>
			<th colspan="4">
				Liste des MODS
				</td>
		</tr>
		<tr>
			<th>Nom du MOD</th>
			<th>version</th>
			<th>Nom affiché sur le menu</th>
			<th>Renommer</th>
		</tr>
	</thead>
	<tbody>
		<?php $ta_liste_table_mods = f_lister_la_table_mod(); ?>
		<?php for ($i = 1; $i <= count($ta_liste_table_mods); $i++) : ?>
			<?php if ($ta_liste_table_mods[$i]['type'] == 0) : ?>
				<tr>
					<td>
						<?php echo $ta_liste_table_mods[$i]['title']; ?>
					</td>
					<td>
						<?php echo $ta_liste_table_mods[$i]['version']; ?>
					</td>
					<td>
						<textarea style="width:350px;height:50px;" name="menu<?php echo $i; ?>" id="menu<?php echo $i; ?>"><?php echo $ta_liste_table_mods[$i]['menu']; ?></textarea>
					<td>
						<input class="og-button" type="button" name="ordre" value="Renommer" onclick="javascript:f_submit('<?php $pub_subaction; ?>', '<?php echo $ta_liste_table_mods[$i]['id']; ?>', 'menu<?php echo $i; ?>');">
					</td>
				</tr>
			<?php endif; ?>
		<?php endfor; ?>
	</tbody>
</table>



<form id="form_renommage_mod" method="post" action="index.php?action=gestion&subaction=action_mod">
	<input type="hidden" name="page" id="page" value="" />
	<input type="hidden" name="id" id="id" value="" />
	<input type="hidden" name="menu" id="menu" value="" />
	<input type="hidden" name="ordre" value="Renommer" />
</form>';


<?php echo f_aide_html();

