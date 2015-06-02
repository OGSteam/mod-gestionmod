<?php

/**
* function.php Fichier avec les fonctions
* @package Gestion MOD
* @author Kal Nightmare
* @update xaviernuma - 2015
* @link http://www.ogsteam.fr/
*/

if (!defined('IN_SPYOGAME')) 
{
	die("Hacking attempt");
}

if (!defined('GESTION_MOD')) 
{
	die("Hacking attempt");
}

// Retourne le dernier numéro de groupe qui à été créé.
function f_dernier_numero_de_groupe() 
{
	// Déclaration des variables
	global $db;
	
	// On sélectionne les numéros de groupe dans la table root
	$s_sql = "SELECT `root` FROM `".TABLE_MOD."` WHERE `link` = 'group' ORDER BY `root` desc;";
	
	// On récupère le dernier numéro créé
	$r_sql = $db->sql_query($s_sql);
	$ta_dernier_numero_groupe = $db->sql_fetch_assoc($r_sql);
	
	return $ta_dernier_numero_groupe['root'];
}

// Fonction qui liste les groupes créé par gestionmod
function f_lister_les_groupes()
{
	// Déclaration des variables
	global $db;
	$i = 0;
	$ta_groupes = array();
	
	// On récupère le numéro du groupe et le lien de chaque groupe créé
	$s_sql = "SELECT `menu`, `root`, `admin_only` FROM `".TABLE_MOD."` WHERE `link` = 'group';";
	$r_sql = $db->sql_query($s_sql);
	
	while($row = $db->sql_fetch_assoc($r_sql)) 
	{		
		$ta_groupes[$i] = array('Nom' => $row['menu'] , 'Num' => $row['root'] , 'admin' => $row['admin_only']);
		$i++;	
	}
	
	return $ta_groupes;
}

// Transforme un nom en nom de menu (xaviernuma - 2012)
function f_traitement_nom_groupe($s_nom, $b_nouveau_groupe, $n_admin = 0)
{
	// Déclaration des variables
	$s_menu = '';
	
	if($s_nom <> '') 
	{
		if(get_magic_quotes_gpc() == 1)
		{
			$s_nom = stripslashes($s_nom);
		}
		$s_menu .= '</a><div style="';
		$s_menu .= 'background:#000;';
		$s_menu .= 'bottom:9px;';
		$s_menu .= 'position:relative;';
		$s_menu .= '">';	
		$s_menu .= '<center><b>'.$s_nom.'</b></center></div><a>';	
	
		// On vérifie si le nom n'existe pas déjà
		$ta_liste_groupes = f_lister_les_groupes();
		for($i = 0 ; $i < count($ta_liste_groupes) ; $i++)
		{
			if($b_nouveau_groupe)
			{
				if($ta_liste_groupes[$i]['Nom'] == $s_menu)
				{
					$s_menu = ''; // On met à null menu
				}
			}
			else
			{
				if(($ta_liste_groupes[$i]['Nom'] == $s_menu) and ($ta_liste_groupes[$i]['admin'] == $n_admin))
				{
					$s_menu = ''; // On met à null menu
				}
			}
		}
		
		// Le champ dans la base est de 255 caractère, on regarde si on ne dépasse pas
		if(strlen($s_menu) > 255)
		{
			$s_menu = '';
		}
	}
	
	return $s_menu;
}

// Création d'un nouveau groupe
function f_nouveau_groupe() 
{
	// Déclaration des variables
	global $db, $dir;
	$s_champs = '';
	$s_menu = ''; // Attention, limité à 255 caractères...
	$new_group = '';
	$n_groupe_admin = 0; // 0 Groupe normal, 1 Groupe admin
	
	// On test si des données ont été envoyé
	if(isset($_POST['new_group'])) 
	{
		if(isset($_POST['admin'])) 
		{
			$n_groupe_admin = 1;
		}
		$s_menu = f_traitement_nom_groupe($_POST['new_group'], true);
		if(!empty($s_menu))
		{
			// On génère le nouvel identifiant du groupe
			$num_new_group = f_dernier_numero_de_groupe();
			$num_new_group++;
			
			// Préparation de la requête
			$s_champs .= "INSERT INTO ";
			$s_champs .= "`".TABLE_MOD."` SET ";
			$s_champs .= "`id` = '', ";
			$s_champs .= "`title` = '%s', ";
			$s_champs .= "`menu` = '%s', ";
			$s_champs .= "`action` = '%s', ";
			$s_champs .= "`root` = '%s', ";
			$s_champs .= "`link` = '%s', ";
			$s_champs .= "`version` = '%s', ";
			$s_champs .= "`active` = '%s', ";
			$s_champs .= "`admin_only` = '%s' ";

			$s_sql = sprintf($s_champs,
					mysqli_real_escape_string($db->db_connect_id, "Group.".$num_new_group),
					mysqli_real_escape_string($db->db_connect_id, $s_menu),
					mysqli_real_escape_string($db->db_connect_id, $num_new_group),
					mysqli_real_escape_string($db->db_connect_id, $num_new_group),
					mysqli_real_escape_string($db->db_connect_id, 'group'),
					mysqli_real_escape_string($db->db_connect_id, 0),
					mysqli_real_escape_string($db->db_connect_id, 1),
					mysqli_real_escape_string($db->db_connect_id, $n_groupe_admin)
					);
			$db->sql_query($s_sql);
			// On met les groupes dans l'ordre
			f_lister_la_table_mod();
		}
	}
	redirection("index.php?action=gestion&subaction=group");
}

