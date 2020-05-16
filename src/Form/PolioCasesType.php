<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PolioCasesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('epid')->add('onsetDate')->add('sex')->add('ageInMonths')->add('noRoutineDoses')->add('noSiaDoses')->add('lastOpvDate')->add('stoolDate')->add('cluster')->add('linkage')->add('year')
            ->add('district', EntityType::class, array(
                'class'=>'App:District',
                'choice_label'=>'districtName',
                'attr' => array('class'=>'form-control select2'),
                'required'=>false,
                'placeholder'=>'Select a District'))
            ->add('user');
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\PolioCases'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_poliocases';
    }


}
