<?php

namespace Liquidaciones\CuposAnualesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/index", name="inicio")
     * @Template()
     */
    public function indexAction()
    {
        return array('name' => 'Bienvenidos');
    }
    
    
    /**
     * @Route("/volver_atras", name="volver_atras")
     * @Template()
     */
    public function volverAction()
    {
        $breadcrumbs = $this->getRequest()->getSession()->get('breadcrumbs_partenovedades');
        array_pop($breadcrumbs);
        if ($bread = end($breadcrumbs)) {
            $path = $bread[1];
            $param = count($bread) > 2 ? $bread[2] : array();
        } else {
            $path = 'default';
            $param = array();
        }
        
        return $this->redirect($this->generateUrl($path, $param));
    }
    
    private function armarFormulario()
    {
        $defaultData = array(
            'dependencia' => null
        );
        $dependencias = $this->getUser()->getDependencias();
        //die(var_dump($dependencias));
        $form = $this->createFormBuilder($defaultData)
                ->add('dependencia', 'entity', array(
                    'class' => 'LiquidacionesIntranetBundle:DependenciasI',
                    'query_builder' => function(EntityRepository $er) use ($dependencias) {
                        return $er->createQueryBuilder('d')
                                ->select('d')
                                ->where("d.id in (:ids)")
                                ->setParameter('ids', $dependencias)
                                ->orderBy('d.nombre', 'ASC');
                    },
                    'required' => false,
                    'label' => 'Dependencia'))
                ->getForm();
         //die(var_dump($form));
         return $form;
    }
    
    /**
     * Formulario para cambiar la dependencia de la sesión.
     *
     * @Route("/formCambiarDependencia", name="form_cambiar_dependencia")
     */
    public function formCambiarDependenciaAction()
    {
        // Formulario de cambio de dependencia.
        $form = $this->armarFormulario();

        return $this->render(
            'LiquidacionesIntranetBundle:Default:formCambiarDependencia.html.twig',
            array('form' => $form->createView())
        );
    }
    
    /**
     * Cambiar la dependencia de la sesión.
     *
     * @Route("/cambiarDependencia", name="cambiar_dependencia")
     * @Method("POST")
     */
    public function cambiarDependenciaAction(Request $request) {
        $peticion = $this->getRequest();

        $form = $this->armarFormulario();
        $form->bind($peticion);
        
        if ($form->isValid()) {
            // Ésto habría que mejorarlo para unificar criterio de abstracción orientado a objetos
            $_SESSION['MULTIDEP_ACTUAL'] = $form->get('dependencia')->getData()->getId();
            $_SESSION['MULTIDEP_ACTUAL_DESC'] = $form->get('dependencia')->getData()->getNombre();
            $_SESSION['MULTIDEP_ACTUAL_CODIGO'] = $form->get('dependencia')->getData()->getCodigo();
            $_SESSION['MULTIDEP_ACTUAL_ESTABLE_ID'] = $form->get('dependencia')->getData()->getEstableId();
            
            $this->getRequest()->getSession()->getFlashBag()->add('aviso_exito', 
                    'Se ha cambiado con éxito la dependencia asociada al usuario.');
        } else {
            $this->getRequest()->getSession()->getFlashBag()->add('aviso_error', 
                    'Error. Vuelva a intentar el cambio de la dependencia.');
        }

        return $this->redirect($this->generateUrl('inicio'));
    }
}
