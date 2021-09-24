<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Liquidaciones\ReferenciasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Liquidaciones\ReferenciasBundle\Entity\Imppresupff11;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

/**
 * Imppresupff11 controller.
 *
 * @Route("/imppresupff11")
 */
class Imppresupff11Controller  extends Controller
{
    
    
    /**
     * 
     * @Route("/traerImputacionPresupuestaria/{regsan}", name="imppresupff11_traerImputacionPresupuestaria")
     * @Method("GET")
     * @Template()
     */
    public function traerImputacionPresupuestariaAction($depe,$liquidaciones)
    {
        $em = $this->getDoctrine()->getManager("referencias");
        $consulta = $em->createQuery("SELECT d.codigo as id,"
                . "                          d.nombre as dependencia,"
                . "                          d.codigo as regsan "
                . "                 FROM LiquidacionesReferenciasBundle:Dependencias d "
                . "                 INNER JOIN LiquidacionesCuposAnualesBundle:"
                . "                 where d.tipoDependencia = ".$regsan." and d.codigo is not null "
                . "                 ORDER BY d.id ASC");
        //$consulta = $em->createQuery("SELECT d.id, d.nombre, d.codigo, d.tipoDependencia FROM LiquidacionesReferenciasBundle:Dependencias d ");
        //$response = new Response('marcos');
        //$response = new Response(json_encode(array('id'=>1, 'Cuenta'=>'Reemplazos')));
        $response = new Response(json_encode($consulta->getResult()));
        $response->headers->set('content-type','application/json');
        
        return $response;
        
    }
}
