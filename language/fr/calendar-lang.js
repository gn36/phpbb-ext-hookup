// ** I18N

// Calendar FR language
// Author: Mihai Bazon, <mihai_bazon@yahoo.com>
// Translator: phpbb-fr.com community with help of phpBB-fr.com Translation Team
// Encoding: any
// Distributed under the same terms as the calendar itself.

// For translators: please use UTF-8 if possible.  We strongly believe that
// Unicode is the answer to a real internationalized world.  Also please
// include your contact information in the header, as can be seen above.

// full day names
Calendar._DN = new Array
("Dimanche",
 "Lundi",
 "Mardi",
 "Mercredi",
 "Jeudi",
 "Vendredi",
 "Samedi",
 "Dimanche");

// Please note that the following array of short day names (and the same goes
// for short month names, _SMN) isn't absolutely necessary.  We give it here
// for exemplification on how one can customize the short day names, but if
// they are simply the first N letters of the full name you can simply say:
//
//   Calendar._SDN_len = N; // short day name length
//   Calendar._SMN_len = N; // short month name length
//
// If N = 3 then this is not needed either since we assume a value of 3 if not
// present, to be compatible with translation files that were written before
// this feature.

// short day names
Calendar._SDN = new Array
("Di",
 "Lu",
 "Ma",
 "Me",
 "Je",
 "Ve",
 "Sa",
 "Di");

// First day of the week. "0" means display Sunday first, "1" means display
// Monday first, etc.
Calendar._FD = 0;

// full month names
Calendar._MN = new Array
("Janvier",
 "Février",
 "Mars",
 "Avril",
 "Mai",
 "Juin",
 "Juillet",
 "Août",
 "Septembre",
 "Octobre",
 "Novembre",
 "Décembre");

// short month names
Calendar._SMN = new Array
("Janv.",
 "Fevr.",
 "Mars",
 "Avr.",
 "Mai",
 "Juin",
 "Juil.",
 "Août",
 "Sept.",
 "Oct.",
 "Nov.",
 "Déc.");

// tooltips
Calendar._TT = {};
Calendar._TT["INFO"] = "À propos du calendrier";

Calendar._TT["ABOUT"] =
"DHTML Date/Time Selector\n" +
"(c) dynarch.com 2002-2005 / Auteur : Mihai Bazon\n" + // don't translate this this ;-)
"Traduction française par la communauté phpbb-fr.com avec l'aide de l'équipe de traducteurs de phpBB-fr.com\n" +
"Pour la dernière version, visitez : http://www.dynarch.com/projects/calendar/\n" +
"Distribué sous GNU LGPL. Voir http://gnu.org/licenses/lgpl.html pour plus de détails." +
"\n\n" +
"* Pensez à définir l’heure AVANT la date !" +
"\n\n" +
"Sélection de la date :\n" +
"- Utilisez les boutons « ou » pour changer d'année.\n" +
"- Utilisez les boutons " + String.fromCharCode(0x2039) + " ou " + String.fromCharCode(0x203a) + " pour changer de mois.";
Calendar._TT["ABOUT_TIME"] = "\n\n" +
"Sélection de l'heure :\n" +
"- Cliquez sur l’heure ou les minutes pour augmenter de 1 ;\n" +
"- ou maintenez la touche MAJ puis cliquez pour diminuer de 1 ;\n" +
"- ou cliquez et déplacez (à droite ou à gauche) pour modifier la sélection.";

Calendar._TT["PREV_YEAR"] = "Année précédente";
Calendar._TT["PREV_MONTH"] = "Mois précédent";
Calendar._TT["GO_TODAY"] = "Aller à aujourd'hui";
Calendar._TT["NEXT_MONTH"] = "Mois suivant";
Calendar._TT["NEXT_YEAR"] = "Année suivante";
Calendar._TT["SEL_DATE"] = "Choisir une date";
Calendar._TT["DRAG_TO_MOVE"] = "Déplacer le calendrier";
Calendar._TT["PART_TODAY"] = " (Aujourd'hui)";

// the following is to inform that "%s" is to be the first day of week
// %s will be replaced with the day name.
Calendar._TT["DAY_FIRST"] = "Définir le %s comme 1er jour";

// This may be locale-dependent.  It specifies the week-end days, as an array
// of comma-separated numbers.  The numbers are from 0 to 6: 0 means Sunday, 1
// means Monday, etc.
Calendar._TT["WEEKEND"] = "0,6";

Calendar._TT["CLOSE"] = "Fermer";
Calendar._TT["TODAY"] = "Aujourd’hui";
Calendar._TT["TIME_PART"] = "(MAJ+)Clic ou déplacer pour modifier";

// date formats
Calendar._TT["DEF_DATE_FORMAT"] = "%d-%m-%Y";
Calendar._TT["TT_DATE_FORMAT"] = "%A %e %B %Y";

Calendar._TT["WK"] = "Sem.";
Calendar._TT["TIME"] = "Heure :";

//	%a 	= 	Nom du jour de la semaine abrégé
//	%A 	= 	Nom du jour de la semaine entier
//	%b 	= 	Nom du mois abrégé
//	%B 	= 	Nom du mois entier
//	%d 	= 	Jour du mois à 2 chiffres (de 01 à 31)
//	%e 	= 	Jour du mois simple (de 1 à 31)
//	%H 	= 	Heure à 2 chiffres format 24h (de 00 à 23)
//	%I 	= 	Heure à 2 chiffres format 12h (de 01 à 12)
//	%j 	= 	Jour de l'année en chiffre (de 001 à 366)
//	%k 	= 	Heure simple format 24h (de 0 à 23)
//	%l 	= 	Heure simple format 12h (de 1 à 12)
//	%m 	= 	Mois à 2 chiffres (de 01 à 12)
//	%M 	= 	Minute à 2 chiffres (de 00 à 59)
//	%p 	= 	"PM" "AM" en majuscule
//	%P 	= 	"pm" "am" en minuscule
//	%S 	= 	Seconde à 2 chiffres (de 00 à 59)
//	%u 	= 	Jour de la semaine (de 1 à 7, 1 = lundi)
//	%w 	= 	Jour de la semaine (de 0 à 6, 0 = dimanche)
//	%y 	= 	Année à 2 chiffres (de 00 à 99)
//	%Y 	= 	Année complète (4 chiffres)
