<?php

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class DistrictType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('districtName', TextType::class, array('label' => 'Name', 'attr' => array('class'=>'form-control')))
                ->add('districtNameAlt', TextType::class, array(
                    'label' => 'Alt Name',
                    'attr' => array('class'=>'form-control'),
                    'required' => false
                ))
                ->add('districtNamePashtu', TextType::class, array(
                    'label' => 'Pashtu Name',
                    'attr' => array('class'=>'form-control'),
                    'required' => false
                ))
                ->add('districtNameDari', TextType::class, array(
                    'label' => 'Dari Name',
                    'attr' => array('class'=>'form-control'),
                    'required' => false,
                    ))
                ->add('districtLpdStatus', ChoiceType::class, array('label'=> 'LPD Status', 'choices' => array(
                    'LPD 1' => '1',
                    'LPD 2' => '2',
                    'LPD 3' => '3',
                    'None' => null
                ), 'attr' => array('class'=>'form-control select2'), 'required' => false))
                ->add('districtRiskStatus', ChoiceType::class, array('label'=> 'Risk Status', 'choices' => array(
                    'VHR' => 'VHR',
                    'HR' => 'HR',
                    'Focus' => 'Focus',
                    'None' => null
                    ),
                    'attr' => array('class'=>'form-control select2'),
                    'required' => false
                    ))
                ->add('districtIcnStatus', ChoiceType::class, array('label'=> 'ICN Status', 'choices' => array(
                    'Full time' => 'Fulltime',
                    'Campaign Based' => 'Campaign',
                    'Other Activity' => 'Other',
                    'None' => null
                    ),
                    'attr' => array('class'=>'form-control select2'),
                    'required' => false))
                ->add('province', EntityType::class, array(
                    'class'=>'App:Province',
                    'choice_label'=>'provinceName',
                    'attr' => array('class'=>'form-control select2'),
                    'placeholder'=>'Province')
                );
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\District'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_district';
    }


}
