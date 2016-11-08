<?php
namespace Pg\GsbFraisBundle\Controller;
require_once("include/fct.inc.php");
//require_once ("include/class.pdogsb.inc.php");
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
//use PdoGsb;

class TablettesController extends Controller
{
    public function listeTabletteAction()
    {
    $pdo = $this->get('pg_gsb_frais.pdo');
    $tablettes = $pdo->getTablettes();
    return $this->render('PgGsbFraisBundle:Tablettes:tablettes.html.twig',array("tablette"=>$tablettes));
    }
}
