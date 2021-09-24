<?php

namespace Liquidaciones\CuposAnualesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LiquidacionesTypeHS extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('refCupoTipoLiquidacion','text'
                 , array('label'  => 'Cupo'
                        ,'attr'   =>  array('class'   => 'form-control','disabled'   => 'disabled')
                        ))       
            ->add('idPersonalCargo','text'
                 , array('label'  => 'Id Persona'
                        ,'attr'   =>  array('class'   => 'form-control','disabled'   => 'disabled')
                        ))
            ->add('hsExValorHora','text'
                 , array('label'  => 'Valor Hora'
                        ,'attr'   =>  array('class'   => 'form-control','readonly'   => 'readonly')
                        ))
            ->add('hsExCantSimples','number'
                 , array('label'  => 'Hs Simples'
                        ,'attr'   =>  array('class'   => 'form-control','required' => false)
                        ))
            ->add('hsExCantDobles','number'
                 , array('label'  => 'Hs Dobles'
                        ,'attr'   =>  array('class'   => 'form-control','required' => false)
                        ))
            ->add('montoTotalCalculado','number'
                 , array('label'  => 'Monto Total'
                        ,'attr'   =>  array('class'   => 'form-control','readonly'   => 'readonly')
                        ))
            ->add('Guardar', 'submit'
                  , array('attr'   =>  array('class'   => 'btn btn-ba')
                        ))
            ->add('Eliminar', 'submit'
                  , array('attr'   =>  array('class'   => 'btn btn-danger')
                        ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Liquidaciones\CuposAnualesBundle\Entity\Liquidaciones'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'liquidaciones_cuposanualesbundle_liquidaciones';
    }
}
