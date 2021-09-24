<?php

namespace Liquidaciones\CuposAnualesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Liquidaciones\CuposAnualesBundle\Form\EventListener\AddFieldsCupoEstadosSubscriber;
use Liquidaciones\CuposAnualesBundle\Entity\CupoEstados;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;



class CuposMontoEstadoType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    
    
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {



        $builder
            ->add('Monto'
                 ,'text'
                 , array('label'  => 'Monto'
                        ,'attr'   =>  array('class'   => 'form-control')
                        ))
            ->add('Guardar', 'submit'
                  , array('attr'   =>  array('class'   => 'btn btn-ba')
                        ))
            ->add('Eliminar', 'submit'
                  , array('attr'   =>  array('class'   => 'btn btn-danger')
                        ))
             
        ;
        //$suscribe = new AddFieldsCupoEstadosSubscriber($builder->getFormFactory());
        //$builder->addEventSubscriber($suscribe);
        
        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
            $client=$event->getData();
            $form=$event->getForm();
            if($client && !$client->getId()){
                $form->add('CupoEstado', 'entity',
                            array(
                                'class' => 'LiquidacionesCuposAnualesBundle:CupoEstados',
                                'label' => 'Estado',
                                'query_builder' => function(EntityRepository $er) {
                                               return $er->createQueryBuilder('CupoEstados')->where("CupoEstados.id = 1 ");
                                             },
                                'attr'   =>  array('class'   => 'form-control')
                            ));
            } else {
                $form->add('CupoEstado', 'entity',
                            array(
                                'class' => 'LiquidacionesCuposAnualesBundle:CupoEstados',
                                'label' => 'Estado',
                                'attr'   =>  array('class'   => 'form-control')
                            ));
            }
        });
        
        //$builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'preSetData'));
    }
    
  
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Liquidaciones\CuposAnualesBundle\Entity\Cupos'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'liquidaciones_cuposanualesbundle_cupos';
    }
}