// Cette fonction à pour but de renommer ou supprimer un groupe
function f_gerer_un_groupe() 
{
	// Déclaration des variables
	global $db;
	$s_champs = '';
	
	// On vérifie si toutes les données sont bien arrivés.
	if (isset($_POST['ordre']) && isset($_POST['num_group']) && isset($_POST['nom_group']) && isset($_POST['admin'])) 
	{
		// On vérifie que le numéro de group n'est pas vide et que le champs admin soit bien un nombre (0 ou 1)
		if(($_POST['num_group'] <> '') and (is_numeric($_POST['admin']))) 
		{
			switch ($_POST['ordre']) 
			{
				case "Renommer Groupe" :
					$s_menu = f_traitement_nom_groupe($_POST['nom_group'], false, $_POST['admin']);
					if(!empty($s_menu))
					{	
						// Préparation de la requête
						$s_champs .= "UPDATE ";
						$s_champs .= "`".TABLE_MOD."` SET ";
						$s_champs .= "`menu` = '%s', ";
						$s_champs .= "`admin_only` = '%s' ";
						$s_champs .= "WHERE ";
						$s_champs .= "`title` = '%s';";
	
						$s_sql = sprintf($s_champs,
								mysqli_real_escape_string($db->db_connect_id, $s_menu),
								mysqli_real_escape_string($db->db_connect_id, $_POST['admin']), // 0 Groupe normal, 1 Groupe admin
								mysqli_real_escape_string($db->db_connect_id, 'Group.'.$_POST['num_group'])
								);
						$db->sql_query($s_sql);
					}
					break;
				
				case "Supprimer Groupe" :
					$s_sql = "DELETE FROM ".TABLE_MOD." WHERE title = 'Group.".$_POST['num_group']."' ";
					$db->sql_query($s_sql);
					
					// On met les groupes dans l'ordre
					f_lister_la_table_mod();
					break;
			}
		}
	}
	redirection("index.php?action=gestion&subaction=group");
}	

// Récupère le contenu de la table mod		
function f_lister_la_table_mod() 
{
	// Déclaration des variables
	global $db;
	$i = 1;
	$type = 0; // Si 0 : mod, si 1 : groupe
	
	$s_sql = "SELECT * FROM ".TABLE_MOD." ORDER BY position";
	$r_sql = $db->sql_query($s_sql);
	
	while($row = $db->sql_fetch_assoc($r_sql)) 
	{
		if($row['link'] == 'group') 
		{
			$type = 1;
		}
		
		$ta_liste_des_mods[$i] = array('menu' => $row['menu'], 'position' => $i, 'type' => $type, 'id' => $row['id'] , 'title' => $row['title'], 'version' => $row['version'], 'active' => $row['active'], 'admin_only' => $row['admin_only']);
		
		// on met à jour les positions dans l'ordre croissant, cela permet d'avoir une suite de position sans 'trou' dans le cas ou un mod/groupe est supprimé
		$s_sql = "UPDATE ".TABLE_MOD." SET position='".$i."' WHERE id = '".$row["id"]."' ";
		$db->sql_query($s_sql);
		
		$type = 0;
		$i++;	
	}
	return $ta_liste_des_mods;
}

