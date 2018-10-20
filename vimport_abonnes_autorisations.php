<?php
/**
 * Définit les autorisations du plugin Importer des abonnés
 *
 * @plugin     Importer des abonnés
 * @copyright  2018
 * @author     Christophe Le Drean
 * @licence    GNU/GPL v3
 * @package    SPIP\Vimport_abonnes\Autorisations
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Fonction d'appel pour le pipeline
 * @pipeline autoriser */
function vimport_abonnes_autoriser() {
}


/**
 * Faire l'import 
 * 
 */
function autoriser_vimporter_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre' ,'', '', $qui);
}


/**
 * Voir le menu d'importation
 * 
 */
function autoriser_vimporterabonnes_menu_dist($faire, $type, $id, $qui, $opt){
	return autoriser('webmestre', '', '', $qui);
}


/**
 * Autorisation de créer (importabonne)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_importabonne_creer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', '', '', $qui);
}

/**
 * Autorisation de voir (importabonne)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_importabonne_voir_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', '', '', $qui);
}

/**
 * Autorisation de modifier (importabonne)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_importabonne_modifier_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', '', '', $qui);
}

/**
 * Autorisation de supprimer (importabonne)
 *
 * @param  string $faire Action demandée
 * @param  string $type  Type d'objet sur lequel appliquer l'action
 * @param  int    $id    Identifiant de l'objet
 * @param  array  $qui   Description de l'auteur demandant l'autorisation
 * @param  array  $opt   Options de cette autorisation
 * @return bool          true s'il a le droit, false sinon
**/
function autoriser_importabonne_supprimer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', '', '', $qui);
}
