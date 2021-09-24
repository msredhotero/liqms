<?php

namespace Liquidaciones\CuposAnualesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Liquidaciones\CuposAnualesBundle\Entity\CuposAnuales;
use Liquidaciones\CuposAnualesBundle\Form\CuposAnualesType;
use Liquidaciones\CuposAnualesBundle\Helpers\DataTable;
use Symfony\Component\HttpFoundation\Response;
use \PDO;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Column\TextColumn;

/**
 * CuposAnuales controller.
 *
 * @Route("/cuposanuales")
 */
class CuposAnualesController extends Controller
{
    /**
     * Lists all CuposAnuales entities.
     *
     * @Route("/", name="cuposanuales")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        // Tomo la dependencia actual
        if ($this->getUser()->multidep_multi){
            $usuarioDependencia = $this->getUser()->multidep_actual_codigo;
        } else {
            $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        }
        
        //Session
        $session = $this->getRequest()->getSession();
        
        //Seguridad
        $securityContext = $this->get('security.context');
        
        if (false === $securityContext->isGranted('ROLE_15')) {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));
        }
        
        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposAnuales')->findAll();
        
        if (($securityContext->isGranted('ROLE_16')) || ($securityContext->isGranted('ROLE_17'))) {
            $puedeModificar =1;
        } else {
            $puedeModificar = 0;
        }

        return array(
            'entities' => $entities,
            'usuaModifica'=>$puedeModificar,
        );
    }
    
    
    /**
     * Lists all CuposAnuales entities.
     *
     * @Route("/gastado", name="cuposanuales_gastado")
     * @Template()
     */
    public function gastadoAction()
    {
        //Session
        $session = $this->getRequest()->getSession();
        
        //Seguridad
        
        $securityContext = $this->get('security.context');
        
        if (false === $securityContext->isGranted('ROLE_15')) {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));
        }
        
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        
        $sql = "SELECT      a.id, 
                            a.descripcion,
                            a.anio,
                            a.monto,
                            sum(l.montoLiquidado) as montoLiquidado,
                            sum(ISNULL(ROUND(((100 * l.montoLiquidado) / a.monto),2),0)) as porcentaje,
                            (case when a.activo = 1 then 'Si' else 'No' end) as activo
                            
                    FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                  inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                  inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                  inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                  inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                  left join (select sum(ll.montoTotalCalculado) as montoLiquidado
                                     ,ll.refCupoTipoLiquidacion
                                from LiquidacionesWeb.dbo.liquidaciones ll
                                
                                group by ll.refCupoTipoLiquidacion) l on ca.id = l.refCupoTipoLiquidacion
                            
                  where e.id in (2,3,18,19,22,23)
                  group by    a.id, 
                                        a.descripcion,
                                        a.anio,
                                        a.monto,
                                        a.activo";
        
        //$entities = $em->createQuery($sql)->getResult();
        $connection = $this -> getDoctrine() 
                                -> getManager("ms_haberes_web") 
                                -> getConnection();

        $entities = $connection ->  prepare($sql);

        $entities -> execute();

        if (($securityContext->isGranted('ROLE_16')) || ($securityContext->isGranted('ROLE_17'))) {
            $puedeModificar = 1;
        } else {
            $puedeModificar = 0;
        }
        
        if ($securityContext->isGranted('ROLE_19')) {
            $liquida = 1;
        } else {
            $liquida = 0;
        }
        return array(
            'entities' => $entities,
            'usuaModifica'=>$puedeModificar,
            'puedeLiquidar'=>$liquida,
        );
    }


    
    /**
     * Lists all CuposAnuales entities.
     *
     * @Route("/{id}/estadisticas", name="cuposanuales_estadisticas")
     * @Template()
     */
    public function estadisticasAction($id)
    {
        //Session
        $session = $this->getRequest()->getSession();
        
        //Seguridad
        $securityContext = $this->get('security.context');
        
        if (false === $securityContext->isGranted('ROLE_15')) {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));
        }
        
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        
        $sql = "SELECT      
                            a.anio as ANIO,
                            ISNULL(sum(c.monto),0) as MONTO,
                            sum(ISNULL(ROUND(((100 * c.monto) / a.monto),2),0)) as PORCENTAJE,
                            CONVERT(VARCHAR,d.IdDependencia) + ' - ' + d.dependencia as CODIGO
                    FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                  inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                  inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                  inner join Haberes.general.HADependencias d on c.iddependencia = d.IdDependencia
                  inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                  inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                  where e.id in (2,3,22) and a.id = ".$id." 
                  group by   
                                        a.anio,
                                       
                                        
                                        d.IdDependencia,
                                        d.dependencia                     
                  ORDER BY d.IdDependencia desc";
        
        //$entities = $em->createQuery($sql)->getResult();
        $connection = $this -> getDoctrine() 
                                -> getManager("ms_haberes_web") 
                                -> getConnection();

        $entities = $connection ->  prepare($sql);

        $entities -> execute();
        
        $entities2 = $connection ->  prepare($sql);

        $entities2 -> execute();
        
        $entities3 = $connection ->  prepare($sql);

        $entities3 -> execute();
        
        $entities4 = $connection ->  prepare($sql);

        $entities4 -> execute();
        
        //die(var_dump($entities));

        if (($securityContext->isGranted('ROLE_16')) || ($securityContext->isGranted('ROLE_17'))) {
            $puedeModificar = 1;
        } else {
            $puedeModificar = 0;
        }
        
        if ($securityContext->isGranted('ROLE_19')) {
            $liquida = 1;
        } else {
            $liquida = 0;
        }
        /*
        $subcadena = '';
        $cadenaY = '';
		$cadenaX = '';
        
        foreach ($entities as $estadisticas) {
            $cadenaY .= "'".$estadisticas['CODIGO']."',";
			$cadenaX .= $estadisticas['MONTO'].",";
        }
        
        $cadenaY = substr($cadenaY,0,-1);
		$cadenaX = substr($cadenaX,0,-1);
        */
        
        return array(
            'entities' => $entities,
            'entities2' => $entities2,
            'entities3' => $entities3,
            'entities4' => $entities4,
            'usuaModifica'=>$puedeModificar,
            'puedeLiquidar'=>$liquida,
        );
    }
    
    
    
    /**
     * Finds and displays a CuposAnuales entity.
     *
     * @Route("/{id}/show", name="cuposanuales_show")
     * @Template()
     */
    public function showAction($id)
    {
        
        //Session
        $session = $this->getRequest()->getSession();
        
        //Seguridad
        $securityContext = $this->get('security.context');
        
        if (false === $securityContext->isGranted('ROLE_15')) {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));
        }
        
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $usr = $this->get('security.context')->getToken()->getUser();
        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposAnuales')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CuposAnuales entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new CuposAnuales entity.
     *
     * @Route("/new", name="cuposanuales_new")
     * @Template()
     */
    public function newAction()
    {
        
        //Session
        $session = $this->getRequest()->getSession();
        
        //Seguridad
        $securityContext = $this->get('security.context');
        
        if ((true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17'))) {
            
        
            $entity = new CuposAnuales();
            $form   = $this->createForm(new CuposAnualesType(), $entity);

            return array(
                'entity' => $entity,
                'form'   => $form->createView(),
            );
        } else {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));
        }

    }

    /**
     * Creates a new CuposAnuales entity.
     *
     * @Route("/create", name="cuposanuales_create")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:CuposAnuales:new.html.twig")
     */
    public function createAction(Request $request)
    {
        
        //Session
        $session = $this->getRequest()->getSession();
        
        //Seguridad
        $securityContext = $this->get('security.context');
        
        if ((true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17'))) {
            
        
        
        $entity  = new CuposAnuales();
        $form = $this->createForm(new CuposAnualesType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager("ms_haberes_web");

            $em->persist($entity);
            $em->flush();

            $request->getSession()->getFlashBag()->add(
                'aviso_ok',
                'Los datos fueron guardados correctamente!'
            );

            return $this->redirect($this->generateUrl('cuposanuales_show', array('id' => $entity->getId())));

            
        } else {
            $request->getSession()->getFlashBag()->add(
                'aviso_error',
                'Hubo algún error verificar nuevamente!: '
            );
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
        } else {
            
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));
        }
    }

    /**
     * Displays a form to edit an existing CuposAnuales entity.
     *
     * @Route("/{id}/edit", name="cuposanuales_edit")
     * @Template()
     */
    public function editAction($id)
    {

        //Session
        $session = $this->getRequest()->getSession();
        
        //Seguridad
        $securityContext = $this->get('security.context');
        
        if ((true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17'))) {
            
        
            $em = $this->getDoctrine()->getManager("ms_haberes_web");

            $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposAnuales')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CuposAnuales entity.');
            }

            $editForm = $this->createForm(new CuposAnualesType(), $entity);

            return array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
            );
        } else {
            
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));
        }
   
    }

    /**
     * Edits an existing CuposAnuales entity.
     *
     * @Route("/{id}/update", name="cuposanuales_update")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:CuposAnuales:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        
        //Session
        $session = $this->getRequest()->getSession();
        
        //Seguridad
        $securityContext = $this->get('security.context');
        
        if ((true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17'))) {
        
            $em = $this->getDoctrine()->getManager("ms_haberes_web");

            $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposAnuales')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CuposAnuales entity.');
            }

            /*$deleteForm = $this->createDeleteForm($id);*/
            $editForm = $this->createForm(new CuposAnualesType(), $entity);
            $editForm->bind($request);
            //var_dump($request);
            
            $totalCargado = $em->createQuery('SELECT sum(c.monto) as total FROM LiquidacionesCuposAnualesBundle:Cupos c
                      JOIN c.cuposanuales ca
                      WHERE ca.id= :idAnual')->setParameter('idAnual', $id)->getResult();

            if ($totalCargado != null) {
                $totalCupoMensual = $totalCargado[0]["total"] == 0 ? 0 : $totalCargado[0]["total"];
            } else {
                $totalCupoMensual = 0;
            }

            if (($entity->getMonto() - $totalCupoMensual) < 0) {


                $request->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'El monto que intenta cargar ($'.number_format($entity->getMonto(),2,'.',',').') es menor a la suma de los montos cargados a los Cupos ($'.number_format($totalCupoMensual,2,'.',',').')'
                );
                return $this->redirect($this->generateUrl('cuposanuales_edit', array('id' => $id)));
            }
            
            if ($editForm->isValid()) {
                $em->persist($entity);
                $em->flush();

                $request->getSession()->getFlashBag()->add(
                    'aviso_ok',
                    'Los datos fueron guardados correctamente!'
                );

                return $this->redirect($this->generateUrl('cuposanuales_edit', array('id' => $id)));
            } else {
                $request->getSession()->getFlashBag()->add(
                    'aviso_error',
                    'Hubo algún error verificar nuevamente!: '.$editForm->getErrorsAsString()
                );
                return $this->redirect($this->generateUrl('cuposanuales_edit', array('id' => $id)));
            }



            return array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                /*'delete_form' => $deleteForm->createView(),*/
            );
        } else {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));
        }
    }

    /**
     * Deletes a CuposAnuales entity.
     *
     * @Route("/{id}/delete", name="cuposanuales_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        //Session
        $session = $this->getRequest()->getSession();
        
        //Seguridad
        $securityContext = $this->get('security.context');
        
        if ((true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17'))) {
            
            $form = $this->createDeleteForm($id);
            $form->bind($request);

            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager("ms_haberes_web");
                $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposAnuales')->find($id);

                if (!$entity) {
                    throw $this->createNotFoundException('Unable to find CuposAnuales entity.');
                }

                $em->remove($entity);
                $em->flush();
            }

            return $this->redirect($this->generateUrl('cuposanuales'));
        } else {
            
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));
        }
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    /**
     * @Route("/cuposanualesdatatable", name="cuposanualesdatatable")
     * 
     */
    public function tableAction() {
        
        $dt = new \Liquidaciones\CuposAnualesBundle\Helpers\DataTable(array( "ID", "DESCRIPCION", "ANIO", "MONTO", "ACTIVO"));
        
        $dt->setQuery("     SELECT c.ID, c.Descripcion, c.Anio, c.Monto, (case c.Activo when '1' then 'Activo' else 'Inactivo' end) as Activo "
                    . "     FROM LiquidacionesWeb.dbo.cuposanuales c ");
        
        $connection = $this -> getDoctrine() 
                            -> getManager("ms_haberes_web") 
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
    
    
    
    /**
     * 
     * @Route("/traerSaldoCupoAnual/{id}", name="cuposanuales_traerSaldoCupoAnual")
     */
    public function traerSaldoCupoAnualAction($id)
    {
        //Session
        $session = $this->getRequest()->getSession();
        
        //Seguridad
        $securityContext = $this->get('security.context');
        
        if ((true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17'))) {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));
        }
        
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $total = $em->createQuery('SELECT sum(c.monto) as total FROM LiquidacionesCuposAnualesBundle:Cupos c
                  JOIN c.cuposanuales ca
                  WHERE ca.id= :idCupoAnual')->setParameter('idCupoAnual', $id)->getResult();
        $totalCupoMensual = $total[0]["total"];
        
        $totalAnual = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposAnuales')->find($id)->getMonto();
        
        $diferencia = $totalAnual - $totalCupoMensual;
        
        $diferencia = $diferencia;
        //$consulta = $em->createQuery("SELECT d.id, d.nombre, d.codigo, d.tipoDependencia FROM LiquidacionesReferenciasBundle:Dependencias d ");
        //$response = new Response('marcos');
        //$response = new Response(json_encode(array('id'=>1, 'Cuenta'=>'Reemplazos')));
        $response = new Response(json_encode($diferencia));
        $response->headers->set('content-type','application/json');
        
        return $response;
        
    }
    
}
