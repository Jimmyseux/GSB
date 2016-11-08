<<<<<<< HEAD
<?php
/** 
 * Classe d'accès aux données. 
 
 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO 
 * $monPdoGsb qui contiendra l'unique instance de la classe
=======
﻿<?php
/** 
 * Fonctions pour l'application GSB
>>>>>>> 4651d262563f32908cc52f4f456c9a257f3fa751
 
 * @package default
 * @author Cheri Bibi
 * @version    1.0
<<<<<<< HEAD
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */

class PdoGsb{   		
      	private static $serveur='mysql:host=localhost';
      	private static $bdd='dbname=gsb';   		
      	private static $user='root' ;    		
      	private static $mdp='root' ;	
	private static $monPdo;
	private static $monPdoGsb=null;
/**
 * Constructeur privé, crée l'instance de PDO qui sera sollicitée
 * pour toutes les méthodes de la classe
 */				
	private function __construct(){
    	PdoGsb::$monPdo = new PDO(PdoGsb::$serveur.';'.PdoGsb::$bdd, PdoGsb::$user, PdoGsb::$mdp); 
		PdoGsb::$monPdo->query("SET CHARACTER SET utf8");
	}
	public function _destruct(){
		PdoGsb::$monPdo = null;
	}
/**
 * Fonction statique qui crée l'unique instance de la classe
 
 * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();
 
 * @return l'unique objet de la classe PdoGsb
 */
	public  static function getPdoGsb(){
		if(PdoGsb::$monPdoGsb==null){
			PdoGsb::$monPdoGsb= new PdoGsb();
		}
		return PdoGsb::$monPdoGsb;  
	}
/**
 * Retourne les informations d'un visiteur
 
 * @param $login 
 * @param $mdp
 * @return l'id, le nom et le prénom sous la forme d'un tableau associatif 
*/
	public function getInfosVisiteur($login, $mdp){
                $req = "select visiteur.id as id, visiteur.nom as nom, visiteur.prenom as prenom from visiteur 
		where visiteur.login=:log and visiteur.mdp=:md";
		var_dump($req);
		$stmt = PdoGsb::$monPdo->prepare($req);
                $stmt->bindParam(':log', $login);
		$stmt->bindParam(':md', $mdp);
                $stmt->execute();
		$ligne = $stmt->fetch();
            //    var_dump($ligne);
		return $ligne;
	}

/**
 * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
 * concernées par les deux arguments
 
 * La boucle foreach ne peut être utilisée ici car on procède
 * à une modification de la structure itérée - transformation du champ date-
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif 
*/
	public function getLesFraisHorsForfait($idVisiteur,$mois){
	    $req = "select * from lignefraishorsforfait where lignefraishorsforfait.idvisiteur = :idVisiteur 
		and lignefraishorsforfait.mois = :mois ";
                $stmt = PdoGsb::$monPdo->prepare($req);
                $stmt->bindParam(':idVisiteur', $idVisiteur);
		$stmt->bindParam(':mois', $mois);
                $stmt->execute();
		$lesLignes = $stmt->fetchAll();
		$nbLignes = count($lesLignes);
		for ($i=0; $i<$nbLignes; $i++){
			$date = $lesLignes[$i]['date'];
			$lesLignes[$i]['date'] =  dateAnglaisVersFrancais($date);
		}
		return $lesLignes; 
	}
/**
 * Retourne le nombre de justificatif d'un visiteur pour un mois donné
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return le nombre entier de justificatifs 
*/
	public function getNbjustificatifs($idVisiteur, $mois){
		$req = "select fichefrais.nbjustificatifs as nb from  fichefrais where fichefrais.idvisiteur =:idVisiteur and fichefrais.mois = :mois";
		$stmt = PdoGsb::$monPdo->prepare($req);
                $stmt->bindParam(':idVisiteur', $idVisiteur);
		$stmt->bindParam(':mois', $mois);
                $stmt->execute();
		$laLigne = $stmt->fetch();
		return $laLigne['nb'];
	}
/**
 * Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait
 * concernées par les deux arguments
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return l'id, le libelle et la quantité sous la forme d'un tableau associatif 
*/
	public function getLesFraisForfait($idVisiteur, $mois){
		$req = "select fraisforfait.id as idfrais, fraisforfait.libelle as libelle, 
		lignefraisforfait.quantite as quantite from lignefraisforfait inner join fraisforfait 
		on fraisforfait.id = lignefraisforfait.idfraisforfait
		where lignefraisforfait.idvisiteur = :idVisiteur and lignefraisforfait.mois= :mois 
		order by lignefraisforfait.idfraisforfait";	
		$stmt = PdoGsb::$monPdo->prepare($req);
                $stmt->bindParam(':idVisiteur', $idVisiteur);
		$stmt->bindParam(':mois', $mois);
                $stmt->execute();
		$lesLignes = $stmt->fetchAll();
		return $lesLignes; 
	}
/**
 * Retourne tous les id de la table FraisForfait
 
 * @return un tableau associatif 
*/
	public function getLesIdFrais(){
		$req = "select fraisforfait.id as idfrais from fraisforfait order by fraisforfait.id";
		$stmt = PdoGsb::$monPdo->prepare($req);
                $stmt->execute();
		$lesLignes = $stmt->fetchAll();
		return $lesLignes;
	}
/**
 * Met à jour la table ligneFraisForfait
 
 * Met à jour la table ligneFraisForfait pour un visiteur et
 * un mois donné en enregistrant les nouveaux montants
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @param $lesFrais tableau associatif de clé idFrais et de valeur la quantité pour ce frais
 * @return un tableau associatif 
*/
	public function majFraisForfait($idVisiteur, $mois, $lesFrais){
		$lesCles = array_keys($lesFrais);
		foreach($lesCles as $unIdFrais){
			$qte = $lesFrais[$unIdFrais];
			$req = "update lignefraisforfait set lignefraisforfait.quantite = $qte
			where lignefraisforfait.idvisiteur = :idVisiteur and lignefraisforfait.mois = :mois
			and lignefraisforfait.idfraisforfait = :unIdFrais";
                         $stmt = PdoGsb::$monPdo->prepare($req);
                        $stmt->bindParam(':idVisiteur', $idVisiteur);
                        $stmt->bindParam(':mois', $mois);
                         $stmt->bindParam(':unIdFrais', $unIdFrais);
                        $stmt->execute();
		}
		
	}
/**
 * met à jour le nombre de justificatifs de la table ficheFrais
 * pour le mois et le visiteur concerné
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
*/
        public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs){
                $req = "update fichefrais set nbjustificatifs = :nbJustificatifs 
                where fichefrais.idvisiteur = :idVisiteur and fichefrais.mois = :mois";
                $stmt = PdoGsb::$monPdo->prepare($req);
                $stmt->bindParam(':idVisiteur', $idVisiteur);
                $stmt->bindParam(':mois', $mois);
                $stmt->bindParam(':nbJustificatifs', $nbJustificatifs);
                $stmt->execute();
        }
