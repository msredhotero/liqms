<?php

namespace Liquidaciones\ParteNovedadesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use APY\DataGridBundle\Grid\Source\Entity;
use APY\DataGridBundle\Grid\Action\RowAction;
use APY\DataGridBundle\Grid\Column\ActionsColumn;

/**
 * Default controller.
 *
 * @Route("/plantel")
 */
class PlantelController extends Controller
{
    /**
     * @Route("/", name="plantel")
     * @Template()
     */
    public function indexAction()
    {
        $session = $this->getRequest()->getSession();
        $session->set('breadcrumbs_partenovedades', array(
            array('Parte de novedades', 'default'))
        );
        
        $usuarioDependencia = $this->getUser()->IdEstablecimiento;
        
        $source = new Entity('LiquidacionesParteNovedadesBundle:Plantel',null,'partenovedades');
        
        // Get a grid instance
        $grid = $this->get('grid');
        
        // Add a where condition to the query to get only bookmarks of the user
        $tableAlias = $source->getTableAlias();

        $source->manipulateQuery(function ($query) use ($tableAlias,$usuarioDependencia) {
            $query->andwhere($tableAlias . '.IdDependenciaPlantel = ' . $usuarioDependencia . ' or ' . $tableAlias . '.IdDestinoCobro = ' . $usuarioDependencia);
        });

        // Set the source
        $grid->setSource($source);
        
        // Set the selector of the number of items per page
        $grid->setLimits(array(10,20,30,40,50));
        
        // Set the default page
        $grid->setDefaultPage(1);
        
        //Orden
        $grid->setDefaultOrder('Apellido', 'asc');
        
        // Acciones
        $actionsColumn = new ActionsColumn('action_column', 'Acciones');
        $actionsColumn->setSeparator("<br/>");
        $grid->addColumn($actionsColumn);
        
        $myRowAction = new RowAction('Novedades', 'personalnovedad_list');
        $myRowAction->setRouteParameters(array('IdPersonalCargo'));
        $myRowAction->setColumn('action_column');
        $grid->addRowAction($myRowAction);
        
        $myRowAction = new RowAction('Licencias', 'plantel');
        $myRowAction->setRouteParameters(array('IdPersonalCargo'));
        $myRowAction->setColumn('action_column');
        $grid->addRowAction($myRowAction);
        
        $myRowAction = new RowAction('Comisiones', 'plantel');
        $myRowAction->setRouteParameters(array('IdPersonalCargo'));
        $myRowAction->setColumn('action_column');
        $grid->addRowAction($myRowAction);
        
        $myRowAction = new RowAction('Traslados', 'plantel');
        $myRowAction->setRouteParameters(array('IdPersonalCargo'));
        $myRowAction->setColumn('action_column');
        $grid->addRowAction($myRowAction);
        
        $myRowAction = new RowAction('Bajas', 'plantel');
        $myRowAction->setRouteParameters(array('IdPersonalCargo'));
        $myRowAction->setColumn('action_column');
        $grid->addRowAction($myRowAction);
        
        return $grid->getGridResponse('LiquidacionesParteNovedadesBundle:Default:index.html.twig',
                array('grid'=>$grid)); 

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
