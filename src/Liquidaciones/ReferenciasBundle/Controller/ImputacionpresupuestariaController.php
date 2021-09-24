<?php

namespace Liquidaciones\ReferenciasBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Liquidaciones\ReferenciasBundle\Entity\Imputacionpresupuestaria;
use Liquidaciones\ReferenciasBundle\Form\ImputacionpresupuestariaType;
use Symfony\Component\HttpFoundation\Response;
use Liquidaciones\CuposAnualesBundle\Entity\Cuentas;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
/**
 * Imputacionpresupuestaria controller.
 *
 * @Route("/imputacionpresupuestaria")
 */
class ImputacionpresupuestariaController extends Controller
{

    /**
     * Lists all Imputacionpresupuestaria entities.
     *
     * @Route("/", name="imputacionpresupuestaria")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LiquidacionesReferenciasBundle:Imputacionpresupuestaria')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Imputacionpresupuestaria entity.
     *
     * @Route("/", name="imputacionpresupuestaria_create")
     * @Method("POST")
     * @Template("LiquidacionesReferenciasBundle:Imputacionpresupuestaria:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Imputacionpresupuestaria();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('imputacionpresupuestaria_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Imputacionpresupuestaria entity.
    *
    * @param Imputacionpresupuestaria $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Imputacionpresupuestaria $entity)
    {
        $form = $this->createForm(new ImputacionpresupuestariaType(), $entity, array(
            'action' => $this->generateUrl('imputacionpresupuestaria_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Imputacionpresupuestaria entity.
     *
     * @Route("/new", name="imputacionpresupuestaria_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Imputacionpresupuestaria();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Imputacionpresupuestaria entity.
     *
     * @Route("/{id}", name="imputacionpresupuestaria_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesReferenciasBundle:Imputacionpresupuestaria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Imputacionpresupuestaria entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Imputacionpresupuestaria entity.
     *
     * @Route("/{id}/edit", name="imputacionpresupuestaria_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesReferenciasBundle:Imputacionpresupuestaria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Imputacionpresupuestaria entity.');
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
    * Creates a form to edit a Imputacionpresupuestaria entity.
    *
    * @param Imputacionpresupuestaria $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Imputacionpresupuestaria $entity)
    {
        $form = $this->createForm(new ImputacionpresupuestariaType(), $entity, array(
            'action' => $this->generateUrl('imputacionpresupuestaria_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Imputacionpresupuestaria entity.
     *
     * @Route("/{id}", name="imputacionpresupuestaria_update")
     * @Method("PUT")
     * @Template("LiquidacionesReferenciasBundle:Imputacionpresupuestaria:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesReferenciasBundle:Imputacionpresupuestaria')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Imputacionpresupuestaria entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('imputacionpresupuestaria_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Imputacionpresupuestaria entity.
     *
     * @Route("/{id}", name="imputacionpresupuestaria_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LiquidacionesReferenciasBundle:Imputacionpresupuestaria')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Imputacionpresupuestaria entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('imputacionpresupuestaria'));
    }

    /**
     * Creates a form to delete a Imputacionpresupuestaria entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('imputacionpresupuestaria_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    
}
