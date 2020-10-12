<?php

namespace App\Form;

use App\Entity\Province;
use App\Service\DropdownFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BphsHealthFacilityType extends AbstractType
{
    /**
     * @var DropdownFilter
     */
    private $filter;

    public function __construct(DropdownFilter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('province', ChoiceType::class, [
                'mapped' => false,
                'choice_label' => 'provinceName',
                'choice_value' => 'id',
                'placeholder' => 'Choose a province',
                'attr' => ['class' => 'form-control select2']
            ])
            ->add('district', ChoiceType::class, [
                'choice_value' => 'id',
                'choice_label' => 'districtName',
                'choices'=>null,
                'attr' => ['class' => 'form-control select2']
            ])
            ->add('facilityName', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Facility Name'
            ])
            ->add('id', NumberType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Facility Code'
            ])
            ->add('facilityType', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Facility Type'
            ])
            ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($builder) {
                $data = $event->getData();
                if(!$data)
                    return;
                $district = $data->getDistrict() !== null ? $data->getDistrict() : null;
                $province = $district !== null ? $district->getProvince() : null;
                //dd($data);
                $this->setProvinces(
                    $event->getForm(),
                    $builder,
                    $province
                );
                $this->setDistricts(
                    $event->getForm(),
                    $province,
                    $builder,
                    $district
                );
            }
        );
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\BphsHealthFacility'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'bphshealthfacility';
    }

    /**
     * @param FormInterface $form
     * @param FormBuilderInterface $builder
     * @param null $selectedProvince
     * This function populates the provinces dropdown, in case it was an edit form, it will set proper selected option
     * It also attaching an event (post-submit) to itself, which can be called to do some actions (e.g.) populating its
     * depending dropdown.
     */
    private function setProvinces(FormInterface $form, FormBuilderInterface $builder, $selectedProvince=null) {
        $provinces = $this->filter->filter('Province');
        $provinceField = $builder->create('province', ChoiceType::class, [
            'mapped' => false,
            'choices' => $provinces,
            'choice_value' => 'id',
            'choice_label' => 'provinceName',
            'placeholder' => 'Choose a province',
            'attr' => ['class' => 'form-control select2'],
            'data' => $selectedProvince,
            'auto_initialize' => false
        ]);

        $provinceField->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($builder) {
                $form = $event->getForm();
                $this->setDistricts(
                    $form->getParent(),
                    $form->getData(),
                    $builder
                );
            }
        );

        $form->add($provinceField->getForm());
    }

    /**
     * @param FormInterface $form
     * @param Province|null $province
     * @param FormBuilderInterface $builder
     * @param null $selectedDistrict
     * This function initialize districts dropdown with selected province districts
     * It also attached post-submit event to the district field, so it can be triggered
     * and the districts depended list can be populated
     */
    private function setDistricts(FormInterface $form, ?Province $province,
                                  FormBuilderInterface $builder, $selectedDistrict = null)
    {
        $districts = $this->filter->filter('District', ['province'=>$province]);
        $districtField = $builder->create('district', ChoiceType::class, [
            'choices'=>$districts,
            'choice_value' => 'id',
            'choice_label' => 'districtName',
            'auto_initialize' => false,
            'placeholder'=>'Choose a district',
            'data'=>$selectedDistrict,
            'attr' => ['class' => 'form-control select2']
        ]);

        $form->add($districtField->getForm());
    }


}
