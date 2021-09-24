<?php

namespace Liquidaciones\CuposAnualesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Liquidaciones\CuposAnualesBundle\Form\EventListener\AddFieldsCupoEstadosSubscriber;
use Liquidaciones\CuposAnualesBundle\Entity\CupoEstados;
use Liquidaciones\ReferenciasBundle\Entity\Dependencias;
use Liquidaciones\ReferenciasBundle\Entity\TipoDependencia;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;



class CuposType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    
    
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $meses = array(1=>1,2=>2,3=>3,4=>4,5=>5,6=>6,7=>7,8=>8,9=>9,10=>10,11=>11,12=>12);
        $anios = array((integer)date('Y')-1 => (integer)date('Y')-1,(integer)date('Y') => (integer)date('Y'), (integer)date('Y')+1 => (integer)date('Y')+1);

        $builder
            //->add('RefCupoAnual')

            ->add('Mes'
                 ,'choice'
                 , array('choices' => $meses,
                         'label'  => 'Mes',
                         'attr'   =>  array('class'   => 'form-control')
                        ))
            ->add('Anio'
                 ,'choice'
                 , array('choices' => $anios,
                         'label'  => 'Año',
                         'attr'   =>  array('class'   => 'form-control')
                        ))
            ->add('Monto'
                 ,'text'
                 , array('label'  => 'Monto'
                        ,'attr'   =>  array('class'   => 'form-control')
                        ))
            //->add('RefCupoAnual')
            /*->add('UsuaCrea')
            ->add('FechaCrea')
            ->add('UsuaModi')
            ->add('FechaModi')*/
            /*->add('idDependencia','choice',//array('class' => 'LiquidacionesReferenciasBundle:Dependencias',
                array('label' => 'Dependencias',
                /*'query_builder' => function(EntityRepository $er) {
                                               return $er->createQueryBuilder('Dependencias')->where("Dependencias.codigo is not null ")->orderBy("Dependencias.codigo");
                                             },*/
                /*'attr'   =>  array('class'   => 'form-control')
            ))*/
            //->add('RefCupoEstado')    
               
            /*->add('cuposanuales', 'entity',
                    array(
                        'class' => 'LiquidacionesCuposAnualesBundle:CuposAnuales',
                        'label' => 'Cupo Anual',
                        'attr'   =>  array('class'   => 'form-control textoGrande')
                    ))  */
            ->add('cuposanuales', 'entity',
                            array(
                                'class' => 'LiquidacionesCuposAnualesBundle:CuposAnuales',
                                'label' => 'Cupo Anual',
                                'empty_value' => '--Seleccione--',
                                'query_builder' => function(EntityRepository $er) {
                                               return $er->createQueryBuilder('CuposAnuales')->where("CuposAnuales.activo = '1' ")->orderBy('CuposAnuales.anio', 'DESC');
                                             },
                                'attr'   =>  array('class'   => 'form-control')
                            ))
            /*->add('CupoEstado', 'entity',
            array(
                'class' => 'LiquidacionesCuposAnualesBundle:CupoEstados',
                'label' => 'Estado',
                'attr'   =>  array('class'   => 'form-control')
            ))*/
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
                                'query_builder' => function(EntityRepository $er) {
                                               return $er->createQueryBuilder('CupoEstados')->where("CupoEstados.id <> 4 ");
                                                },
                                'attr'   =>  array('class'   => 'form-control')
                            ));
            }
        });
        
        //$builder->addEventListener(FormEvents::PRE_SET_DATA, array($this, 'preSetData'));
    }
    /*
    public function preSetData(FormEvent $event)
    {
        $cupos = $event->getData();
        $form = $event->getForm();
        
        $lista = $this->em->getRepository('')
    }
    */
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
