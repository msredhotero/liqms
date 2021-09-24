<?php

namespace Liquidaciones\CuposAnualesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Liquidaciones\CuposAnualesBundle\Entity\PerfilesLiquidaciones;
use Liquidaciones\CuposAnualesBundle\Form\PerfilesLiquidacionesType;

/**
 * PerfilesLiquidaciones controller.
 *
 * @Route("/perfilesliquidaciones")
 */
class PerfilesLiquidacionesController extends Controller
{

    /**
     * Lists all PerfilesLiquidaciones entities.
     *
     * @Route("/", name="perfilesliquidaciones")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:PerfilesLiquidaciones')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new PerfilesLiquidaciones entity.
     *
     * @Route("/", name="perfilesliquidaciones_create")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:PerfilesLiquidaciones:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new PerfilesLiquidaciones();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('perfilesliquidaciones_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a PerfilesLiquidaciones entity.
    *
    * @param PerfilesLiquidaciones $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(PerfilesLiquidaciones $entity)
    {
        $form = $this->createForm(new PerfilesLiquidacionesType(), $entity, array(
            'action' => $this->generateUrl('perfilesliquidaciones_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new PerfilesLiquidaciones entity.
     *
     * @Route("/new", name="perfilesliquidaciones_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new PerfilesLiquidaciones();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a PerfilesLiquidaciones entity.
     *
     * @Route("/{id}", name="perfilesliquidaciones_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:PerfilesLiquidaciones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PerfilesLiquidaciones entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing PerfilesLiquidaciones entity.
     *
     * @Route("/{id}/edit", name="perfilesliquidaciones_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:PerfilesLiquidaciones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PerfilesLiquidaciones entity.');
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
    * Creates a form to edit a PerfilesLiquidaciones entity.
    *
    * @param PerfilesLiquidaciones $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(PerfilesLiquidaciones $entity)
    {
        $form = $this->createForm(new PerfilesLiquidacionesType(), $entity, array(
            'action' => $this->generateUrl('perfilesliquidaciones_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing PerfilesLiquidaciones entity.
     *
     * @Route("/{id}", name="perfilesliquidaciones_update")
     * @Method("PUT")
     * @Template("LiquidacionesCuposAnualesBundle:PerfilesLiquidaciones:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:PerfilesLiquidaciones')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PerfilesLiquidaciones entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('perfilesliquidaciones_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a PerfilesLiquidaciones entity.
     *
     * @Route("/{id}", name="perfilesliquidaciones_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:PerfilesLiquidaciones')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PerfilesLiquidaciones entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('perfilesliquidaciones'));
    }

    /**
     * Creates a form to delete a PerfilesLiquidaciones entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('perfilesliquidaciones_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
