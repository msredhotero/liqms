<?php

namespace Liquidaciones\CuposAnualesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Liquidaciones\CuposAnualesBundle\Entity\Cupos;
use Liquidaciones\CuposAnualesBundle\Entity\Cuentas;
use Liquidaciones\CuposAnualesBundle\Entity\CuposHATiposLiquidacion;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Event\DataEvent;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class VacantesType extends AbstractType
{
    protected $accion;
    protected $idvacante;

    public function __construct ($accion, $idvacante)
    {
        $this->accion = $accion;
        $this->idvacante = $idvacante;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $idvacante = $this->idvacante;

        $mes = date('m');
        if ($mes == 1) {
            $anio = date('Y') - 1;
            $mes = 12;
        } else {
            $anio = date('Y');
            $mes = date('m') - 1;
        }
        
        if ($this->accion == 'new') {
            $builder
            ->add('cupos', 'entity',
                array(
                    'class' => 'LiquidacionesCuposAnualesBundle:Cupos',
                    'label' => 'Cupo Mensual',
                    'query_builder' => function(EntityRepository $er) use ($anio, $mes) {
                                               return $er->createQueryBuilder('c')
                                                       ->innerjoin('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion', 'ca', 'with', 'c.id = ca.refCupo')
                                                       ->leftjoin('LiquidacionesCuposAnualesBundle:Vacantes', 'v', 'with', 'c.id = v.refCupo')
                                                       ->where("ca.refCuenta in (22,24)  and c.anio = :anio and c.mes = :mes and v.id is null")
                                                       ->orderBy('ca.refCuenta', 'ASC')
                                                       ->addOrderBy('c.idDependencia', 'ASC')
                                                       ->setParameters(array('anio'=> $anio, 'mes'=> $mes ));
                                             },
                    'attr'   =>  array('class'   => 'form-control')
                ))
            ->add('vacantes','text'
                 , array('label'  => 'Vacantes'
                        ,'attr'   =>  array('class'   => 'form-control')
                        ))
            ->add('Guardar', 'submit'
                  , array('attr'   =>  array('class'   => 'btn btn-ba')
                        ))
            ->add('Eliminar', 'submit'
                  , array('attr'   =>  array('class'   => 'btn btn-danger')
                        ))    
        ;
        } else {
            $builder
            ->add('cupos', 'entity',
                array(
                    'class' => 'LiquidacionesCuposAnualesBundle:Cupos',
                    'label' => 'Cupo Mensual',
                    'query_builder' => function(EntityRepository $er) use ($idvacante) {
                                               return $er->createQueryBuilder('c')
                                                       ->innerjoin('LiquidacionesCuposAnualesBundle:CuposHATiposLiquidacion', 'ca', 'with', 'c.id = ca.refCupo')
                                                       ->innerjoin('LiquidacionesCuposAnualesBundle:Vacantes', 'v', 'with', 'c.id = v.refCupo')
                                                       ->where("ca.refCuenta in (22,24)  and v.id = ".$idvacante);
                                             },
                    'attr'   =>  array('class'   => 'form-control')
                ))
            ->add('vacantes','text'
                 , array('label'  => 'Vacantes'
                        ,'attr'   =>  array('class'   => 'form-control')
                        ))
            ->add('Guardar', 'submit'
                  , array('attr'   =>  array('class'   => 'btn btn-ba')
                        ))
            ->add('Eliminar', 'submit'
                  , array('attr'   =>  array('class'   => 'btn btn-danger')
                        ))    
        ;
        }
        
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Liquidaciones\CuposAnualesBundle\Entity\Vacantes'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'liquidaciones_cuposanualesbundle_vacantes';
    }
}