/**
 * Teste si un visiteur possède une fiche de frais pour le mois passé en argument
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return vrai ou faux 
*/	
        public function estPremierFraisMois($idVisiteur,$mois)
        {
                $ok = false;
                $req = "select count(*) as nblignesfrais from fichefrais 
                where fichefrais.mois = :mois and fichefrais.idvisiteur = :idVisiteur";
                $stmt = PdoGsb::$monPdo->prepare($req);
                $stmt->bindParam(':idVisiteur', $idVisiteur);
                $stmt->bindParam(':mois', $mois);
                $stmt->execute();
                $laLigne = $stmt->fetch();
                if($laLigne['nblignesfrais'] == 0){
                        $ok = true;
                }
                return $ok;
        }
/**
 * Retourne le dernier mois en cours d'un visiteur
 
 * @param $idVisiteur 
 * @return le mois sous la forme aaaamm
*/	
        public function dernierMoisSaisi($idVisiteur){
                $req = "select max(mois) as dernierMois from fichefrais where fichefrais.idvisiteur = :idVisiteur";
                $stmt = PdoGsb::$monPdo->prepare($req);
                $stmt->bindParam(':idVisiteur', $idVisiteur);
                $stmt->execute();
                $laLigne = $stmt->fetch();
                $dernierMois = $laLigne['dernierMois'];
                return $dernierMois;
        }
	
/**
 * Crée une nouvelle fiche de frais et les lignes de frais au forfait pour un visiteur et un mois donnés
 
 * récupère le dernier mois en cours de traitement, met à 'CL' son champs idEtat, crée une nouvelle fiche de frais
 * avec un idEtat à 'CR' et crée les lignes de frais forfait de quantités nulles 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
*/
        public function creeNouvellesLignesFrais($idVisiteur,$mois){
                $dernierMois = $this->dernierMoisSaisi($idVisiteur);
                $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur,$dernierMois);
                if($laDerniereFiche['idEtat']=='CR'){
                                $this->majEtatFicheFrais($idVisiteur, $dernierMois,'CL');

                }
                $req = "insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat) 
                values(:idVisiteur,:mois,0,0,now(),'CR')";
                $stmt = PdoGsb::$monPdo->prepare($req);
                $stmt->bindParam(':idVisiteur', $idVisiteur);
                 $stmt->bindParam(':mois', $mois);
                $stmt->execute();
                $lesIdFrais = $this->getLesIdFrais();
                foreach($lesIdFrais as $uneLigneIdFrais){
                        $unIdFrais = $uneLigneIdFrais['idfrais'];
                        $req = "insert into lignefraisforfait(idvisiteur,mois,idFraisForfait,quantite) 
                        values(:idVisiteur,:mois,:unIdFrais,0)";
                        $stmt = PdoGsb::$monPdo->prepare($req);
                        $stmt->bindParam(':idVisiteur', $idVisiteur);
                        $stmt->bindParam(':mois', $mois);
                        $stmt->bindParam(':unIdFrais', $unIdFrais);
                        $stmt->execute();
                 }
        }
