<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Liquidaciones\CuposAnualesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Liquidaciones\CuposAnualesBundle\Entity\Cupos;
use Liquidaciones\CuposAnualesBundle\Entity\Cuentas;
use Liquidaciones\CuposAnualesBundle\Entity\CuposHATiposLiquidacion;
use Liquidaciones\CuposAnualesBundle\Form\CuposType;
use Liquidaciones\CuposAnualesBundle\Helpers\DataTable;
use Liquidaciones\ReferenciasBundle\Entity\TipoDependencia;
use Liquidaciones\ReferenciasBundle\Controller\ImputacionpresupuestariaController;
use Symfony\Component\HttpFoundation\Response;
use \PDO;

/**
 * Principal controller.
 *
 * @Route("/principal")
 */
 
class PrincipalController extends Controller {
    
    
    /**
     * Lists all Cupos entities.
     *
     * @Route("/", name="principal")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $usr = $this->get('security.context')->getToken()->getUser();
        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:Cupos')->findBy(array('idDependencia'=> '300'));

        return array(
            'entities' => $entities,
        );
    }
    
    
    /**
     * @Route("/cupos/cuposdatatable", name="cuposdatatable")
     * 
     */
    public function tableAction() {
        
        $dt = new \Liquidaciones\CuposAnualesBundle\Helpers\DataTable2(array( "ID", "ANIO", "MES", "NOMBRE", "MONTO", "CUENTA"));
        
        $dt->setQuery("select c.ID,c.Anio,c.Mes,d.Nombre,c.Monto,cc.Cuenta from Cupos c "
                . " inner join TREFE14.Dependencias d on c.iddependencia = d.codigo "
                . " inner join CuposHATiposLiquidacion h on c.id = h.refcupo "
                . " inner join Cuentas cc on cc.id = h.refcuenta "
                . " where c.iddependencia in (100,300,101,102,103,104,105,106) ");
        
        $connection = $this -> getDoctrine() 
                            -> getManager() 
                            -> getConnection();
        
        $stmt = $connection ->  prepare($dt ->  strQuery);
        
        $stmt -> execute();
        
        $rResult = $stmt->fetchAll(PDO::FETCH_ASSOC );
        
        if (count($rResult) > 0){
            $arrResult = $rResult[0]["TOTAL"];
        } else {
            $arrResult = 0;
        }
             
        return new Response($dt->buildDataTable($rResult, $arrResult, count($rResult)));
    }
    
}

?>
