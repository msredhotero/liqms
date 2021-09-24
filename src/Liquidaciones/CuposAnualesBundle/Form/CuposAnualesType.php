<?php

namespace Liquidaciones\CuposAnualesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class CuposAnualesType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*$builder
            ->add('descripcion')
            ->add('anio')
            ->add('monto')
            ->add('activo')
            ->add('usuaCrea')
            ->add('fechaCrea')
            ->add('usuaModi')
            ->add('fechaModi')
        ;*/
        $anios = array((integer)date('Y')-1 => (integer)date('Y')-1,(integer)date('Y') => (integer)date('Y'), (integer)date('Y')+1 => (integer)date('Y')+1);
        $activo = array('1'=>'Activo','0'=>'Inactivo');
        $builder
            ->add('Descripcion'
                 ,'text'
                 , array('label'  => 'Descripción',
                         'attr'   =>  array('class'   => 'form-control')
                        )
                 )
            ->add('Anio'
                 ,'choice'
                 , array('choices' => $anios
                 ,'label' => 'Año'
                 ,'attr'   =>  array('class'   => 'form-control')
                        )
                 )
            ->add('Monto'
                 ,'text'
                 , array('label'  => 'Monto'
                        ,'attr'   =>  array('class'   => 'form-control')
                        )
                 )
            ->add('Activo','choice'
                 , array('choices' => $activo
                 ,'label' => 'Estado'
                 ,'attr'   =>  array('class'   => 'form-control')
                        ))

            ->add('Guardar', 'submit'
                  , array('attr'   =>  array('class'   => 'btn btn-ba')
                        ))
            ->add('Eliminar', 'submit'
                  , array('attr'   =>  array('class'   => 'btn btn-danger')
                        ))
        ;
        //$builder->add('Activo', 'checkbox', array('mapped' => false));
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Liquidaciones\CuposAnualesBundle\Entity\CuposAnuales'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'liquidaciones_cuposanualesbundle_cuposanuales';
    }
}
