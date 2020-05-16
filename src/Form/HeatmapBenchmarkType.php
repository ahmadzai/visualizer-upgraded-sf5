<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HeatmapBenchmarkType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('minValue', NumberType::class, array('label' => 'Minimum', 'attr' => array('class'=>'form-control')))
            ->add('maxValue', NumberType::class, array('label' => 'Maximum', 'attr' => array('class'=>'form-control')))
            ->add('midStop', NumberType::class, array('label' => 'Mid Stop', 'attr' => array('class'=>'form-control')))
            ->add('minColor', TextType::class, array('label' => 'Min Color', 'attr' => array('class'=>'form-control color-picker colorpicker-element')))
            ->add('maxColor', TextType::class, array('label' => 'Max Color', 'attr' => array('class'=>'form-control color-picker colorpicker-element')))
            ->add('midColor', TextType::class, array('label' => 'Mid Color', 'attr' => array('class'=>'form-control color-picker colorpicker-element')))
            ->add('dataSource', TextType::class, array('label' => 'Data Source', 'attr' => array('class'=>'form-control')))
            ->add('indicator', TextType::class, array('label' => 'Indicator', 'attr' => array('class'=>'form-control')));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\HeatmapBenchmark'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_heatmapbenchmark';
    }


}