/**
 * Crée un nouveau frais hors forfait pour un visiteur un mois donné
 * à partir des informations fournies en paramètre
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @param $libelle : le libelle du frais
 * @param $date : la date du frais au format français jj//mm/aaaa
 * @param $montant : le montant
*/
        public function creeNouveauFraisHorsForfait($idVisiteur,$mois,$libelle,$date,$montant){
                $dateFr = dateFrancaisVersAnglais($date);
                $req = "insert into lignefraishorsforfait 
                values('',:idVisiteur,:mois,:libelle,'$dateFr',:montant)";
                $stmt = PdoGsb::$monPdo->prepare($req);
                $stmt->bindParam(':idVisiteur', $idVisiteur);
                $stmt->bindParam(':mois', $mois);
                $stmt->bindParam(':libelle', $libelle);
                $stmt->bindParam(':montant', $montant);
                $stmt->execute();
        }
/**
 * Supprime le frais hors forfait dont l'id est passé en argument
 
 * @param $idFrais 
*/
        public function supprimerFraisHorsForfait($idFrais){
                $req = "delete from lignefraishorsforfait where lignefraishorsforfait.id =:idFrais ";
                $stmt = PdoGsb::$monPdo->prepare($req);
                $stmt->bindParam(':idFrais', $idFrais);
                $stmt->execute();
        }
/**
 * Retourne les mois pour lesquel un visiteur a une fiche de frais
 
 * @param $idVisiteur 
 * @return un tableau associatif de clé un mois -aaaamm- et de valeurs l'année et le mois correspondant 
*/
        public function getLesMoisDisponibles($idVisiteur){
                $req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur =:idVisiteur 
                order by fichefrais.mois desc ";
                $stmt = PdoGsb::$monPdo->prepare($req);
                $stmt->bindParam(':idVisiteur', $idVisiteur);
                $stmt->execute();
                $laLigne = $stmt->fetch();
                $lesMois =array();
                while($laLigne != null)	{
                        $mois = $laLigne['mois'];
                        $numAnnee =substr( $mois,0,4);
                        $numMois =substr( $mois,4,2);
                        $lesMois["$mois"]=array(
                        "mois"=>"$mois",
                        "numAnnee"  => "$numAnnee",
                        "numMois"  => "$numMois"
                        );
                        $laLigne = $stmt->fetch(); 		
                }
                return $lesMois;
        }
/**
 * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donné
 
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 * @return un tableau avec des champs de jointure entre une fiche de frais et la ligne d'état 
*/	
        public function getLesInfosFicheFrais($idVisiteur,$mois){
                $req = "select ficheFrais.idEtat as idEtat, ficheFrais.dateModif as dateModif, ficheFrais.nbJustificatifs as nbJustificatifs, 
                        ficheFrais.montantValide as montantValide, etat.libelle as libEtat from  fichefrais inner join Etat on ficheFrais.idEtat = Etat.id 
                        where fichefrais.idvisiteur = :idVisiteur and fichefrais.mois = :mois";
                $stmt = PdoGsb::$monPdo->prepare($req);
                $stmt->bindParam(':idVisiteur', $idVisiteur);
                $stmt->bindParam(':mois', $mois);
                $stmt->execute();
                $laLigne = $stmt->fetch();
                return $laLigne;
        }
/**
 * Modifie l'état et la date de modification d'une fiche de frais
 
 * Modifie le champ idEtat et met la date de modif à aujourd'hui
 * @param $idVisiteur 
 * @param $mois sous la forme aaaamm
 */
 
        public function majEtatFicheFrais($idVisiteur,$mois,$etat){
                $req = "update ficheFrais set idEtat = :etat, dateModif = now() 
                where fichefrais.idvisiteur = :idVisiteur and fichefrais.mois = :mois";
                $stmt = PdoGsb::$monPdo->prepare($req);
                $stmt->bindParam(':idVisiteur', $idVisiteur);
                $stmt->bindParam(':mois', $mois);
                $stmt->bindParam(':etat', $etat);
                $stmt->execute();

        }
