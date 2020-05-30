<?php

namespace App\Form;

use App\Entity\BphsIndicatorReach;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BphsIndicatorReachType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reach')
            ->add('reportMonth')
            ->add('reportYear')
            ->add('slug')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('hfIndicator')
            ->add('hfCode')
            ->add('indicator')
            ->add('createdBy')
            ->add('updatedBy')
            ->add('deletedBy')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BphsIndicatorReach::class,
        ]);
    }
}
