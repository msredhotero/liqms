<?php

namespace Liquidaciones\HaberesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Liquidaciones\HaberesBundle\Entity\HAConceptosValor;
use Liquidaciones\HaberesBundle\Form\HAConceptosValorType;

/**
 * HAConceptosValor controller.
 *
 * @Route("/haconceptosvalor")
 */
class HAConceptosValorController extends Controller
{

    /**
     * Lists all HAConceptosValor entities.
     *
     * @Route("/", name="haconceptosvalor")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager("ms_haberes");

        $entities = $em->getRepository('LiquidacionesHaberesBundle:HAConceptosValor')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new HAConceptosValor entity.
     *
     * @Route("/", name="haconceptosvalor_create")
     * @Method("POST")
     * @Template("LiquidacionesHaberesBundle:HAConceptosValor:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new HAConceptosValor();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('haconceptosvalor_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a HAConceptosValor entity.
    *
    * @param HAConceptosValor $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(HAConceptosValor $entity)
    {
        $form = $this->createForm(new HAConceptosValorType(), $entity, array(
            'action' => $this->generateUrl('haconceptosvalor_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new HAConceptosValor entity.
     *
     * @Route("/new", name="haconceptosvalor_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new HAConceptosValor();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a HAConceptosValor entity.
     *
     * @Route("/{id}", name="haconceptosvalor_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesHaberesBundle:HAConceptosValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HAConceptosValor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing HAConceptosValor entity.
     *
     * @Route("/{id}/edit", name="haconceptosvalor_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesHaberesBundle:HAConceptosValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HAConceptosValor entity.');
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
    * Creates a form to edit a HAConceptosValor entity.
    *
    * @param HAConceptosValor $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(HAConceptosValor $entity)
    {
        $form = $this->createForm(new HAConceptosValorType(), $entity, array(
            'action' => $this->generateUrl('haconceptosvalor_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing HAConceptosValor entity.
     *
     * @Route("/{id}", name="haconceptosvalor_update")
     * @Method("PUT")
     * @Template("LiquidacionesHaberesBundle:HAConceptosValor:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesHaberesBundle:HAConceptosValor')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find HAConceptosValor entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('haconceptosvalor_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a HAConceptosValor entity.
     *
     * @Route("/{id}", name="haconceptosvalor_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LiquidacionesHaberesBundle:HAConceptosValor')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find HAConceptosValor entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('haconceptosvalor'));
    }

    /**
     * Creates a form to delete a HAConceptosValor entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('haconceptosvalor_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
