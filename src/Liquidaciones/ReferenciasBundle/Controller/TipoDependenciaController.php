<?php

namespace Liquidaciones\ReferenciasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Liquidaciones\ReferenciasBundle\Entity\TipoDependencia;
use Liquidaciones\ReferenciasBundle\Form\TipoDependenciaType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
/**
 * TipoDependencia controller.
 *
 * @Route("/tipodependencia")
 */
class TipoDependenciaController extends Controller
{

    /**
     * Lists all TipoDependencia entities.
     *
     * @Route("/", name="tipodependencia")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager("referencias");

        $entities = $em->getRepository('LiquidacionesReferenciasBundle:TipoDependencia')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new TipoDependencia entity.
     *
     * @Route("/", name="tipodependencia_create")
     * @Method("POST")
     * @Template("LiquidacionesReferenciasBundle:TipoDependencia:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new TipoDependencia();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager("referencias");
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('tipodependencia_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a TipoDependencia entity.
    *
    * @param TipoDependencia $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(TipoDependencia $entity)
    {
        $form = $this->createForm(new TipoDependenciaType(), $entity, array(
            'action' => $this->generateUrl('tipodependencia_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new TipoDependencia entity.
     *
     * @Route("/new", name="tipodependencia_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new TipoDependencia();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a TipoDependencia entity.
     *
     * @Route("/{id}", name="tipodependencia_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager("referencias");

        $entity = $em->getRepository('LiquidacionesReferenciasBundle:TipoDependencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoDependencia entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing TipoDependencia entity.
     *
     * @Route("/{id}/edit", name="tipodependencia_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("referencias");

        $entity = $em->getRepository('LiquidacionesReferenciasBundle:TipoDependencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoDependencia entity.');
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
    * Creates a form to edit a TipoDependencia entity.
    *
    * @param TipoDependencia $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(TipoDependencia $entity)
    {
        $form = $this->createForm(new TipoDependenciaType(), $entity, array(
            'action' => $this->generateUrl('tipodependencia_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing TipoDependencia entity.
     *
     * @Route("/{id}", name="tipodependencia_update")
     * @Method("PUT")
     * @Template("LiquidacionesReferenciasBundle:TipoDependencia:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager("referencias");

        $entity = $em->getRepository('LiquidacionesReferenciasBundle:TipoDependencia')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find TipoDependencia entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('tipodependencia_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a TipoDependencia entity.
     *
     * @Route("/{id}", name="tipodependencia_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager("referencias");
            $entity = $em->getRepository('LiquidacionesReferenciasBundle:TipoDependencia')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find TipoDependencia entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('tipodependencia'));
    }

    /**
     * Creates a form to delete a TipoDependencia entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tipodependencia_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    
    /**
     * 
     * @Route("/verCodigoDepe/{id}", name="tipodependencia_verCodigoDepe")
     */
    public function verCodigoDepeAction($id)
    {
        $em = $this->getDoctrine()->getManager("referencias");
        $consulta = $em->createQuery("SELECT d.codTipoDependencia as codigo FROM LiquidacionesReferenciasBundle:TipoDependencia d where d.id = ".$id);
        //$consulta = $em->createQuery("SELECT d.id, d.nombre, d.codigo, d.tipoDependencia FROM LiquidacionesReferenciasBundle:Dependencias d ");
        //$response = new Response('marcos');
        //$response = new Response(json_encode(array('id'=>1, 'Cuenta'=>'Reemplazos')));
        $response = new Response(json_encode($consulta->getResult()));
        $response->headers->set('content-type','application/json');
        
        return $response;
        
    }
    
    
    /**
     * 
     * @Route("/verCodigoPorDepe/{id}", name="tipodependencia_verCodigoPorDepe")
     */
    public function verCodigoPorDepeAction($depe)
    {
        $em = $this->getDoctrine()->getManager("referencias");
        $consulta = $em->createQuery("SELECT d.codTipoDependencia as codigo FROM LiquidacionesReferenciasBundle:TipoDependencia d "
                . " inner join LiquidacionesReferenciasBundle:Dependencias dd "
                . " on d.id = dd.tipoDependencia "
                . " where dd.codigo = '".$depe."'");
        //$consulta = $em->createQuery("SELECT d.id, d.nombre, d.codigo, d.tipoDependencia FROM LiquidacionesReferenciasBundle:Dependencias d ");
        //$response = new Response('marcos');
        //$response = new Response(json_encode(array('id'=>1, 'Cuenta'=>'Reemplazos')));
        $response = new Response(json_encode($consulta->getResult()));
        $response->headers->set('content-type','application/json');
        
        return $response;
        
    }
    
    
    
    /**
     * 
     * @Route("/traerCodigoDepe/", name="tipodependencia_traerCodigoDepe")
     */
    public function traerCodigoDepeAction()
    {
        $em = $this->getDoctrine()->getManager("referencias");
        $consulta = $em->createQuery("SELECT d.id,d.codTipoDependencia as codigo,d.descTipoDependencia as descripcion FROM LiquidacionesReferenciasBundle:TipoDependencia d where d.codTipoDependencia is not null ");
        //$consulta = $em->createQuery("SELECT d.id, d.nombre, d.codigo, d.tipoDependencia FROM LiquidacionesReferenciasBundle:Dependencias d ");
        //$response = new Response('marcos');
        //$response = new Response(json_encode(array('id'=>1, 'Cuenta'=>'Reemplazos')));
        $response = new Response(json_encode($consulta->getResult()));
        $response->headers->set('content-type','application/json');
        
        return $response;
        
    }
}
