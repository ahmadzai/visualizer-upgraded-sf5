<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CampaignType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$builder->add('campaignSortNo')->add('campaignName')->add('campaignType')->add('campaignStartDate')->add('campaignEndDate')->add('entryDate')->add('campaignYear')->add('campaignMonth');

        $builder->add('campaignName', TextType::class, array('label' => 'Campaign Name', 'attr' => array('class'=>'form-control')))
            ->add('campaignType', ChoiceType::class, array('label'=> 'Campaign Type', 'choices' => array(
                'NID' => 'NID',
                'SNID' => 'SNID',
                'LPD' => 'LPD',
                'SR' => 'SR'
            ), 'attr' => array('class'=>'form-control select2'), 'placeholder'=>'Select Campaign Type'))
            ->add('campaignStartDate', TextType::class,
                array('label' => 'Campaign Start Date', 'attr' => array('class'=>'form-control datepicker')))
            ->add('campaignEndDate', TextType::class,
                array('label' => 'Campaign End Date', 'attr' => array('class'=>'form-control datepicker')))
            ->add('campaignMonth', TextType::class, array('label' => 'Campaign Month', 'attr' => array('class'=>'form-control'), 'required'=>false))
            ->add('campaignYear', TextType::class, array('label' => 'Campaign Year', 'attr' => array('class'=>'form-control'), 'required'=>false))
            ->add('campaignSortNo', NumberType::class, array('label' => 'Order No', 'attr' => array('class'=>'form-control'), 'required'=>false));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Campaign'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_campaign';
    }


}
