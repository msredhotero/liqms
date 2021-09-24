<?php

namespace Liquidaciones\ReferenciasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ImputacionpresupuestariaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('jurisdiccion')
            ->add('descEntidad')
            ->add('codJurisdiccion')
            ->add('regimenestatutario')
            ->add('agrupamiento')
            ->add('programaDescripcion')
            ->add('aCE')
            ->add('aCO')
            ->add('pAN')
            ->add('programa')
            ->add('subPrograma')
            ->add('proyecto')
            ->add('grupo')
            ->add('aES')
            ->add('subGrupo')
            ->add('obra')
            ->add('ubicacionGeografica')
            ->add('finalidad')
            ->add('funcion')
            ->add('subFuncion')
            ->add('procedencia')
            ->add('fuente')
            ->add('partidaPpal')
            ->add('partidaSubppal')
            ->add('partidaParcial')
            ->add('partidaSubparcial')
            ->add('entidad')
            ->add('origenFondos')
            ->add('codInstitucional')
            ->add('descImputacionpresup')
            ->add('multidependencia')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Liquidaciones\ReferenciasBundle\Entity\Imputacionpresupuestaria'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'liquidaciones_referenciasbundle_imputacionpresupuestaria';
    }
}
