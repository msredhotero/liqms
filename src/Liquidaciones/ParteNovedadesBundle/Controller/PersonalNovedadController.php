<?php

namespace Liquidaciones\ParteNovedadesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use MinSaludBA\ParteNovedadesBundle\Entity\PersonalNovedad;
use MinSaludBA\ParteNovedadesBundle\Form\PersonalNovedadType;
use APY\DataGridBundle\Grid\Source\Entity;

/**
 * PersonalNovedad controller.
 *
 * @Route("/personalnovedad")
 */
class PersonalNovedadController extends Controller
{
    /**
     * Lists all PersonalNovedad entities.
     *
     * @Route("/{IdPersonalCargo}/list", name="personalnovedad_list")
     * @Template()
     */
    public function indexAction($IdPersonalCargo)
    {
        $session = $this->getRequest()->getSession();
        $session->set('breadcrumbs_partenovedades', array(
            array('Parte de novedades', 'default'))
        );
        
        $source = new Entity('LiquidacionesParteNovedadesBundle:PersonalNovedad',null,'partenovedades');
        $tabla = $source->getTableAlias();
        $source->manipulateQuery(
            function ($query) use ($IdPersonalCargo, $tabla)
            {
            $query->andWhere($tabla.".Plantel = " .$IdPersonalCargo);
            $query->orderBy($tabla.".FechaDesde");
            }
        );
        
        // Get a grid instance
        $grid = $this->get('grid');

        // Set the source
        $grid->setSource($source);
        
        // Set the selector of the number of items per page
        $grid->setLimits(array(10,20,30,40,50));
        
        // Set the default page
        $grid->setDefaultPage(1);

        return $grid->getGridResponse('LiquidacionesParteNovedadesBundle:PersonalNovedad:index.html.twig',
                array('grid'=>$grid, 'id'=>$IdPersonalCargo)); 
    }

    /**
     * Finds and displays a PersonalNovedad entity.
     *
     * @Route("/{id}/show", name="personalnovedad_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesParteNovedadesBundle:PersonalNovedad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PersonalNovedad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new PersonalNovedad entity.
     *
     * @Route("/{IdPersonalCargo}/new", name="personalnovedad_new")
     * @Method("POST|GET")
     * @Template()
     */
    public function newAction($IdPersonalCargo, Request $request)
    {
        /* @var $connection \PDO */
        $conn =  $this->getDoctrine()->getManager("partenovedades")
                ->getConnection();

        $stmt = $conn->prepare('EXEC novedades.dbo.traerPlantel2 300,338992');
       
        $stmt->execute();
        
        $results = $stmt->fetchAll();
        
        die(var_dump($results));
        
        
        $entity = new PersonalNovedad();

        $em = $this->getDoctrine()->getManager('partenovedades');
        $plantelEntity = $em->getRepository('LiquidacionesParteNovedadesBundle:Plantel')->find($IdPersonalCargo);
        
        $form = $this->createForm(new PersonalNovedadType($plantelEntity), $entity);
        
        $form->bind($request);
        
        if ($form->isValid()) {
            
            
            return array(
                'entity' => $entity,
                'target_route' => 'personalnovedad_create',
                'plantelEntity' => $plantelEntity,
                'form'   => $form->createView(),
            );
        }

        return array(
            'entity' => $entity,
            'target_route' => 'personalnovedad_new',
            'plantelEntity' => $plantelEntity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a new PersonalNovedad entity.
     *
     * @Route("/create", name="personalnovedad_create")
     * @Method("POST")
     * @Template("LiquidacionesParteNovedadesBundle:PersonalNovedad:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new PersonalNovedad();
        
        $form = $this->createForm(new PersonalNovedadType(), $entity);
        
        $form->bind($request);
        
        if ($form->isValid()) {
            $entity->setEliminado(0);
            
            $entity->setFechaModi(null);
            $entity->setUsuaModi($this->getUser()->getUsername());
            $entity->setDepeModi($this->getUser()->IdEstablecimiento);
            
            $em = $this->getDoctrine()->getManager('partenovedades');

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('personalnovedad_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing PersonalNovedad entity.
     *
     * @Route("/{id}/edit", name="personalnovedad_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesParteNovedadesBundle:PersonalNovedad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PersonalNovedad entity.');
        }

        $editForm = $this->createForm(new PersonalNovedadType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing PersonalNovedad entity.
     *
     * @Route("/{id}/update", name="personalnovedad_update")
     * @Method("POST")
     * @Template("LiquidacionesParteNovedadesBundle:PersonalNovedad:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('LiquidacionesParteNovedadesBundle:PersonalNovedad')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PersonalNovedad entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new PersonalNovedadType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('personalnovedad_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a PersonalNovedad entity.
     *
     * @Route("/{id}/delete", name="personalnovedad_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('LiquidacionesParteNovedadesBundle:PersonalNovedad')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PersonalNovedad entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('personalnovedad'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
}
