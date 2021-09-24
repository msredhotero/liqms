<?php

namespace Liquidaciones\HaberesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Liquidaciones\HaberesBundle\Entity\HAImputacionPresupuestaria;
use Liquidaciones\HaberesBundle\Form\HAImputacionPresupuestariaType;

/**
 * HAImputacionPresupuestaria controller.
 *
 * @Route("/haimputacionpresupuestaria")
 */
class HAImputacionPresupuestariaController extends Controller
{

    /**
     * Lists all HAImputacionPresupuestaria entities.
     *
     * @Route("/", name="haimputacionpresupuestaria")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager("ms_haberes");

        $entities = $em->getRepository('LiquidacionesHaberesBundle:HAImputacionPresupuestaria')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new HAImputacionPresupuestaria entity.
     *
     * @Route("/", name="haimputacionpresupuestaria_create")
     * @Method("POST")
     * @Template("LiquidacionesHaberesBundle:HAImputacionPresupuestaria:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new HAImputacionPresupuestaria();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('haimputacionpresupuestaria_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a HAImputacionPresupuestaria entity.
    *
    * @param HAImputacionPresupuestaria $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(HAImputacionPresupuestaria $entity)
    {
        $form = $this->createForm(new HAImputacionPresupuestariaType(), $entity, array(
            'action' => $this->generateUrl('haimputacionpresupuestaria_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new HAImputacionPresupuestaria entity.
     *
     * @Route("/new", name="haimputacionpresupuestaria_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new HAImputacionPresupuestaria();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a HAImputacionPresupuestaria entity.
     *
     * @Route("/{id}", name="haimputacionpresupuestaria_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesHaberesBundle:HAImputacionPresupuestaria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HAImputacionPresupuestaria entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing HAImputacionPresupuestaria entity.
     *
     * @Route("/{id}/edit", name="haimputacionpresupuestaria_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesHaberesBundle:HAImputacionPresupuestaria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HAImputacionPresupuestaria entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a HAImputacionPresupuestaria entity.
    *
    * @param HAImputacionPresupuestaria $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(HAImputacionPresupuestaria $entity)
    {
        $form = $this->createForm(new HAImputacionPresupuestariaType(), $entity, array(
            'action' => $this->generateUrl('haimputacionpresupuestaria_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing HAImputacionPresupuestaria entity.
     *
     * @Route("/{id}", name="haimputacionpresupuestaria_update")
     * @Method("PUT")
     * @Template("LiquidacionesHaberesBundle:HAImputacionPresupuestaria:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesHaberesBundle:HAImputacionPresupuestaria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HAImputacionPresupuestaria entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('haimputacionpresupuestaria_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a HAImputacionPresupuestaria entity.
     *
     * @Route("/{id}", name="haimputacionpresupuestaria_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LiquidacionesHaberesBundle:HAImputacionPresupuestaria')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find HAImputacionPresupuestaria entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('haimputacionpresupuestaria'));
    }

    /**
     * Creates a form to delete a HAImputacionPresupuestaria entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('haimputacionpresupuestaria_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    
    
    
    
    
    /**
     * 
     * @Route("/traerTodosImputacion2/{cuenta}", name="imputacionpresupuestaria_traerTodosImputacion2")
     */
    public function traerTodosImputacionAction($cuenta)
    {
        $em = $this->getDoctrine()->getManager("referencias");
        $emL = $this->getDoctrine()->getManager();
        $ent_cuenta = new Cuentas();
        $ent_cuenta = $emL->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->find($cuenta);
        
        $idTipoLiquidacion = $ent_cuenta->getIdTipoLiquidacion();
        
        $sql =           "select im.id, im.programaDescripcion, im.descImputacionpresup "
                        ." FROM        LiquidacionesReferenciasBundle:Imputacionpresupuestaria im ";

        switch ($idTipoLiquidacion) {
            case 24://reemplazos
                $sql = $sql." WHERE im.regimenestatutario = '09' and im.agrupamiento = '05' ";

                break;
            case 39://horas extras

                    $sql = $sql." WHERE im.descImputacionpresup = 'Horas Extras' ";
                break;
            case 33://horas reba
                $sql = $sql." WHERE im.id = 1071 and im.descImputacionpresup = 'Horas Extras' ";
                break;
            case 40:// ART 48
                $sql = $sql." WHERE im.regimenestatutario = '09' and im.agrupamiento = '05' ";
                break;
            case 15:// Modulos Contingencias
                $sql = $sql." WHERE im.id = 499 ";
                break;
            case 35:// Modulos Contingencias
                $sql = $sql." WHERE im.id = 499 ";
                break;
            case 31:// Modulos Contingencias
                $sql = $sql." WHERE im.id = 432 ";
                break;
            case 36:// Modulos Contingencias
                $sql = $sql." WHERE im.id = 432 ";
                break;

        }

        
        $consulta = $em->createQuery($sql);
        /*
        $consulta = $em->createQuery("select im.id "
                        ." FROM        LiquidacionesReferenciasBundle:Imputacionpresupuestaria im "
                        ." INNER JOIN "
                        ." LiquidacionesReferenciasBundle:ImputacionDependencias d "
                        ." WITH          im.id = d.idImputacionPresupuestaria "
                        ." WHERE       d.codigoDependencia = ".$depe." and im.regimenestatutario = '09' and im.agrupamiento = '05' ");
        */
        /*$consulta = $em->createQuery("select d.id, d.idImputacionPresupuestaria "
                        ." from        LiquidacionesReferenciasBundle:ImputacionDependencias d ");*/
        
        //$consulta = $em->createQuery("SELECT d.id, d.nombre, d.codigo, d.tipoDependencia FROM LiquidacionesReferenciasBundle:Dependencias d ");
        //$response = new Response('marcos');
        //$response = new Response(json_encode(array('id'=>1, 'Cuenta'=>'Reemplazos')));
        $response = new Response(json_encode($consulta->getResult()));
        $response->headers->set('content-type','application/json');
        
        return $response;
        
    }
}
