<?php

namespace Liquidaciones\ReferenciasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Liquidaciones\ReferenciasBundle\Entity\Dependencias;
use Liquidaciones\ReferenciasBundle\Entity\TipoDependencia;
use Liquidaciones\ReferenciasBundle\Form\DependenciasType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
/**
 * Dependencias controller.
 *
 * @Route("/dependencias")
 */
class DependenciasController extends Controller
{

    /**
     * Lists all Dependencias entities.
     *
     * @Route("/", name="dependencias")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager("referencias");

        $entities = $em->getRepository('LiquidacionesReferenciasBundle:Dependencias')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Dependencias entity.
     *
     * @Route("/", name="dependencias_create")
     * @Method("POST")
     * @Template("LiquidacionesReferenciasBundle:Dependencias:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Dependencias();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('dependencias_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Dependencias entity.
    *
    * @param Dependencias $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Dependencias $entity)
    {
        $form = $this->createForm(new DependenciasType(), $entity, array(
            'action' => $this->generateUrl('dependencias_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Dependencias entity.
     *
     * @Route("/new", name="dependencias_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Dependencias();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Dependencias entity.
     *
     * @Route("/{id}", name="dependencias_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesReferenciasBundle:Dependencias')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Dependencias entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Dependencias entity.
     *
     * @Route("/{id}/edit", name="dependencias_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesReferenciasBundle:Dependencias')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Dependencias entity.');
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
    * Creates a form to edit a Dependencias entity.
    *
    * @param Dependencias $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Dependencias $entity)
    {
        $form = $this->createForm(new DependenciasType(), $entity, array(
            'action' => $this->generateUrl('dependencias_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Dependencias entity.
     *
     * @Route("/{id}", name="dependencias_update")
     * @Method("PUT")
     * @Template("LiquidacionesReferenciasBundle:Dependencias:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesReferenciasBundle:Dependencias')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Dependencias entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('dependencias_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Dependencias entity.
     *
     * @Route("/{id}", name="dependencias_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LiquidacionesReferenciasBundle:Dependencias')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Dependencias entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('dependencias'));
    }

    /**
     * Creates a form to delete a Dependencias entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('dependencias_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
    
    
    /**
     * 
     * @Route("/verDependencias2/{regsan}", name="dependencias_verDependencias2")
     */
    public function verDependencias2Action($regsan)
    {
        $em = $this->getDoctrine()->getManager();
        $consulta = $em->createQuery("SELECT d.id,d.nombre as dependencia,d.codigo as regsan FROM LiquidacionesReferenciasBundle:Dependencias d "
                                    . " join LiquidacionesReferenciasBundle:TipoDependencia tp "
                                    . " with d.tipodependencia = tp.id "
                                    . " where tp.id = ".$regsan." and d.codigo is not null ORDER BY d.codigo ASC");
        //$consulta = $em->createQuery("SELECT d.id, d.nombre, d.codigo, d.tipoDependencia FROM LiquidacionesReferenciasBundle:Dependencias d ");
        //$response = new Response('marcos');
        //$response = new Response(json_encode(array('id'=>1, 'Cuenta'=>'Reemplazos')));
        $response = new Response(json_encode($consulta->getResult()));
        $response->headers->set('content-type','application/json');
        
        return $response;
        
    }
}