// Récupère le nom du groupe à partir du nom de groupe stocké dans la BDD (avec le code HTML/CSS personalisé)
function f_nom_du_groupe($s_menu) 
{
	// Déclaration des variables
	$t_resultats = array();
	$s_nom_du_groupe = '';
	
	// Ancien pattern pour ceux qui ont créé nom de groupe avant la mise à jour du mod
	if(preg_match("#^.*?<a href=\"\"><u>(.*?)<\/u><\/a>.*$#", $s_menu, $t_resultats)) 
	{
		$s_nom_du_groupe = $t_resultats[1];
	}
	elseif(preg_match("#<center><b>(.*?)</b></center>#", $s_menu, $t_resultats)) 
	{
		 $s_nom_du_groupe = $t_resultats[1];
	}
	return $s_nom_du_groupe;
}	

// Met à jour les positions des mods/groupes et renomme les modules
function f_gerer_mod() 
{
	// Déclaration des variables
	global $db;
	
	// On vérifie si toutes les données sont bien arrivés.
	if(isset($_POST['module_range']) && isset($_POST['ordre']) || (isset($_POST['menu'])))
	{
		switch ($_POST['ordre']) 
		{
			case "maj": // On met à jour les id des modules dans le nouvel ordre de rangement
				$t_id_modules = explode( ',' , $_POST['module_range']);
				
				for( $i = 0 ; $i < count($t_id_modules) ; $i++)
				{
					$s_champs = "UPDATE ";
					$s_champs .= "`".TABLE_MOD."` SET ";
					$s_champs .= "`position` = '%s' ";
					$s_champs .= "WHERE id = '%s';";

					$s_sql = sprintf($s_champs,
							mysqli_real_escape_string($db->db_connect_id, $i),
							mysqli_real_escape_string($db->db_connect_id, $t_id_modules[$i])
							);
					$r_sql = $db->sql_query($s_sql);
				}
				break;	
			case "Renommer": // On renomme un module
				$b_existant = false;
				
				$ta_liste_des_mods = f_lister_la_table_mod();
				for($i = 0 ; $i < count($ta_liste_des_mods) ; $i++)
				{
					if($ta_liste_des_mods[$i]['menu'] == $_POST['menu'])
					{
						$b_existant = true;
					}
				}
				
				if(!$b_existant)
				{
					if(get_magic_quotes_gpc () == 1)
					{
						$_POST['menu'] = stripslashes($_POST['menu']);
					}
					$s_champs = "UPDATE ";
					$s_champs .= "`".TABLE_MOD."` SET ";
					$s_champs .= "`menu` = '%s' ";
					$s_champs .= "WHERE id = '%s';";

					$s_sql = sprintf($s_champs,
							mysqli_real_escape_string($db->db_connect_id, $_POST['menu']),
							mysqli_real_escape_string($db->db_connect_id, $_POST['id'])
							);
					$r_sql = $db->sql_query($s_sql);
				}
				break;
		}
	}
	
	if(isset($_POST['page']))
	{	
		redirection("index.php?action=gestion&subaction=".$_POST['page']);
	}
	else 
	{
		redirection("index.php?action=gestion");
	}
}

// Affichage de l'aide pour les balises HTML (xaviernuma - 2012)
function f_aide_html()
{
	$s_html = '';
	$s_html .= '<div id="aide" style="display:block;font-size: 12px;width: 795px;text-align:left;background-image:url(\'skin/OGSpy_skin/tableaux/th.png\');background-repeat:repeat;">';
	$s_html .= 	'Voici quelques exemples de code HTML que vous pouvez utiliser pour changer le style d\'écriture.';
	$s_html .= 	'<ul>';
	$s_html .= 		'<li>'.htmlentities ('<font color="red">Gestion des attaques</font>').' : <font color="red">Gestion des attaques</font><br>';
	$s_html .= 		'<li>'.htmlentities ('<font size="3">Gestion des attaques</font>').' : <font size="3">Gestion des attaques</font><br>';
	$s_html .= 		'<li>'.htmlentities ('<br>').' : Aller à la ligne.<br>';
	$s_html .= 		'<li>'.htmlentities ('<u>Gestion des attaques</u>').' : <u>Gestion des attaques</u><br>';
	$s_html .= 		'<li>'.htmlentities ('<i>Gestion des attaques</i>').' : <i>Gestion des attaques</i><br>';
	$s_html .= 		'<li>'.htmlentities ('<b>Gestion des attaques</b>').' : <b>Gestion des attaques</b><br>';
	$s_html .= 	'</ul>';
	$s_html .= '</div>';
	
	return $s_html;
}

?>