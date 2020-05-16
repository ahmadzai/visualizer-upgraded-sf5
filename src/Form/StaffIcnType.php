<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StaffIcnType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('noOfDco',IntegerType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder'=>'In number'],
                'required' => false,
                'label' => 'Total DCOs'
            ))
            ->add('noOfFemaleDco',IntegerType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder'=>'In number'],
                'required' => false,
                'label' => 'Female DCOs'
            ))
            ->add('noOfCcs',IntegerType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder'=>'In number'],
                'required' => false,
                'label' => 'Total CCSs'
            ))
            ->add('noOfFemaleCcs',IntegerType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder'=>'In number'],
                'required' => false,
                'label' => 'Female CCSs'
            ))
            ->add('noOfSm',IntegerType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder'=>'In number'],
                'required' => false,
                'label' => 'Total SMs'
            ))
            ->add('noOfFemaleSm',IntegerType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder'=>'In number'],
                'required' => false,
                'label' => 'Female SMs'
            ))
            ->add('noOfFmv',IntegerType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder'=>'In number'],
                'required' => false,
                'label' => 'Total FMV'
            ))
            ->add('noOfExt',IntegerType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder'=>'In number'],
                'required' => false,
                'label' => 'Total Extenders'
            ))
            ->add('noOfFemaleExt',IntegerType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder'=>'In number'],
                'required' => false,
                'label' => 'Female Extenders'
            ))
            ->add('asOfMonth', ChoiceType::class, array('choices' => array(
                'January'=>'Jan',
                'February'=>'Feb',
                'March'=>'Mar',
                'April'=>'Apr',
                'May'=>'May',
                'June'=>'Jun', 'July'=>'Jul',
                'August'=>'Aug', 'September'=>'Sep',
                'October'=>'Oct', 'November'=>'Nov', 'December'=>'Dec'
            ),
                'attr' => array('class'=>'form-control select2'),
                'placeholder'=>'Select Month',
                'required' => false,
            ))
            ->add('asOfYear', IntegerType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder'=>'e.g. 2020'],
                'required' => false,
                'label' => 'As of Year'))
            ->add('district', EntityType::class, array(
                    'class'=>'App:District',
                    'choice_label'=>'districtName',
                    'attr' => array('class'=>'form-control select2'),
                    'placeholder'=>'District')
            );
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\StaffIcn'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_stafficn';
    }


}