/**
 * Retourne toutes les fiches de frais dont l'état est à validé
 * @return type
 */
        public function getLesFichesValidees()
        {
            $req = "select * from fichefrais
                    inner join etat on fichefrais.idetat = etat.id
                    inner join visiteur on visiteur.id = fichefrais.idVisiteur
                    where idetat = 'VA'";	
            $stmt = PdoGsb::$monPdo->prepare($req);
            $stmt->execute();
            $lesLignes = $stmt->fetchAll();
            return $lesLignes; 
        }
/**
 * Vérifie si un frais existe pour un visiteur et un mois donné
 * @param type $idVisiteur
 * @param type $mois
 * @param type $idFrais
 * @return 1 ou 0
 */
        public function estValideSuppressionFrais($idVisiteur,$mois,$idFrais){
            $req = "select count(*) as nb from lignefraishorsforfait 
            where lignefraishorsforfait.id=:idfrais and lignefraishorsforfait.mois=:mois
            and lignefraishorsforfait.idvisiteur=:idvisiteur";
            $stmt = PdoGsb::$monPdo->prepare($req);
            $stmt->bindParam(':idfrais', $idFrais);
            $stmt->bindParam(':mois', $mois);
            $stmt->bindParam(':idvisiteur', $idVisiteur);
            $stmt->execute();
            $ligne = $stmt->fetch();
            return $ligne['nb'];

        }
        public function getTablettes(){
        $req = "select tablette.refvisiteur as visiteur, tablette.numtablette as numtablette, tablette.typeT as typeT, tablette.capaciteI as capciteI, tablette.capaciteE as capaciteE from tablette";
        $stmt = PdoGsb::$monPdo->prepare($req);
                $stmt->execute();
        $lesTab = $stmt->fetchAll();
        return $lesTab;
    }
}
?>
=======
 */
 /**
 * Teste si un quelconque visiteur est connecté
 * @return vrai ou faux 
 */
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Request;

function estConnecte($session){
  //return isset($_SESSION['idVisiteur']);
//    $request = Request::createFromGlobals();
//    $session = $request->getSession();
      return $session->getFlashBag()->has('id');
}
/**
 * Enregistre dans une variable session les infos d'un visiteur
 
 * @param $id 
 * @param $nom
 * @param $prenom
 */
function connecter($session, $id,$nom,$prenom){
//	$_SESSION['idVisiteur']= $id; 
//	$_SESSION['nom']= $nom;
////	$_SESSION['prenom']= $prenom;
//     $request = Request::createFromGlobals();
//    $session = $request->getSession();
    $session->getFlashBag()->add('id',$id);
     $session->getFlashBag()->add('nom',$nom);
      $session->getFlashBag()->add('prenom',$prenom);
     
}
/**
 * Détruit la session active
 */
function deconnecter(){
	session_destroy();
}
/**
 * Transforme une date au format français jj/mm/aaaa vers le format anglais aaaa-mm-jj
 
 * @param $madate au format  jj/mm/aaaa
 * @return la date au format anglais aaaa-mm-jj
*/
function dateFrancaisVersAnglais($maDate){
	@list($jour,$mois,$annee) = explode('/',$maDate);
	return date('Y-m-d',mktime(0,0,0,$mois,$jour,$annee));
}
/**
 * Transforme une date au format format anglais aaaa-mm-jj vers le format français jj/mm/aaaa 
 
 * @param $madate au format  aaaa-mm-jj
 * @return la date au format format français jj/mm/aaaa
*/
function dateAnglaisVersFrancais($maDate){
   @list($annee,$mois,$jour)=explode('-',$maDate);
   $date="$jour"."/".$mois."/".$annee;
   return $date;
}
/**
 * retourne le mois au format aaaamm selon le jour dans le mois
 
 * @param $date au format  jj/mm/aaaa
 * @return le mois au format aaaamm
*/
function getMois($date){
		@list($jour,$mois,$annee) = explode('/',$date);
		if(strlen($mois) == 1){
			$mois = "0".$mois;
		}
		return $annee.$mois;
}

/* gestion des erreurs*/
/**
 * Indique si une valeur est un entier positif ou nul
 
 * @param $valeur
 * @return vrai ou faux
*/
function estEntierPositif($valeur) {
	return preg_match("/[^0-9]/", $valeur) == 0;
	
}

