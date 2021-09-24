<?php

namespace MinSaludBA\ParteNovedadesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;
use MinSaludBA\ParteNovedadesBundle\Entity\Plantel;

class PersonalNovedadType extends AbstractType
{
    protected $plantel;
    
    public function __construct (Plantel $plantel)
    {
        $this->plantel = $plantel;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $plantel = $this->plantel;
        
        $builder->add('Novedad', 'entity',
            array(
                'label' => 'Novedad',
                'class' => 'LiquidacionesParteNovedadesBundle:Novedad',
                'query_builder' => function(EntityRepository $er) use ($plantel) {
                    return $er->createQueryBuilder('n')
                            ->select('n')
                            ->innerJoin('LiquidacionesParteNovedadesBundle:NovedadRegimen', 'r', 'WITH', 'r.novedad = n.id')
                            ->where("n.tipoDeNovedad = :tipo")
                            ->andWhere("n.idEstado = 1")
                            ->andWhere("r.idRegimen = :regimen")
                            ->andWhere("r.idAgrupamiento = :agrupamiento or r.idAgrupamiento is null")
                            ->setParameter('tipo', 'N', 'string')  
                            ->setParameter('regimen', $plantel->getIdRegimenEstatutario(), 'string')  
                            ->setParameter('agrupamiento', $plantel->getIdAgrupamiento(), 'string')  
                            ->orderBy('n.codigo', 'ASC');
                },
                'empty_value' => 'Seleccione...',
        ));
        
//        $builder
//            ->add('FechaDesde', 'date', array(
//            'label' => 'Vigencia de la novedad',
//            'widget' => 'single_text',
//            'format' => 'dd/MM/yyyy',
//            'input' => 'datetime',
//            )) 
//            ->add('FechaHasta', 'date', array(
//                'widget' => 'single_text',
//                'format' => 'dd/MM/yyyy',
//                'input' => 'datetime',
//            ))    
//            ->add('CantidadMinutosPorDia', 'integer', array(
//                'label' => 'Cantidad de minutos por día',
//            ))
//            ->add('Observaciones')

//            ;

    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MinSaludBA\ParteNovedadesBundle\Entity\PersonalNovedad'
        ));
    }

    public function getName()
    {
        return 'minsaludba_partenovedadesbundle_personalnovedadtype';
    }
}
