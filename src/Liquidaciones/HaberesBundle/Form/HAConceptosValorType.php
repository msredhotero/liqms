<?php

namespace Liquidaciones\HaberesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class HAConceptosValorType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('refConcepto')
            ->add('vigDesde')
            ->add('vigHasta')
            ->add('mesDeAplicacion')
            ->add('montoDesde')
            ->add('montoHasta')
            ->add('esDiscapacitado')
            ->add('idAgrupamiento')
            ->add('regHorario')
            ->add('categDesde')
            ->add('categHasta')
            ->add('antiguedadDesde')
            ->add('antiguedadHasta')
            ->add('idPlanta')
            ->add('monto')
            ->add('modulos')
            ->add('porcentaje')
            ->add('cantidad')
            ->add('montoMinimo')
            ->add('montoMaximo')
            ->add('idConceptoaux')
            ->add('condicion')
            ->add('formulaIsTrue')
            ->add('formulaIsFalse')
            ->add('perfil')
            ->add('idEncasillamiento')
            ->add('funcion')
            ->add('refGrupoDepeAplica')
            ->add('idAtributo')
            ->add('idRegimenEstatutario')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Liquidaciones\HaberesBundle\Entity\HAConceptosValor'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'liquidaciones_haberesbundle_haconceptosvalor';
    }
}
