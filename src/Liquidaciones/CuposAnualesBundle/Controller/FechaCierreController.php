<?php

namespace Liquidaciones\CuposAnualesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Liquidaciones\CuposAnualesBundle\Entity\FechaCierre;
use Liquidaciones\CuposAnualesBundle\Form\FechaCierreType;

/**
 * FechaCierre controller.
 *
 * @Route("/fechacierre")
 */
class FechaCierreController extends Controller
{

    /**
     * Lists all FechaCierre entities.
     *
     * @Route("/", name="fechacierre")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
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
        
        $sql = "SELECT fc.id, 
                            c.anio,
                            c.mes,
                            c.monto,
                            e.cupoEstado,
                            fc.fechadesde,
                            fc.fechahasta,
                            CONVERT(VARCHAR,d.IdDependencia) + ' - ' + d.dependencia as codigo,
                            cc.cuenta,
                            ca.adicional,
                            a.descripcion,
                            d.regsan1 as COD_TIPO_DEPENDENCIA
                    FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                  inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                  inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                  inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                  inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                  inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                  inner join LiquidacionesWeb.dbo.fechacierre fc on fc.refcupo = c.id
                  where cc.id not in (2,3,18,19,22,23) and convert(date, convert(varchar,c.mes) + '/01' +  '/' + convert(varchar,c.anio)) > DATEADD(MM, -5, getdate())
                  
                union all
                

        select  d.id,
                d.anio,
                d.mes,
                d.monto,
                d.cupoEstado,
                d.fechadesde,
                d.fechahasta,
                d.codigo,
                CONCAT(CONCAT(min(d.cuentaModulo),' / '),min(d.cuentaBeca)) as cuenta,
                d.adicional,
                d.descripcion,
                d.COD_TIPO_DEPENDENCIA
                
                from (
                SELECT fc.id, 
                            c.anio,
                            c.mes,
                            c.monto,
                            e.cupoEstado,
                            fc.fechadesde,
                            fc.fechahasta,
                            CONVERT(VARCHAR,d.IdDependencia) + ' - ' + d.dependencia as codigo,
                            cc.cuenta as cuentaModulo,
                            '' as cuentaBeca,
                            ca.adicional,
                            a.descripcion,
                            d.regsan1 as COD_TIPO_DEPENDENCIA
                    FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                  inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                  inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                  inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                  inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                  inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                  inner join LiquidacionesWeb.dbo.fechacierre fc on fc.refcupo = c.id
                  where cc.id in (2,18,22) and convert(date, convert(varchar,c.mes) + '/01' +  '/' + convert(varchar,c.anio)) > DATEADD(MM, -5, getdate())
                  
                union all
                
                SELECT fc.id, 
                            c.anio,
                            c.mes,
                            c.monto,
                            e.cupoEstado,
                            fc.fechadesde,
                            fc.fechahasta,
                            CONVERT(VARCHAR,d.IdDependencia) + ' - ' + d.dependencia as codigo,
                            '' as cuentaModulo,
                            cc.cuenta as cuentaBeca,
                            ca.adicional,
                            a.descripcion,
                            d.regsan1 as COD_TIPO_DEPENDENCIA
                    FROM LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca
                  inner join LiquidacionesWeb.dbo.cupos c on c.id = ca.refcupo
                  inner join LiquidacionesWeb.dbo.cuposanuales a on a.id = c.refcupoanual
                  inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                  inner join LiquidacionesWeb.dbo.cupoestados e on e.id = c.refcupoestado
                  inner join LiquidacionesWeb.dbo.Cuentas cc on cc.id = ca.refcuenta
                  inner join LiquidacionesWeb.dbo.fechacierre fc on fc.refcupo = c.id
                  where cc.id in (3,19,23) and convert(date, convert(varchar,c.mes) + '/01' +  '/' + convert(varchar,c.anio)) > DATEADD(MM, -5, getdate()) ) d
            group by d.anio,
                d.mes,
                d.monto,
                d.cupoEstado,
                d.adicional,
                d.codigo,
                d.descripcion,
                d.fechadesde,
                d.fechahasta,
                d.COD_TIPO_DEPENDENCIA,
                d.id";
        
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
     * Creates a new FechaCierre entity.
     *
     * @Route("/", name="fechacierre_create")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:FechaCierre:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $session = $this->getRequest()->getSession();
        
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        
        $entity = new FechaCierre();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        
        $data = $request->request->all();
        
        $region         = $data['regiones'];
        $cuposAnuales   = $data['cupoanual'];
        $mes            = $data['mes'];
        $idDependencia  = $data['idDependencia'];
        
        $cantidad = 0;
        
        if ($idDependencia == 0) {
            $depes = $em->createQuery("SELECT d.id,d.dependencia as dependencia,d.regsan1 as regsan FROM LiquidacionesHaberesBundle:HADependencias d "
                                    . " where d.regsan1 = '".$region."' ORDER BY d.id ASC")->getResult();
        } else {
            $depes = $em->createQuery("SELECT d.id,d.dependencia as dependencia,d.regsan1 as regsan FROM LiquidacionesHaberesBundle:HADependencias d "
                                    . " where d.regsan1 = '".$region."' and d.id = ".$idDependencia." ORDER BY d.id ASC")->getResult();
            
        }
        //die(var_dump($depes));
        foreach ($depes as $value) {

            $entityCupos   =   $em->getRepository('LiquidacionesCuposAnualesBundle:Cupos')->findBy(array("mes"=>$mes,"refCupoAnual"=>$cuposAnuales, "idDependencia"=>$value['id']));
            
            foreach ($entityCupos as $value) {
                $cupos = $em->getRepository('LiquidacionesCuposAnualesBundle:Cupos')->find($value->getId());
                
                $fechaCierreCupo = $em->getRepository('LiquidacionesCuposAnualesBundle:FechaCierre')->findBy(array("refCupo"=>$value->getId()));
                
                if ($fechaCierreCupo == null) {
                    $cantidad += 1;
                    $fechaCierreNueva   = new FechaCierre();
                    $fechaCierreNueva->setCupos($cupos);
                    $fechaCierreNueva->setRefCupo($value->getId());
                    $fechaCierreNueva->setFechaCrea(new \DateTime());
                    $fechaCierreNueva->setFechaDesde($entity->getFechaDesde());
                    $fechaCierreNueva->setFechaHasta($entity->getFechaHasta());
                    $fechaCierreNueva->setFechaModi(new \DateTime());
                    $fechaCierreNueva->setUsuaCrea($this->getUser()->getUsername());
                    $fechaCierreNueva->setUsuaModi($this->getUser()->getUsername());

                    $em->persist($fechaCierreNueva);
                    $em->flush();
                } else {
                    $cantidad += 1;
                    $fechaCierreCupo[0]->setUsuaModi($this->getUser()->getUsername());
                    $fechaCierreCupo[0]->setFechaDesde($entity->getFechaDesde());
                    $fechaCierreCupo[0]->setFechaHasta($entity->getFechaHasta());
                    $em->persist($fechaCierreCupo[0]);
                    $em->flush();
                }
                
                unset($fechaCierreCupo);
            }
            
            
        }
        
        $this->getRequest()->getSession()->getFlashBag()->add(
                    'aviso_ok',
                    'Se le cargo Fecha de Cierre a '.$cantidad.' cupo/s.'
                );

        return $this->redirect($this->generateUrl('fechacierre_new'));
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a FechaCierre entity.
    *
    * @param FechaCierre $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(FechaCierre $entity)
    {
        $form = $this->createForm(new FechaCierreType(), $entity, array(
            'action' => $this->generateUrl('fechacierre_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new FechaCierre entity.
     *
     * @Route("/new", name="fechacierre_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new FechaCierre();
        $form   = $this->createCreateForm($entity);

        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        
        $sql = "SELECT distinct d.RegSan1 as COD_TIPO_DEPENDENCIA
                    FROM Haberes.General.HADependencias d where d.RegSan1 is not null";
        
        //$entities = $em->createQuery($sql)->getResult();
        $connection = $this -> getDoctrine() 
                                -> getManager("ms_haberes_web") 
                                -> getConnection();

        $entityHAL = $connection ->  prepare($sql);

        $entityHAL -> execute();
        
        $entityCA   =   $em->getRepository('LiquidacionesCuposAnualesBundle:CuposAnuales')->findBy(array("activo"=>"1"));
        
        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
            'entityHAL' => $entityHAL,
            'cuposAnuales'=>$entityCA,
        );
    }

    /**
     * Finds and displays a FechaCierre entity.
     *
     * @Route("/{id}", name="fechacierre_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:FechaCierre')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FechaCierre entity.');
        }
        
        $cupoHA   = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->findOneBy(array('refCupo'=> $entity->getRefCupo()));
   
        $dependencia    =   $em->getRepository('LiquidacionesHaberesBundle:HADependencias')->find($cupoHA->getCupos()->getIdDependencia());
        
        $leyendaTipoLiquidacion     = $cupoHA->getCuentas()->getCuenta();
        $leyendaDependencia         = $dependencia->getId().' - '.$dependencia->getDependencia();
        $leyendaAnio                = $cupoHA->getCupos()->getAnio();
        $leyendaMes                 = $cupoHA->getCupos()->getMes();

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'lblTipoLiquidacion' => $leyendaTipoLiquidacion,
            'lblDependencia' => $leyendaDependencia,
            'lblAnio' => $leyendaAnio,
            'lblMes' => $leyendaMes,
        );
    }

    /**
     * Displays a form to edit an existing FechaCierre entity.
     *
     * @Route("/{id}/edit", name="fechacierre_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:FechaCierre')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FechaCierre entity.');
        }
        
        $cupoHA   = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->findOneBy(array('refCupo'=> $entity->getRefCupo()));
        
        $editForm = $this->createForm(new FechaCierreType(),$entity);
        
        $deleteForm = $this->createDeleteForm($id);

        $leyenda = '';
        
        
        
        $dependencia    =   $em->getRepository('LiquidacionesHaberesBundle:HADependencias')->find($cupoHA->getCupos()->getIdDependencia());
        
        $leyendaTipoLiquidacion     = $cupoHA->getCuentas()->getCuenta();
        $leyendaDependencia         = $dependencia->getId().' - '.$dependencia->getDependencia();
        $leyendaAnio                = $cupoHA->getCupos()->getAnio();
        $leyendaMes                 = $cupoHA->getCupos()->getMes();
        
        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'lblTipoLiquidacion' => $leyendaTipoLiquidacion,
            'lblDependencia' => $leyendaDependencia,
            'lblAnio' => $leyendaAnio,
            'lblMes' => $leyendaMes,
        );
    }

    /**
    * Creates a form to edit a FechaCierre entity.
    *
    * @param FechaCierre $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(FechaCierre $entity)
    {
        $form = $this->createForm(new FechaCierreType(), $entity, array(
            'action' => $this->generateUrl('fechacierre_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing FechaCierre entity.
     *
     * @Route("/{id}/update", name="fechacierre_update")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:FechaCierre:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        
        //Session
        $session = $this->getRequest()->getSession();
        
        //Seguridad
        $securityContext = $this->get('security.context');
        
        if ((true === $securityContext->isGranted('ROLE_16')) || (true === $securityContext->isGranted('ROLE_17'))) {
            
        
            $em = $this->getDoctrine()->getManager("ms_haberes_web");

            $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:FechaCierre')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find FechaCierre entity.');
            }

            $editForm = $this->createForm(new FechaCierreType(),$entity);
            $editForm->handleRequest($request);

            
            if ($editForm->isValid()) {
                $entity->setUsuaModi($this->getUser()->getUsername());
                $entity->setFechaModi(new \DateTime);
                $em->persist($entity);
                $em->flush();

                $request->getSession()->getFlashBag()->add(
                    'aviso_ok',
                    'Los datos fueron guardados correctamente!'
                );
                return $this->redirect($this->generateUrl('fechacierre_edit', array('id' => $id)));
            }
              
            $cupoHA   = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->findOneBy(array('refCupo'=> $entity->getRefCupo()));

            $editForm = $this->createEditForm($entity);
            $deleteForm = $this->createDeleteForm($id);

            $leyenda = '';



            $dependencia    =   $em->getRepository('LiquidacionesHaberesBundle:HADependencias')->find($cupoHA->getCupos()->getIdDependencia());

            $leyendaTipoLiquidacion     = $cupoHA->getCuentas()->getCuenta();
            $leyendaDependencia         = $dependencia->getId().' - '.$dependencia->getDependencia();
            $leyendaAnio                = $cupoHA->getCupos()->getAnio();
            $leyendaMes                 = $cupoHA->getCupos()->getMes();

            return array(
                'entity'      => $entity,
                'edit_form'   => $editForm->createView(),
                'delete_form' => $deleteForm->createView(),
                'lblTipoLiquidacion' => $leyendaTipoLiquidacion,
                'lblDependencia' => $leyendaDependencia,
                'lblAnio' => $leyendaAnio,
                'lblMes' => $leyendaMes,
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
     * Deletes a FechaCierre entity.
     *
     * @Route("/{id}", name="fechacierre_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager("ms_haberes_web");
            $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:FechaCierre')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find FechaCierre entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('fechacierre'));
    }

    /**
     * Creates a form to delete a FechaCierre entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('fechacierre_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
