<?php

namespace Liquidaciones\CuposAnualesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Liquidaciones\CuposAnualesBundle\Entity\CuposHATiposLiquidacion;
use Liquidaciones\CuposAnualesBundle\Form\CuposHATiposLiquidacionType;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\ActionsColumn;
use APY\DataGridBundle\Grid\Column\TextColumn;

/**
 * CuposHATiposLiquidacion controller.
 *
 * @Route("/cuposhatiposliquidacion")
 */
class CuposHATiposLiquidacionController extends Controller
{

    /**
     * Lists all CuposHATiposLiquidacion entities.
     *
     * @Route("/", name="cuposhatiposliquidacion")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->findAll();

        $source = new Entity('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion');
        
        $grid = $this->get('grid');
        
        // Set the source
        $grid->setSource($source);
        $grid->setActionsColumnSize(90);
        $grid->setLimits(25,50,75);
        $grid->setActionsColumnTitle('Opcionnes');
        
        $actionsColumn = new ActionsColumn('action_column', 'Acciones',array('width' => '120'));
        $actionsColumn->setSeparator("<br/>");
        //$grid->addColumn($actionsColumn);
        
        // Add row actions in the default row actions column
        $myRowAction = new RowAction('Ver', 'cupos_showvista');
        $myRowAction->setRouteParameters(array('refCupo'));
        $grid->addRowAction($myRowAction);
        //
        //$myRowAction->setColumn('action_column');
        
        //$grid->addRowAction($myRowAction);

        $myRowAction2 = new RowAction('Modificar', 'cupos_editvista');
        $myRowAction2->setRouteParameters(array('refCupo'));
        $grid->addRowAction($myRowAction2);
        //$myRowAction->setColumn('action_column');
        //$myRowAction->setRouteParameters(array('id'));
        //$grid->addRowAction($myRowAction);
        
        
        
        return $grid->getGridResponse('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion:index.html.twig',
                array('grid'=>$grid)); 
    }
    /**
     * Creates a new CuposHATiposLiquidacion entity.
     *
     * @Route("/", name="cuposhatiposliquidacion_create")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new CuposHATiposLiquidacion();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cuposhatiposliquidacion_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a CuposHATiposLiquidacion entity.
    *
    * @param CuposHATiposLiquidacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(CuposHATiposLiquidacion $entity)
    {
        $form = $this->createForm(new CuposHATiposLiquidacionType(), $entity, array(
            'action' => $this->generateUrl('cuposhatiposliquidacion_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new CuposHATiposLiquidacion entity.
     *
     * @Route("/new", name="cuposhatiposliquidacion_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new CuposHATiposLiquidacion();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a CuposHATiposLiquidacion entity.
     *
     * @Route("/{id}", name="cuposhatiposliquidacion_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CuposHATiposLiquidacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing CuposHATiposLiquidacion entity.
     *
     * @Route("/{id}/edit", name="cuposhatiposliquidacion_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CuposHATiposLiquidacion entity.');
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
    * Creates a form to edit a CuposHATiposLiquidacion entity.
    *
    * @param CuposHATiposLiquidacion $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(CuposHATiposLiquidacion $entity)
    {
        $form = $this->createForm(new CuposHATiposLiquidacionType(), $entity, array(
            'action' => $this->generateUrl('cuposhatiposliquidacion_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing CuposHATiposLiquidacion entity.
     *
     * @Route("/{id}", name="cuposhatiposliquidacion_update")
     * @Method("PUT")
     * @Template("LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find CuposHATiposLiquidacion entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('cuposhatiposliquidacion_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a CuposHATiposLiquidacion entity.
     *
     * @Route("/{id}", name="cuposhatiposliquidacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find CuposHATiposLiquidacion entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('cuposhatiposliquidacion'));
    }

    /**
     * Creates a form to delete a CuposHATiposLiquidacion entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('cuposhatiposliquidacion_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
