<?php

namespace Liquidaciones\CuposAnualesBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LiquidacionesType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    protected $tipoguardia;

    public function __construct ($tipoguardia)
    {
        $this->tipoguardia = $tipoguardia;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        //$tipoguardia = array(515=>'12 Hs',514=>'24 Hs',517=>'12 Hs Feriado',516=>'24 Hs Feriado');
        $activo = array('0'=>'No','1'=>'Si');
        $builder
            ->add('refCupoTipoLiquidacion','text'
                 , array('label'  => 'Cupo'
                        ,'attr'   =>  array('class'   => 'form-control','disabled'   => 'disabled')
                        ))    
            ->add('idPersonalCargo','text'
                 , array('label'  => 'Id Persona'
                        ,'attr'   =>  array('class'   => 'form-control','disabled'   => 'disabled')
                        ))
            ->add('rGIdPersonalCargo','text'
                 , array('label'  => 'Id Persona Reempla.'
                        ,'attr'   =>  array('class'   => 'form-control','disabled'   => 'disabled')
                        ))
                
                /*
                 * 
                 * 'widget' => 'single_text', 
                                           'format' => 'yyyy-MM-dd',
                 */
            /*->add('rGFecha' ,'date'
                 , array('label'  => 'Fecha del Reemplazo'
                        ,'attr'   =>  array('class'   => 'form-control',
                        'widget' => 'single_text',
                        'input' => 'datetime',)
                     ))*/
                
            ->add('rGFecha' ,'date'
                 , array('label' => 'Fecha de la novedad',
                'attr'   =>  array('class'   => 'form-control'),     
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',  
                'input' => 'datetime'
                     ))
            ->add('rGCantHsGuardia','number'
                 , array('label'  => 'Cantidad de Guardias'
                        ,'attr'   =>  array('class'   => 'form-control', 'oninvalid'=>"setCustomValidity('Ingrese un valor.')")
                        ))
            ->add('idConcepto','choice'
                 , array('choices' => $this->tipoguardia,
                         'label'  => 'Tipo Guardia',
                         'attr'   =>  array('class'   => 'form-control')
                        ))
            ->add('montoTotalCalculado','number'
                 , array('label'  => 'Monto Total'
                        ,'attr'   =>  array('class'   => 'form-control','readonly'   => 'readonly')
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
            'data_class' => 'Liquidaciones\CuposAnualesBundle\Entity\Liquidaciones'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'liquidaciones_cuposanualesbundle_liquidaciones';
    }
}
