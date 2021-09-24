<?php

namespace Liquidaciones\CuposAnualesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Liquidaciones\CuposAnualesBundle\Entity\Vacantes;
use Liquidaciones\CuposAnualesBundle\Form\VacantesType;

/**
 * Vacantes controller.
 *
 * @Route("/vacantes")
 */
class VacantesController extends Controller
{

    /**
     * Lists all Vacantes entities.
     *
     * @Route("/", name="vacantes")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        //Session
        $session = $this->getRequest()->getSession();
        
        //Seguridad
        $securityContext = $this->get('security.context');
        
        if (false === $securityContext->isGranted('ROLE_21')) {
            $this->get("session")->getFlashBag()->add(
                'aviso_error',
                'No posee permisos para ingresar al contenido solicitado!'
            );

            return $this->redirect($this->generateUrl('inicio'));
        }
        
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        
        $sql = "SELECT v.id, 
                    c.anio,
                    c.mes,
                    CONVERT(VARCHAR,d.IdDependencia) + ' - ' + d.dependencia as codigo,
                    round((convert(decimal(18,2), v.vacantes) / 5),2) as vacantes,
                    d.regsan1 as COD_TIPO_DEPENDENCIA,
                    cu.cuenta
                FROM LiquidacionesWeb.dbo.vacantes v
                inner join LiquidacionesWeb.dbo.cupos c on c.id = v.refcupo
                inner join LiquidacionesWeb.dbo.CuposHATiposLiquidacion ca on c.id = ca.refcupo and ca.refcuenta not in (23,25)
                inner join LiquidacionesWeb.dbo.cuentas cu on cu.id = ca.refcuenta
                inner join Haberes.General.HADependencias d on d.IdDependencia = c.iddependencia
                ";
        
        //$entities = $em->createQuery($sql)->getResult();
        $connection = $this -> getDoctrine() 
                                -> getManager("ms_haberes_web") 
                                -> getConnection();

        $entities = $connection ->  prepare($sql);

        $entities -> execute();
        

        

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Vacantes entity.
     *
     * @Route("/", name="vacantes_create")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:Vacantes:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Vacantes();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        //Session
        $session = $this->getRequest()->getSession();
        
        //Seguridad
        $securityContext = $this->get('security.context');
        
        $usr = $this->get('security.context')->getToken()->getUser();
        
        $entity->setUsuaCrea($usr->getUsername());
        $entity->setUsuaModi($usr->getUsername());

        $entity->setFechaCrea(new \DateTime);
        $entity->setFechaModi(new \DateTime);
            
            
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager("ms_haberes_web");
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('vacantes_new'));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
    * Creates a form to create a Vacantes entity.
    *
    * @param Vacantes $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createCreateForm(Vacantes $entity)
    {
        $form = $this->createForm(new VacantesType('new',0), $entity, array(
            'action' => $this->generateUrl('vacantes_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Vacantes entity.
     *
     * @Route("/new", name="vacantes_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        //Session
        $session = $this->getRequest()->getSession();
        
        //Seguridad
        $securityContext = $this->get('security.context');
        
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        
        $entity = new Vacantes();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Vacantes entity.
     *
     * @Route("/{id}", name="vacantes_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        //Session
        $session = $this->getRequest()->getSession();
        
        //Seguridad
        $securityContext = $this->get('security.context');
        
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Vacantes')->find($id);
        
        

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vacantes entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        
        

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Vacantes entity.
     *
     * @Route("/{id}/edit", name="vacantes_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        //Session
        $session = $this->getRequest()->getSession();
        
        //Seguridad
        $securityContext = $this->get('security.context');
        
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Vacantes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vacantes entity.');
        }

        if ($entity->getCupos()->getCupoestado()->getId() != 2) {
            $this->get("session")->getFlashBag()->add(
            'aviso_error',
            'La vacante no puede modificarse, el cupo no esta abierto!'
            );
            return $this->redirect($this->generateUrl('vacantes'));
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);
        
        $descripcion = $entity->getCupos()->getCuposanuales()->getDescripcion().' - '.$entity->getCupos()->getAnio().'/'.$entity->getCupos()->getMes().' Depe: '.$entity->getCupos()->getIdDependencia();

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'descripcion' => $descripcion,
        );
    }

    /**
    * Creates a form to edit a Vacantes entity.
    *
    * @param Vacantes $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Vacantes $entity)
    {
        $form = $this->createForm(new VacantesType('edit',$entity->getId()), $entity, array(
            'action' => $this->generateUrl('vacantes_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }
    /**
     * Edits an existing Vacantes entity.
     *
     * @Route("/{id}", name="vacantes_update")
     * @Method("PUT")
     * @Template("LiquidacionesCuposAnualesBundle:Vacantes:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        
        //Session
        $session = $this->getRequest()->getSession();
        
        //Seguridad
        $securityContext = $this->get('security.context');
        
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Vacantes')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Vacantes entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        
        $usr = $this->get('security.context')->getToken()->getUser();
        
        $entity->setUsuaCrea($usr->getUsername());
        $entity->setUsuaModi($usr->getUsername());

        $entity->setFechaCrea(new \DateTime);
        $entity->setFechaModi(new \DateTime);
        
        //die(var_dump($editForm));

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('vacantes_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Vacantes entity.
     *
     * @Route("/{id}", name="vacantes_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager("ms_haberes_web");
            $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Vacantes')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Vacantes entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('vacantes'));
    }

    /**
     * Creates a form to delete a Vacantes entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('vacantes_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
