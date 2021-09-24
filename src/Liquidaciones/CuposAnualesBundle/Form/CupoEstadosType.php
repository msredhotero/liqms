<?php

namespace Liquidaciones\CuposAnualesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Description of CupoEstadosType
 *
 * @author saupureinm
 */
class CupoEstadosType extends AbstractType{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder->add('CupoEstado');
        
    }

    public function getName()
    {
        return 'CupoEstado';
    }
    
}
