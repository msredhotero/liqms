<?php

namespace Liquidaciones\CuposAnualesBundle\Form\EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Liquidaciones\CuposAnualesBundle\Entity\CupoEstados;
use Liquidaciones\CuposAnualesBundle\Entity\Cupos;
use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\ORM\EntityRepository;

/**
 * Description of AddFieldsCupoEstadosSubscriber
 *
 * @author msaupurein
 */
class AddFieldsCupoEstadosSubscriber implements EventSubscriberInterface {
    private $factory;
 
    public function __construct(FormFactoryInterface $factory)
    {
        $this->factory = $factory;
    }
    
    public static function getSubscribedEvents()
    {
        // Informa al despachador que deseas escuchar el evento
        // form.pre_set_data y se debe llamar al mÃ©todo 'preSetData'.
        return array(FormEvents::PRE_SET_DATA => 'preSetData');
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        // pregunto si estoy en el new
        if (!$data || !$data->getId()) {
             return;
        }//edit
        if ($data->getId()){
             $form->add($this->factory->createNamed('cupoestados', 
                     'entity',
                     $data->getCupoestado(),
                     array('class' => 'LiquidacionesCuposAnualesBundle:CupoEstados',
                         'data_class' => null,
                         'auto_initialize' => false,
                         'query_builder'=> function(EntityRepository $er) {
                                               return $er->createQueryBuilder('CupoEstados');
                                             })/*,
                     array('label' => 'Estado Farmacéutico')*/));
        }
    }
}
