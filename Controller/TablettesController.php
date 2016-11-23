<?php
namespace Pg\GsbFraisBundle\Controller;
require_once("include/fct.inc.php");
//require_once ("include/class.pdogsb.inc.php");
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use PdoGsb;

class TablettesController extends Controller
{

	 public function listeVisiteurAction()
    {
    	$pdo = $this->get('pg_gsb_frais.pdo');
    	$visiteur = $pdo->getLesVisiteurs();
    	return $this->render('PgGsbFraisBundle:Tablettes:tablettes.html.twig',array("liste"=>$lesAffec));
    }
    public function listeTabletteAction()
    {
    	$visiteur = 'a17';
    	$pdo = $this->get('pg_gsb_frais.pdo');
    	$affectationTab = $pdo->getLesAffectationsTablettes($visiteur);
    	return $this->render('PgGsbFraisBundle:Tablettes:tablettes.html.twig',array("tablette"=>$affectationTab));
    }
}
