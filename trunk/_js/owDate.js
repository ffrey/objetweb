/**
 * compare date d'expiration ? date d'aujourd'hui : format yyyymm (exemple : 201204 < 201110)
 * 
 * 
 * @param mois  mixed : 1 to 12 (works with 01 to 12 also)
 * @param annee mixed : 2 to 4 digits (exemple : 02 or 2002) ! if only 2 digits => 2000 is assumed as millenary !
 * @param optional jour mixed : 1 to 2 digits : 1 to 31
 * @return boolean
 */
function isDateFuture(annee, mois, jour)
{
	var DB = true;
	var mois  = parseInt(mois, 10);
	var annee = parseInt(annee, 10);
	if (2000 > annee) {
		annee = 2000 + annee;	
	}
	if (undefined == jour) {
		jour = 1;
	}
	if (isNaN(annee) ) {
		if (DB) { console.log('isDateFuture : ERREUR ('+annee+')'); }
		return false;
	}
	var date_exp = new Date(annee, mois-1, jour);
	var date_auj = new Date();
	if (DB) {
		console.log('arguements recus => jour : ' + jour + '/mois : '+ mois + '/annee : ' + annee);
		console.log('date testee : ' + date_exp.toLocaleString() ); 
		console.log('dates : ' + date_exp + ' < ' + date_auj); 
	}

	if (date_exp < date_auj) {	return false;	}
	return true;
}	

function isDatePassee(annee, mois, jour)
{
	return !isDateFuture(annee, mois, jour);
}

function getNextMonth(annee, mois, jour)
{
		var DB = true;
	var mois  = parseInt(mois, 10);
	var annee = parseInt(annee, 10);
	if (2000 > annee) {
		annee = 2000 + annee;	
	}
	if (undefined == jour) {
		jour = 1;
	}
	if (isNaN(annee) ) {
		if (DB) { console.log('isDateFuture : ERREUR ('+annee+')'); }
		return false;
	}
	var nextMois = mois + 1;
	var nextAnnee = annee;
	if (12 < nextMois) {
		nextAnnee++;
		nextMois = 1;
	}
	return [nextAnnee, nextMois];
}