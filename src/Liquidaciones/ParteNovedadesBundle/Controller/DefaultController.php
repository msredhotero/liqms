<?php

namespace Liquidaciones\ParteNovedadesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Default controller.
 *
 * @Route("/")
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="default")
     * @Template()
     */
    public function indexAction()
    {
        $session = $this->getRequest()->getSession();
        $session->set('breadcrumbs_partenovedades', array(
            array('Parte de novedades', 'default'))
        );
        
        return array('name' => 'Andrés');
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
}
