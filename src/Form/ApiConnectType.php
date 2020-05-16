<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ApiConnectType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $builder
//            ->add('apiServiceName')
//            ->add('apiServiceUrl')
//            ->add('apiKey')
//            ->add('apiUser')
//            ->add('apiPass')
//            ->add('apiLoginType')
//            ->add('updatedAt')
//            ->add('user');
        $builder
            ->add('apiServiceName', TextType::class, array(
                'label' => 'Service Name',
                'attr' => array('class'=>'form-control'),
                'required' => true
            ))
            ->add('apiServiceUrl', UrlType::class, array(
                'label' => 'API Base URL',
                'attr' => array('class'=>'form-control'),
                'required' => true
            ))
            ->add('apiKey', TextType::class, array(
                'label' => 'API KEY',
                'attr' => array('class'=>'form-control'),
                'required' => false
            ))
            ->add('apiUser', TextType::class, array(
                'label' => 'Service User Name',
                'attr' => array('class'=>'form-control'),
                'required' => false
            ))
            ->add('apiPass', TextType::class, array(
                'label' => 'User Password',
                'attr' => array('class'=>'form-control'),
                'required' => false
            ))
            ->add('apiLoginType', ChoiceType::class,
                array('label'=> 'Login Type', 'choices' => array(
                    'With API Token' => 'Token',
                    'With User and Pass' => 'User'
                ),
                    'attr' => array('class'=>'form-control select2'),
                    'required' => false
                ));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\ApiConnect'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_apiconnect';
    }


}
