<?php

namespace Liquidaciones\HaberesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HAImputacionPresupuestariaType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('idJurisdiccionPresupuestaria')
            ->add('dependenciaPresupuestaria')
            ->add('principalSubPrinc')
            ->add('regimenEstatutario')
            ->add('agrupamiento')
            ->add('descripcion')
            ->add('programaDescripcion')
            ->add('ace')
            ->add('aco')
            ->add('pan')
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
            ->add('principal')
            ->add('subPrincipal')
            ->add('parcial')
            ->add('subParcial')
            ->add('activa')
            ->add('cargos')
            ->add('credito')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Liquidaciones\HaberesBundle\Entity\HAImputacionPresupuestaria'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'liquidaciones_haberesbundle_haimputacionpresupuestaria';
    }
}
