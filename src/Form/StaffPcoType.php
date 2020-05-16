<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StaffPcoType extends AbstractType
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
                    'placeholder'=>'Province')
            )
            ->add('noOfPco', IntegerType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder'=>'In number'],
                'required' => false,
                'label' => 'No. of PCOs',
            ))
            ->add('noOfFemalePco',IntegerType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder'=>'In number'],
                'required' => false,
                'label' => 'No. of Female PCOs'
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
                'placeholder'=>'By which month (please select)',
                'required' => false,
            ))
            ->add('asOfYear', IntegerType::class, array(
                'attr' => ['class' => 'form-control', 'placeholder'=>'e.g. 2020'],
                'required' => false,
                'label' => 'As of Year'));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\StaffPco'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_staffpco';
    }


}
