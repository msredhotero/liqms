<?php

namespace Liquidaciones\CuposAnualesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LiquidacionesTypeM extends AbstractType
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
            ->add('rGFecha' ,'text'
                 , array('label'  => 'Fecha del Reemplazo'
                        ,'attr'   =>  array('class'   => 'form-control')
                        )) 
            ->add('montoTotalCalculado','number'
                 , array('label'  => 'Monto Total'
                        ,'attr'   =>  array('class'   => 'form-control')
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