/**
 * Indique si un tableau de valeurs est constitué d'entiers positifs ou nuls
 
 * @param $tabEntiers : le tableau
 * @return vrai ou faux
*/
function estTableauEntiers($tabEntiers) {
	$ok = true;
	foreach($tabEntiers as $unEntier){
		if(!estEntierPositif($unEntier)){
		 	$ok=false; 
		}
	}
	return $ok;
}
/**
 * Vérifie si une date est inférieure d'un an à la date actuelle
 
 * @param $dateTestee 
 * @return vrai ou faux
*/
function estDateDepassee($dateTestee){
	$dateActuelle=date("d/m/Y");
	@list($jour,$mois,$annee) = explode('/',$dateActuelle);
	$annee--;
	$AnPasse = $annee.$mois.$jour;
	@list($jourTeste,$moisTeste,$anneeTeste) = explode('/',$dateTestee);
	return ($anneeTeste.$moisTeste.$jourTeste < $AnPasse); 
}
/**
 * Vérifie la validité du format d'une date française jj/mm/aaaa 
 
 * @param $date 
 * @return vrai ou faux
*/
function estDateValide($date){
	$tabDate = explode('/',$date);
	$dateOK = true;
	if (count($tabDate) != 3) {
	    $dateOK = false;
    }
    else {
		if (!estTableauEntiers($tabDate)) {
			$dateOK = false;
		}
		else {
			if (!checkdate($tabDate[1], $tabDate[0], $tabDate[2])) {
				$dateOK = false;
			}
		}
    }
	return $dateOK;
}

/**
 * Vérifie que le tableau de frais ne contient que des valeurs numériques 
 
 * @param $lesFrais 
 * @return vrai ou faux
*/
function lesQteFraisValides($lesFrais){
	return estTableauEntiers($lesFrais);
}
/**
 * Vérifie la validité des trois arguments : la date, le libellé du frais et le montant 
 
 * des message d'erreurs sont ajoutés au tableau des erreurs
 
 * @param $dateFrais 
 * @param $libelle 
 * @param $montant
 */
//function valideInfosFrais($session,$dateFrais,$libelle,$montant){
//	if($dateFrais==""){
//		ajouterErreur($session,"Le champ date ne doit pas être vide");
//	}
//	else{
//		if(!estDatevalide($dateFrais)){
//			ajouterErreur($session,"Date invalide");
//		}	
//		else{
//			if(estDateDepassee($dateFrais)){
//				ajouterErreur($session,"date d'enregistrement du frais dépassé, plus de 1 an");
//			}			
//		}
//	}
//	if($libelle == ""){
//		ajouterErreur($session,"Le champ description ne peut pas être vide");
//	}
//	if($montant == ""){
//		ajouterErreur($session,"Le champ montant ne peut pas être vide");
//	}
//	else
//		if( !is_numeric($montant) ){
//			ajouterErreur($session,"Le champ montant doit être numérique");
//		}
//}


function valideInfosFrais($dateFrais,$libelle,$montant){
    $lesErreurs = array();	
    if($dateFrais==""){
		$lesErreurs[] = "Le champ date ne doit pas être vide";
	}
	else{
		if(!estDatevalide($dateFrais)){
			$lesErreurs[] = "Date invalide";
		}	
		else{
			if(estDateDepassee($dateFrais)){
				$lesErreurs[] = "date d'enregistrement du frais dépassé de plus de 1 an";
			}			
		}
	}
	if($libelle == ""){
		$lesErreurs[] = "Le champ description ne peut pas être vide";
	}
	if($montant == ""){
		$lesErreurs[] = "Le champ montant ne peut pas être vide";
	}
	else
		if( !is_numeric($montant) ){
			$lesErreurs[] = "Le champ montant doit être numérique";
		}
                echo "erreurs:";
                var_dump($lesErreurs);
        return $lesErreurs;        
}

/**
 * Ajoute le libellé d'une erreur au tableau des erreurs 
 
 * @param $msg : le libellé de l'erreur 
 */
function ajouterErreur($session, $msg){
  
 

    $session->getFlashBag()->add('erreurs',$msg);
    
}
function existeErreurs($session){
    
    return $session->getFlashBag()->has('erreurs');
}
function getLesErreurs($session){
    
    return  $session->getFlashBag()->get('erreurs');
}
/**
 * Retoune le nombre de lignes du tableau des erreurs 
 
 * @return le nombre d'erreurs
 */
//function nbErreurs(){
//    $request = Request::createFromGlobals();
//    $erreurs = $request->getSession()->get('erreurs');
//  
//	   return count($erreurs);
//	
//}
?>
>>>>>>> 4651d262563f32908cc52f4f456c9a257f3fa751
