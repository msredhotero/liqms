<?php

namespace Liquidaciones\CuposAnualesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CuentasType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('conceptoMS')
            ->add('idTipoLiquidacion')
            ->add('cuenta','text'
                 , array('label'  => 'Cuenta'
                        ,'attr'   =>  array('class'   => 'form-control')
                        ))
            ->add('activo')
            ->add('modoCarga')
            ->add('save', 'submit')    
            //->add('cuposhatiposliquidacion')
            //->add('cuentaconceptos')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Liquidaciones\CuposAnualesBundle\Entity\Cuentas'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'liquidaciones_cuposanualesbundle_cuentas';
    }
}
