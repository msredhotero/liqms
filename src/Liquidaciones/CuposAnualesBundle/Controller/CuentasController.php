<?php

namespace Liquidaciones\CuposAnualesBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Liquidaciones\CuposAnualesBundle\Entity\Cuentas;
use Liquidaciones\CuposAnualesBundle\Form\CuentasType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;

/**
 * Cuentas controller.
 *
 * @Route("/cuentas")
 */
class CuentasController extends Controller
{
    /**
     * Lists all Cuentas entities.
     *
     * @Route("/", name="cuentas")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $usr = $this->get('security.context')->getToken()->getUser();
        $entities = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->findAll();

        return array(
            'entities' => $entities,
        );
    }
        
    
    
    
    /**
     * Finds and displays a Cuentas entity.
     *
     * @Route("/{id}/show", name="cuentas_show")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $usr = $this->get('security.context')->getToken()->getUser();
        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cuentas entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to create a new Cuentas entity.
     *
     * @Route("/new", name="cuentas_new")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $entity = new Cuentas();
        
        $entity->setActivo('1');
        $entity->setCodOperacion('');
        $entity->setConceptoMS('41');
        $entity->setCuenta('Reemplazos de Guardia - Pago Deuda');
        $entity->setEsPresupuestaria('1');
        $entity->setIdTipoLiquidacion(24);
        $entity->setModoCarga('rg');
        /*
        $form = $this->createFormBuilder($entity)
                ->add('conceptoMS')
            ->add('idTipoLiquidacion')
            ->add('cuenta','text'
                 , array('label'  => 'Cuenta'
                        ,'attr'   =>  array('class'   => 'form-control')
                        ))
            ->add('activo')
            ->add('modoCarga')
            ->add('save', 'submit');
        */
        $form   = $this->createForm(new CuentasType(), $entity);

        return $this->render('LiquidacionesCuposAnualesBundle:Cuentas:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new Cuentas entity.
     *
     * @Route("/create", name="cuentas_create")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:Cuentas:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity  = new Cuentas();
        $form = $this->createForm(new CuentasType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager("ms_haberes_web");
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cuentas_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Cuentas entity.
     *
     * @Route("/{id}/edit", name="cuentas_edit")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");
        $usr = $this->get('security.context')->getToken()->getUser();
        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cuentas entity.');
        }

        $editForm = $this->createForm(new CuentasType(), $entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Edits an existing Cuentas entity.
     *
     * @Route("/{id}/update", name="cuentas_update")
     * @Method("POST")
     * @Template("LiquidacionesCuposAnualesBundle:Cuentas:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager("ms_haberes_web");

        $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Cuentas entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createForm(new CuentasType(), $entity);
        $editForm->bind($request);

        if ($editForm->isValid()) {
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('cuentas_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Cuentas entity.
     *
     * @Route("/{id}/delete", name="cuentas_delete")
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager("ms_haberes_web");
            $entity = $em->getRepository('LiquidacionesCuposAnualesBundle:Cuentas')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Cuentas entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('cuentas'));
    }

    private function createDeleteForm($id)
    {
        return $this->createFormBuilder(array('id' => $id))
            ->add('id', 'hidden')
            ->getForm()
        ;
    }
    
    
    
}
