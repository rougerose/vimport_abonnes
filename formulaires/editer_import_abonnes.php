<?php
/**
 * Gestion du formulaire de d'édition de import_abonne
 *
 * @plugin     Importer des abonnés
 * @copyright  2018
 * @author     Christophe Le Drean
 * @licence    GNU/GPL v3
 * @package    SPIP\Vimport_abonnes\Formulaires
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/actions');
include_spip('inc/editer');


/**
 * Identifier le formulaire en faisant abstraction des paramètres qui ne représentent pas l'objet edité
 *
 * @param int|string $id_import_abonne
 *     Identifiant du import_abonne. 'new' pour un nouveau import_abonne.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un import_abonne source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du import_abonne, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return string
 *     Hash du formulaire
 */
function formulaires_editer_import_abonne_identifier_dist($id_import_abonne = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	return serialize(array(intval($id_import_abonne)));
}

/**
 * Chargement du formulaire d'édition de import_abonne
 *
 * Déclarer les champs postés et y intégrer les valeurs par défaut
 *
 * @uses formulaires_editer_objet_charger()
 *
 * @param int|string $id_import_abonne
 *     Identifiant du import_abonne. 'new' pour un nouveau import_abonne.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un import_abonne source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du import_abonne, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Environnement du formulaire
 */
function formulaires_editer_import_abonne_charger_dist($id_import_abonne = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$valeurs = formulaires_editer_objet_charger('import_abonne', $id_import_abonne, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	return $valeurs;
}

/**
 * Vérifications du formulaire d'édition de import_abonne
 *
 * Vérifier les champs postés et signaler d'éventuelles erreurs
 *
 * @uses formulaires_editer_objet_verifier()
 *
 * @param int|string $id_import_abonne
 *     Identifiant du import_abonne. 'new' pour un nouveau import_abonne.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un import_abonne source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du import_abonne, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Tableau des erreurs
 */
function formulaires_editer_import_abonne_verifier_dist($id_import_abonne = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$erreurs = array();

	$erreurs = formulaires_editer_objet_verifier('import_abonne', $id_import_abonne);

	return $erreurs;
}

/**
 * Traitement du formulaire d'édition de import_abonne
 *
 * Traiter les champs postés
 *
 * @uses formulaires_editer_objet_traiter()
 *
 * @param int|string $id_import_abonne
 *     Identifiant du import_abonne. 'new' pour un nouveau import_abonne.
 * @param string $retour
 *     URL de redirection après le traitement
 * @param int $lier_trad
 *     Identifiant éventuel d'un import_abonne source d'une traduction
 * @param string $config_fonc
 *     Nom de la fonction ajoutant des configurations particulières au formulaire
 * @param array $row
 *     Valeurs de la ligne SQL du import_abonne, si connu
 * @param string $hidden
 *     Contenu HTML ajouté en même temps que les champs cachés du formulaire.
 * @return array
 *     Retours des traitements
 */
function formulaires_editer_import_abonne_traiter_dist($id_import_abonne = 'new', $retour = '', $lier_trad = 0, $config_fonc = '', $row = array(), $hidden = '') {
	$retours = formulaires_editer_objet_traiter('import_abonne', $id_import_abonne, '', $lier_trad, $retour, $config_fonc, $row, $hidden);
	return $retours;
}
