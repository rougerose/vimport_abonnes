<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


function importer_normaliser_date($date) {
	
	if (!strlen($date)) {
		return $date;
	}

	include_spip('inc/filtres_dates');
	
	if (preg_match('/^([0-9]{4})-([0-9]{1,2})$/', $date, $regs)) {
		$mois = str_pad($regs[2], 2, 0, STR_PAD_LEFT);
		$date = $regs[1].'-'.$mois.'-1';
	}
	
	$date_array = recup_date($date);
	
	if (!is_array($date_array)) {
		return $date = '';
	}
	
	list($annee, $mois, $jour, $heures, $minutes, $secondes) = $date_array;
	$time = mktime($heures, $minutes, $secondes, $mois, $jour, $annee);
	$date = date('Y-m-d H:i:s', $time);
	
	return $date;
}


function importer_reference_abonnement($duree, $type_abonnement) {
	// Calcul de la référence d'abonnement
	switch ($duree) {
		case '12':
			$reference_duree = 'A1';
			break;
		
		case '24':
			$reference_duree = 'A2';
			break;
		
		default:
			$reference_duree = 'A1';
			break;
	}
	
	switch ($type_abonnement) {
		case 'REDF':
			$reference_type_abonnement = 'T1F';
			break;
		
		case 'REDI':
			$reference_type_abonnement = 'T1I';
			break;
			
		case 'STDF':
		  	$reference_type_abonnement = 'T2F';
			break;
		
		case 'STDI':
			$reference_type_abonnement = 'T2I';
			break;
		
		case 'SOUF':
			$reference_type_abonnement = 'T3F';
			break;
		
		case 'SOUI':
			$reference_type_abonnement = 'T3I';
			break;
		
		default:
			$reference_type_abonnement = 'T2F';
			break;
	}
	
	$reference = $reference_duree.$reference_type_abonnement;
	
	return $reference;
}
