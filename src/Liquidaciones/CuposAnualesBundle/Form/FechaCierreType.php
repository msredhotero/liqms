<?php

namespace Liquidaciones\CuposAnualesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FechaCierreType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('refCupo','text'
                 , array('label'  => 'Cupo'
                        ,'attr'   =>  array('class'   => 'form-control')
                        ))
            ->add('fechaDesde','date'
                 , array('label' => 'Fecha Desde',
                'attr'   =>  array('class'   => 'form-control'),     
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy', 
                'input' => 'datetime'
                     ))
            ->add('fechaHasta','date'
                 , array('label' => 'Fecha Hasta',
                'attr'   =>  array('class'   => 'form-control'),     
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'input' => 'datetime'
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
            'data_class' => 'Liquidaciones\CuposAnualesBundle\Entity\FechaCierre'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'liquidaciones_cuposanualesbundle_fechacierre';
    }
}
