<?php

/**
* function.php Fichier avec les fonctions
* @package Gestion MOD
* @author Kal Nightmare
* @update xaviernuma - 2012
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

// Retourne le dernier num�ro de groupe qui � �t� cr��.
function f_dernier_numero_de_groupe() 
{
	// D�claration des variables
	global $db;
	
	// On s�lectionne les num�ros de groupe dans la table root
	$s_sql = "SELECT `root` FROM `".TABLE_MOD."` WHERE `link` = 'group' ORDER BY `root` desc;";
	
	// On r�cup�re le dernier num�ro cr��
	$r_sql = $db->sql_query($s_sql);
	$ta_dernier_numero_groupe = $db->sql_fetch_assoc($r_sql);
	
	return $ta_dernier_numero_groupe['root'];
}

// Fonction qui liste les groupes cr�� par gestionmod
function f_lister_les_groupes()
{
	// D�claration des variables
	global $db;
	$i = 0;
	$ta_groupes = array();
	
	// On r�cup�re le num�ro du groupe et le lien de chaque groupe cr��
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
	// D�claration des variables
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
	
		// On v�rifie si le nom n'existe pas d�j�
		$ta_liste_groupes = f_lister_les_groupes();
		for($i = 0 ; $i < count($ta_liste_groupes) ; $i++)
		{
			if($b_nouveau_groupe)
			{
				if($ta_liste_groupes[$i]['Nom'] == $s_menu)
				{
					$s_menu = ''; // On met � null menu
				}
			}
			else
			{
				if(($ta_liste_groupes[$i]['Nom'] == $s_menu) and ($ta_liste_groupes[$i]['admin'] == $n_admin))
				{
					$s_menu = ''; // On met � null menu
				}
			}
		}
		
		// Le champ dans la base est de 255 caract�re, on regarde si on ne d�passe pas
		if(strlen($s_menu) > 255)
		{
			$s_menu = '';
		}
	}
	
	return $s_menu;
}

// Cr�ation d'un nouveau groupe
function f_nouveau_groupe() 
{
	// D�claration des variables
	global $db, $dir;
	$s_champs = '';
	$s_menu = ''; // Attention, limit� � 255 caract�res...
	$new_group = '';
	$n_groupe_admin = 0; // 0 Groupe normal, 1 Groupe admin
	
	// On test si des donn�es ont �t� envoy�
	if(isset($_POST['new_group'])) 
	{
		if(isset($_POST['admin'])) 
		{
			$n_groupe_admin = 1;
		}
		$s_menu = f_traitement_nom_groupe($_POST['new_group'], true);
		if(!empty($s_menu))
		{
			// On g�n�re le nouvel identifiant du groupe
			$num_new_group = f_dernier_numero_de_groupe();
			$num_new_group++;
			
			// Pr�paration de la requ�te
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
					mysql_real_escape_string("Group.".$num_new_group),
					mysql_real_escape_string($s_menu),
					mysql_real_escape_string($num_new_group),
					mysql_real_escape_string($num_new_group),
					mysql_real_escape_string('group'),
					mysql_real_escape_string(0),
					mysql_real_escape_string(1),
					mysql_real_escape_string($n_groupe_admin)
					);
			$db->sql_query($s_sql);
			// On met les groupes dans l'ordre
			f_lister_la_table_mod();
		}
	}
	redirection("index.php?action=gestion&subaction=group");
}

// Cette fonction � pour but de renommer ou supprimer un groupe
function f_gerer_un_groupe() 
{
	// D�claration des variables
	global $db;
	$s_champs = '';
	
	// On v�rifie si toutes les donn�es sont bien arriv�s.
	if (isset($_POST['ordre']) && isset($_POST['num_group']) && isset($_POST['nom_group']) && isset($_POST['admin'])) 
	{
		// On v�rifie que le num�ro de group n'est pas vide et que le champs admin soit bien un nombre (0 ou 1)
		if(($_POST['num_group'] <> '') and (is_numeric($_POST['admin']))) 
		{
			switch ($_POST['ordre']) 
			{
				case "Renommer Groupe" :
					$s_menu = f_traitement_nom_groupe($_POST['nom_group'], false, $_POST['admin']);
					if(!empty($s_menu))
					{	
						// Pr�paration de la requ�te
						$s_champs .= "UPDATE ";
						$s_champs .= "`".TABLE_MOD."` SET ";
						$s_champs .= "`menu` = '%s', ";
						$s_champs .= "`admin_only` = '%s' ";
						$s_champs .= "WHERE ";
						$s_champs .= "`title` = '%s';";
	
						$s_sql = sprintf($s_champs,
								mysql_real_escape_string($s_menu),
								mysql_real_escape_string($_POST['admin']), // 0 Groupe normal, 1 Groupe admin
								mysql_real_escape_string('Group.'.$_POST['num_group'])
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

// R�cup�re le contenu de la table mod		
function f_lister_la_table_mod() 
{
	// D�claration des variables
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
		
		// on met � jour les positions dans l'ordre croissant, cela permet d'avoir une suite de position sans 'trou' dans le cas ou un mod/groupe est supprim�
		$s_sql = "UPDATE ".TABLE_MOD." SET position='".$i."' WHERE id = '".$row["id"]."' ";
		$db->sql_query($s_sql);
		
		$type = 0;
		$i++;	
	}
	return $ta_liste_des_mods;
}

// R�cup�re le nom du groupe � partir du nom de groupe stock� dans la BDD (avec le code HTML/CSS personalis�)
function f_nom_du_groupe($s_menu) 
{
	// D�claration des variables
	$t_resultats = array();
	$s_nom_du_groupe = '';
	
	// Ancien pattern pour ceux qui ont cr�� nom de groupe avant la mise � jour du mod
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

// Met � jour les positions des mods/groupes et renomme les modules
function f_gerer_mod() 
{
	// D�claration des variables
	global $db;
	
	// On v�rifie si toutes les donn�es sont bien arriv�s.
	if(isset($_POST['module_range']) && isset($_POST['ordre']) || (isset($_POST['menu'])))
	{
		switch ($_POST['ordre']) 
		{
			case "maj": // On met � jour les id des modules dans le nouvel ordre de rangement
				$t_id_modules = explode( ',' , $_POST['module_range']);
				
				for( $i = 0 ; $i < count($t_id_modules) ; $i++)
				{
					$s_champs = "UPDATE ";
					$s_champs .= "`".TABLE_MOD."` SET ";
					$s_champs .= "`position` = '%s' ";
					$s_champs .= "WHERE id = '%s';";

					$s_sql = sprintf($s_champs,
							mysql_real_escape_string($i),
							mysql_real_escape_string($t_id_modules[$i])
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
							mysql_real_escape_string($_POST['menu']),
							mysql_real_escape_string($_POST['id'])
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
	$s_html .= 	'Voici quelques exemples de code HTML que vous pouvez utiliser pour changer le style d\'�criture.';
	$s_html .= 	'<ul>';
	$s_html .= 		'<li>'.htmlentities ('<font color="red">Gestion des attaques</font>').' : <font color="red">Gestion des attaques</font><br>';
	$s_html .= 		'<li>'.htmlentities ('<font size="3">Gestion des attaques</font>').' : <font size="3">Gestion des attaques</font><br>';
	$s_html .= 		'<li>'.htmlentities ('<br>').' : Aller � la ligne.<br>';
	$s_html .= 		'<li>'.htmlentities ('<u>Gestion des attaques</u>').' : <u>Gestion des attaques</u><br>';
	$s_html .= 		'<li>'.htmlentities ('<i>Gestion des attaques</i>').' : <i>Gestion des attaques</i><br>';
	$s_html .= 		'<li>'.htmlentities ('<b>Gestion des attaques</b>').' : <b>Gestion des attaques</b><br>';
	$s_html .= 	'</ul>';
	$s_html .= '</div>';
	
	return $s_html;
}

?>