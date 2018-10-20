<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Importer des abonnés
 * @copyright  2018
 * @author     Christophe Le Drean
 * @licence    GNU/GPL v3
 * @package    SPIP\Vimport_abonnes\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function vimport_abonnes_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['import_abonnes'] = 'import_abonnes';

	return $interfaces;
}


/**
 * Déclaration des objets éditoriaux
 *
 * @pipeline declarer_tables_objets_sql
 * @param array $tables
 *     Description des tables
 * @return array
 *     Description complétée des tables
 */
function vimport_abonnes_declarer_tables_objets_sql($tables) {

	$tables['spip_import_abonnes'] = array(
		'type' => 'import_abonne',
		'principale' => 'oui',
		'table_objet_surnoms' => array('importabonne'), // table_objet('import_abonne') => 'import_abonnes' 
		'field'=> array(
			'id_import_abonne'   => 'bigint(21) NOT NULL',
			'id_auteur'          => 'bigint(21) NOT NULL DEFAULT 0',
			'id_abonnement'      => 'bigint(21) NOT NULL DEFAULT 0',
			'nom'                => 'text NOT NULL DEFAULT ""',
			'prenom'             => 'text NOT NULL DEFAULT ""',
			'email'              => 'tinytext NOT NULL DEFAULT ""',
			'organisation'       => 'text NOT NULL DEFAULT ""',
			'service'            => 'text NOT NULL DEFAULT ""',
			'voie'               => 'text NOT NULL DEFAULT ""',
			'complement'         => 'text NOT NULL DEFAULT ""',
			'boite_postale'      => 'varchar(40) NOT NULL DEFAULT ""',
			'code_postal'        => 'varchar(40) NOT NULL DEFAULT ""',
			'ville'              => 'tinytext NOT NULL DEFAULT ""',
			'region'             => 'varchar(40) NOT NULL DEFAULT ""',
			'pays'               => 'varchar(40) NOT NULL DEFAULT ""',
			'code_facteur' => 'varchar(40) NOT NULL DEFAULT ""',
			'numero_debut'       => 'tinytext NOT NULL DEFAULT ""',
			'numero_fin'         => 'tinytext NOT NULL DEFAULT ""',
			'duree'              => 'varchar(25) NOT NULL DEFAULT ""',
			'type_abonnement'    => 'text NOT NULL DEFAULT ""',
			'reabonnement' => 'varchar(25) NOT NULL DEFAULT ""',
			'nombre_abonnement' => 'varchar(25) NOT NULL DEFAULT ""',
			'montant'            => 'varchar(25) NOT NULL DEFAULT ""',
			'paiement'           => 'text NOT NULL DEFAULT ""',
			'date_abonnement'    => 'datetime NOT NULL DEFAULT "0000-00-00 00:00:00"',
			'maj'                => 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY'        => 'id_import_abonne',
		),
		'titre' => 'nom AS titre, "" AS lang',
		 #'date' => '',
		 'champs_editables'  => array('id_auteur', 'id_abonnement', 'nom', 'prenom', 'email', 'organisation', 'service', 'voie', 'complement', 'boite_postale', 'code_postal', 'ville', 'region', 'pays', 'numero_debut', 'numero_fin', 'duree', 'type_abonnement', 'montant', 'paiement', 'reabonnement', 'nombre_abonnement'),
 		'champs_versionnes' => array('id_auteur', 'id_abonnement', 'nom', 'prenom', 'email', 'organisation', 'service', 'voie', 'complement', 'boite_postale', 'code_postal', 'ville', 'region', 'pays', 'numero_debut', 'numero_fin', 'duree', 'type_abonnement', 'montant', 'paiement', 'date_abonnement',  'reabonnement', 'nombre_abonnement'),
 		'rechercher_champs' => array(),
 		'tables_jointures'  => array(),


	);

	return $tables;
}
