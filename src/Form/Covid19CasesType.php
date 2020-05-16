<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Covid19CasesType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('province', EntityType::class, array(
                'class'=>'App:Province',
                'choice_label'=>'provinceName',
                'attr' => array('class'=>'form-control select2'),
                'placeholder'=>'Province'))
            ->add('noOfConfirmedCases', IntegerType::class, array(
                'label' => 'Confirmed Cases',
                'attr' => array('class'=>'form-control'),
                'required' => true
            ))
            ->add('noOfSuspectedCases', IntegerType::class, array(
                'label' => 'Suspected Cases',
                'attr' => array('class'=>'form-control'),
                'required' => false
            ))
            ->add('noOfRecoveredCases', IntegerType::class, array(
                'label' => 'Recovered Cases',
                'attr' => array('class'=>'form-control'),
                'required' => false
            ))
            ->add('noOfDeaths', IntegerType::class, array(
                'label' => 'No of Deaths',
                'attr' => array('class'=>'form-control'),
                'required' => false
            ))
            ->add('lastUpdated', DateTimeType::class,
                array('label' => 'As of (Date/Time)',
                    'html5' => true,
                    'widget'=>'single_text',
                    'attr' => array('class'=>'form-control')
                ));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Covid19Cases'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_covid19cases';
    }


}
