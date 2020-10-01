<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fullName', null, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Full Name'
            ])

            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('jobTitle', TextType::class, [
                'label' => 'Job Title',
                'attr' => ['class' => 'form-control']
            ])
            ->add('country', null, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('city', null, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('mobileNo', null, [
                'attr' => ['class' => 'form-control']
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => User::ROLES,
                'multiple' => true,
                'attr' => ['class'=>'form-control select2'],
                'placeholder' => 'Assign Roles',
            ])
            ->add('hasApiAccess', ChoiceType::class, [
                'choices' => ['Yes'=>true, 'No'=>false],
                'attr' => ['class' => 'form-control'],
                'label' => 'API Access'
            ])
            ->add('enabled', ChoiceType::class, [
                'choices' => ['Yes'=>true, 'No'=>false],
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
