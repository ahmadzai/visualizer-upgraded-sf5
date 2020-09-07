<?php

namespace App\Form;

use App\Entity\BphsHfIndicator;
use App\Entity\BphsIndicator;
use App\Entity\Province;
use App\Service\DropdownFilter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BphsHfIndicatorType extends AbstractType
{
    /**
     * @var DropdownFilter
     */
    private $filter;

    public function __construct(DropdownFilter $filter)
    {
        $this->filter = $filter;
    }

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
                'mapped' => false,
                'choice_value' => 'id',
                'choice_label' => 'districtName',
                'choices'=>null,
                'attr' => ['class' => 'form-control select2']
            ])
            ->add('bphsHealthFacility', ChoiceType::class, [
                'mapped' => true,
                'choices'=>null,
                'label'=>'Facility',
                'placeholder'=>'Select a district first',
                'attr' => ['class' => 'form-control select2']
            ])
            ->add('bphsIndicator', EntityType::class, [
                'class' => BphsIndicator::class,
                'label' => 'Indicator',
                'placeholder' => 'Choose indicator',
                'attr' => ['class' => 'form-control select2']
            ])
            ->add('annualTarget', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Annual Target'
            ])
            ->add('monthlyTarget', IntegerType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Monthly Target',
                'required' => false
            ])
            ->add('targetYear', ChoiceType::class, [
                'label' => 'Reporting Year',
                'required' => true,
                'placeholder' => 'Choose Reporting Year',
                'choices' => $this->yearDropdown(),
                'data' => date('Y'),
                'attr' => ['class' => 'form-control']
            ])
        ;
        // this is the pre-set data event,
        // called before setting the data to the form,
        // here we initialize the dropdowns and in case it was an edit request
        // we fill the dropdown with appropriate values.
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($builder) {
                $data = $event->getData();
                if(!$data)
                    return;
                $district = $data->getBphsHealthFacility() !==null ? $data->getBphsHealthFacility()->getDistrict() : null;
                $province = $district  !== null ? $district->getProvince() : null;
                $districtId = $district  !== null ? $district->getId() : '';
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
                $this->setHealthFacility(
                    $event->getForm(),
                    $districtId,
                    $builder
                );
            }
        );


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BphsHfIndicator::class,
        ]);
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
            'data' => $selectedProvince,
            'auto_initialize' => false,
            'attr' => ['class' => 'form-control select2']
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
                $this->setHealthFacility(
                    $form->getParent(),
                    '',
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
            'mapped'=>false,
            'choices'=>$districts,
            'choice_value' => 'id',
            'choice_label' => 'districtName',
            'auto_initialize' => false,
            'placeholder'=>'Choose a district',
            'data'=>$selectedDistrict,
            'attr' => ['class' => 'form-control select2']
        ]);
        $districtField->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($builder) {
                $form = $event->getForm();
                //dd($form->getViewData());
                $this->setHealthFacility(
                    $form->getParent(),
                    $form->getViewData(),
                    $builder
                );
            });
        $form->add($districtField->getForm());
    }

    /**
     * @param FormInterface $form
     * @param string|null $district
     * @param FormBuilderInterface $builder
     * This function initialize health facility dropdown list with health facilities of selected districts
     */
    private function setHealthFacility(FormInterface $form, ?string $district, FormBuilderInterface $builder)
    {
        $placeHolder = 'Select a district first';
        $facilities = null;
        if(strlen($district) > 0) {
            $placeHolder = 'Enter health facility first';
            $districtObj = $this->filter->getEntityObject('District', ['id'=>$district]);
            $facilities = $this->filter->filter(
                'BphsHealthFacility',
                ['district'=>$districtObj]);
            if(count($facilities) > 0)
                $placeHolder = 'Choose a health facility';
        }
        $field = $builder->create('bphsHealthFacility', ChoiceType::class, [
            'mapped'=>true,
            'label'=>'Facility',
            'choices'=>$facilities,
            'placeholder'=> $placeHolder,
            'choice_value'=>'id',
            'choice_label'=>'facilityName',
            'auto_initialize' => false,
            'attr' => ['class' => 'form-control select2']
        ]);
        $form->add($field->getForm());
    }

    private function yearDropdown()
    {
        $curYear = date('Y');
        return [
            //$curYear - 2 => $curYear - 2,
            $curYear - 1 => $curYear - 1,
            $curYear => $curYear,
            $curYear + 1 => $curYear + 1
        ];
    }
}
