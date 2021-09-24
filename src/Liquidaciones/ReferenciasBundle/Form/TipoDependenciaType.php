<?php

namespace Liquidaciones\ReferenciasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TipoDependenciaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codTipoDependencia')
            ->add('descTipoDependencia')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Liquidaciones\ReferenciasBundle\Entity\TipoDependencia'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'liquidaciones_referenciasbundle_tipodependencia';
    }
}
