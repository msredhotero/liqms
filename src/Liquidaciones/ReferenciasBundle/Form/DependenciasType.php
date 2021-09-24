<?php

namespace Liquidaciones\ReferenciasBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DependenciasType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('codigo')
            ->add('nombre')
            ->add('estableId')
            ->add('codigoExte')
            ->add('entecaratulador')
            ->add('tipoDependencia')
            ->add('domicilio')
            ->add('telefono')
            ->add('paisId')
            ->add('provId')
            ->add('partidoId')
            ->add('localiId')
            ->add('tipoJurisdiccion')
            ->add('regionId')
            ->add('email')
            ->add('director')
            ->add('tipoEstable')
            ->add('habilitado')
            ->add('fechaModif')
            ->add('usuModif')
            ->add('codpost')
            ->add('auxTipoJurisdiccion')
            ->add('latitud')
            ->add('longitud')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Liquidaciones\ReferenciasBundle\Entity\Dependencias'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'liquidaciones_referenciasbundle_dependencias';
    }
}
