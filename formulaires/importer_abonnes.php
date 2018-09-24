<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function formulaires_importer_abonnes_saisies_dist() {
	$saisies = array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'vimport_abonnes_fichier',
				'label' => 'Fichier CSV',
				'type' => 'file'
			)
		)
	);
	
	return $saisies;
}

function formulaires_importer_abonnes_charger_dist() {
	$valeurs = array(
		'import_fichier' => '',
	);
	
	return $valeurs;
}

function formulaires_importer_abonnes_verifier_dist() {
	$erreurs = array();
	
	if ($_FILES['vimport_abonnes_fichier']['name'] == '') {
		$erreurs['message_erreur'] = "Erreur : fichier manquant";
	}
	
	return $erreurs;
}

function formulaires_importer_abonnes_traiter_dist() {
	$res = array();
	
	if ($_FILES['vimport_abonnes_fichier']['name'] != '') {
		
		$dir = sous_repertoire(_DIR_VAR, 'vimport_abonnes');
		$hash = md5('vimport-' . $GLOBALS['visiteur_session']['id_auteur'] . time());
		$fichier = $dir . $hash . '-' . $_FILES['vimport_abonnes_fichier']['name'];
		move_uploaded_file($_FILES['vimport_abonnes_fichier']['tmp_name'], $fichier);
		
		$importer_csv = charger_fonction('importer_csv', 'inc');
		$datas = $importer_csv($fichier, true);
	}
	
	if (!count($datas)) {
		return $res['message_erreur'] = "Aucune donnée à importer.";
	}
	
	// noter le nombre d'erreurs
	$er = 0;
	
	include_spip('inc/importer');
	include_spip('action/editer_objet');
	include_spip('action/editer_contact');
	include_spip('action/editer_liens');
	include_spip('action/inscrire_auteur');
	include_spip('outils/migration_nom_pays');
	include_spip('inc/acces');
	
	foreach ($datas as $data) {
		
		// données
		// 
		// organisation ; service ; prenom ; nom ; 
		// complement ; voie ; boite_postale ; code_postal ; ville ; code_facteur ; pays ; 
		// email ; reabonnement ; nombre_abonnement ;
		// numero_debut ; numero_fin ; 
		// montant ; date_abonnement ; paiement ; duree ; type_abonnement
		// 
		
		unset($id_import_abonne, $id_doublon, $auteur, $id_auteur, $nom, $prenom, $email, $login, $set_auteur, $contact, $id_contact, $adresse, $id_adresse, $code_pays, $nom_pays, $numero_debut, $numero_fin, $pays, $date_abonnement, $debut_fin, $log_abonnement, $set_adresse, $set_abonnement, $reference, $abonnements_offre, $statut, $id_abonnement, $log);
		
		
		$data = array_map('trim', $data);
		
		// date
		$data['date_abonnement'] = importer_normaliser_date($data['date_abonnement']);
		
		// nom
		if (!strlen($data['nom'])) {
			
			if (strlen($data['organisation'])) {
				$data['nom'] = $data['organisation'];
				// Garder uniquement un nom d'auteur
				$data['organisation'] = '';
				
			} elseif (strlen($data['service'])) {
				$data['nom'] = $data['service'];
				$data['service'] = '';
				
			} else {
				$res['erreurs'] = ++$er;
				spip_log("Pas de nom, ni d'organisation ou de service dans les données CSV. Ces données n'ont pas été importées dans la base : ".var_export($data, true), "import_abonnes"._LOG_ERREUR);
				
				continue; // abonné suivant
			}
		}
		
		// Doublon éventuel dans la table import_abonnes
		if (
			(strlen($data['email']) 
				and $id_doublon = sql_getfetsel('id_import_abonne', 'spip_import_abonnes', 'email='.sql_quote($data['email']))) 
			or (!strlen($data['email']) and $id_doublon = sql_getfetsel('id_import_abonne', 'spip_import_abonnes', 'nom='.sql_quote($data['nom']).' AND prenom='.sql_quote($data['prenom'])))
		) {
			$res['erreurs'] = ++$er;
			$id_import_abonne = objet_inserer('import_abonnes', null, $data);
			spip_log("Abonné en double dans la table import_abonnes. Déjà enregistré id_import #$id_doublon. Les données en double été enregistrées #$id_import_abonne mais sans traitement supplémentaire.", "import_abonnes"._LOG_ERREUR);
			
			continue; // abonné suivant
		} else {
			// Enregistrer les données
			$id_import_abonne = objet_inserer('import_abonnes', null, $data);
		}
		
		// Doublon éventuel dans la table auteurs
		if (strlen($data['email']) and $auteur = sql_fetsel('id_auteur, nom', 'spip_auteurs', 'email='.sql_quote($data['email']))) {
			// Comparer les nom et prénom avec suppression des accents et diacritiques
			$nom_base = strtolower(vprofils_supprimer_accents(nom($auteur['nom'])));
			$prenom_base = strtolower(vprofils_supprimer_accents(prenom($auteur['nom'])));
			$nom_import = strtolower(vprofils_supprimer_accents($data['nom']));
			$prenom_import = strtolower(vprofils_supprimer_accents($data['prenom']));
			
			// S'il existe des différences, noter l'erreur
			// et passer à l'abonné suivant.
			if (strcasecmp($nom_base, $nom_import) != 0 or strcasecmp($prenom_base, $prenom_import) != 0) {
				$res['erreurs'] = ++$er;
				spip_log("Auteur en double. L'auteur #".$auteur['id_auteur']." est déjà enregistré, mais le nom ou le prénom sont différents. Données importées : ".var_export($data, true), "import_abonnes"._LOG_ERREUR);
				
				// poursuivre avec l'abonné suivant
				continue;
			} else {
				// La comparaison est correcte, on poursuit le traitement
				$id_auteur = $auteur['id_auteur'];
				$nom = nom($auteur['nom']);
				$prenom = prenom($auteur['nom']);
				$type_adresse = _ADRESSE_TYPE_DEFAUT;
				// Chercher le contact de cet auteur déjà existant
				// et s'il existe, noter pour vérification.
				if ($contact = sql_fetsel('*', 'spip_contacts', 'id_auteur='.intval($id_auteur))) {
					$res['erreurs'] = ++$er;
					spip_log("Contact déjà existant. L'auteur #$id_auteur a déjà un id_contact #".$contact['id_contact'].". Les données de contact déjà présentes n'ont pas été modifiées. Données importées : ".var_export($data, true), "import_abonnes"._LOG_ERREUR);
				}
				
				// Chercher l'adresse de cet auteur
				// et si elle existe, noter pour vérification.
				if ($adresse = sql_fetsel('*', 'spip_adresses AS adresses INNER JOIN spip_adresses_liens AS L1 ON (L1.id_adresse = adresses.id_adresse)', 'L1.id_objet='.intval($id_auteur).' AND L1.objet='.sql_quote('auteur').' AND L1.type='.sql_quote($type_adresse))) {
					$res['erreurs'] = ++$er;
					spip_log("Adresse déjà existante pour l'auteur #$id_auteur. L'adresse existante n'a pas été modifiée. Données importées : ".var_export($data, true), "import_abonnes"._LOG_ERREUR);
				}
			}
		} else {
			// Créer l'auteur
			$nom = $data['nom'].'*'.$data['prenom'];
			$email = $data['email'];
			
			if (strlen($email)) {
				$login = $email;
			} else {
				// Pas de mail : créer un login à partir du nom.
				$login = test_login($nom, $email);
			}
			
			$set_auteur = array(
				'nom' => $nom,
				'email' => $email,
				'statut' => '6forum',
				'login' => $login,
				'pass' => creer_pass_aleatoire()
			);
			
			$id_auteur = objet_inserer('auteur', null, $set_auteur);
			$prenom = prenom($nom);
			$nom = nom($nom);
			
			if ($id_auteur and $id_import_abonne) {
				objet_modifier('import_abonnes', $id_import_abonne, array('id_auteur' => intval($id_auteur)));
			}
		}
		
		// Créer le contact
		if (!isset($contact) or !$contact) {
			if (!isset($definir_contact)) {
				$definir_contact = charger_fonction('definir_contact', 'action');
			}
			$id_contact = $definir_contact('contact/'.intval($id_auteur));
			
			contact_modifier($id_contact, $set = array(
				'civilite' => '', // civilité toujours absente du fichier
				'prenom' => $prenom,
				'nom' => $nom)
			);
		}
		
		// Créer l'adresse
		if (!isset($adresse) or !$adresse) {
			// Code pays
			$pays = (strlen($data['pays'])) ? $data['pays'] : "France";
			$nom_pays = homogeneiser_nom_pays($pays);
			$code_pays = sql_getfetsel('code', 'spip_pays', 'nom LIKE '.sql_quote("%$nom_pays%"));
			$type_adresse = _ADRESSE_TYPE_DEFAUT;
			
			$set_adresse = array(
				'organisation' => $data['organisation'],
				'service' => $data['service'],
				'voie' => $data['voie'],
				'complement' => $data['complement'],
				'boite_postale' => $data['boite_postale'],
				'code_postal' => $data['code_postal'],
				'ville' => $data['ville'],
				'pays' => $code_pays,
				'code_facteur' => $data['code_facteur'],
			);
			
			$id_adresse = objet_inserer('adresse', null, $set_adresse);
			
			objet_associer(
				array('adresse' => $id_adresse),
				array('auteur' => $id_auteur),
				array('type' => $type_adresse)
			);
		}
		
		
		// Créer l'abonnement
		$reference = importer_reference_abonnement($data['duree'], $data['type_abonnement']);
		
		$abonnements_offre = sql_fetsel('*', 'spip_abonnements_offres', 'reference='.sql_quote($reference));
		
		include_spip('inc/vabonnements_calculer_debut_fin');
		$numero_debut = $data['numero_debut'];
		$numero_fin = $data['numero_fin'];
		$debut_fin = vabonnements_calculer_debut_fin($abonnements_offre['id_abonnements_offre'], $numero_debut);
		
		// Noter si le numéro de fin est incohérent entre les données 
		// importées et les données calculées.
		if ($numero_fin != $debut_fin['numero_fin']) {
			$res['erreurs'] = ++$er;
			spip_log("Numéro de fin d'abonnement incohérent. Auteur #$id_auteur ; id_import_abonne #$id_import_abonne. Le numéro de fin d'abonnement importé : $numero_fin ; numéro de fin calculé : ".$debut_fin['numero_fin'], "import_abonnes"._LOG_ERREUR);
		}
		
		// Si la date d'abonnement est absente, utiliser la date de début
		// d'abonnement calculée.
		$date_abonnement = ($data['date_abonnement']) ? $data['date_abonnement'] : $debut_fin['date_debut'];
		
		// Log
		include_spip('inc/vabonnements');
		$log = "Ajout de l'abonnement à partir du fichier excel.";
		
		if (strlen($data['reabonnement'])) {
			$log .= " Réabonnement : ".$data['reabonnement'].".";
		}
		
		if (strlen($data['nombre_abonnement'])) {
			$log .= " Nombre d'abonnement : ".$data['nombre_abonnement'].".";
		}
		
		if (strlen($data['montant'])) {
			$log .= " Montant versé : ".$data['montant'].".";
		}
		
		if (strlen($data['paiement'])) {
			$log .= " Mode de paiement : ".$data['paiement'].".";
		}
		
		$log_abonnement = vabonnements_log($log);
		
		// Statut
		if (!isset($numero_encours)) {
			$numero_encours = sql_getfetsel(
				'reference', 
				'spip_rubriques', 
				'statut='.sql_quote('publie').' AND id_parent='.sql_quote('115'), 
				'', 
				'titre DESC'
			);
		}
		

		
		if (($numero_debut <= $numero_encours and $numero_fin >= $numero_encours) or ($numero_debut >= $numero_encours and $numero_fin >= $numero_encours)) {
			$statut = 'actif';
		} else {
			$statut = 'resilie';
		}
		
		$set_abonnement = array(
			'id_abonnements_offre' => $abonnements_offre['id_abonnements_offre'],
			'id_auteur' => $id_auteur,
			'date' => $date_abonnement,
			'date_debut' => $debut_fin['date_debut'],
			'date_fin' => $debut_fin['date_fin'],
			'numero_debut' => $numero_debut,
			'numero_fin' => $debut_fin['numero_fin'],
			'duree_echeance' => $abonnements_offre['duree'],
			'prix_echeance' => $abonnements_offre['prix_ht'],
			'log' => $log_abonnement,
			'statut' => $statut,
			'offert' => 'non',
			'completer' => false, // ne pas utiliser la fonction completer
		);
		
		$id_abonnement = objet_inserer('abonnement', null, $set_abonnement);
		
		if ($id_abonnement and $id_import_abonne) {
			objet_modifier('import_abonnes', $id_import_abonne, array('id_abonnement' => intval($id_abonnement)));
		}
	}
	
	if ($res['erreurs']) {
		$res['message_erreur'] = "L'importation du fichier a provoqué ".$res['erreurs']." erreur(s). Vérifier les logs";
		unset($res['erreurs']);
	}
	
	if (!$res['message_erreur']) {
		$res['message_ok'] = "L'importation du fichier est terminée. Aucune erreur.";
		$res['editable'] = true;
	}
	
	return $res;
}
