<?php

namespace App\Form;

use App\Entity\BphsHfIndicator;
use App\Entity\BphsIndicatorReach;
use App\Entity\Province;
use App\Service\DropdownFilter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class BphsIndicatorReachType
 * @package App\Form
 * This form type could be improved, I used some hacked ways (sessions) to make it work
 */
class BphsIndicatorReachType extends AbstractType
{
    /**
     * @var DropdownFilter
     */
    private $filter;

    private $session;

    public function __construct(DropdownFilter $filter, SessionInterface $session)
    {
        $this->filter = $filter;
        $this->session = $session;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reportYear', ChoiceType::class, [
                'choices' => $this->filter->bphsIndicatorYears(),
                'placeholder' => 'Select year',
                'attr' => ['class' => 'form-control']
            ])
            ->add('reportMonth', ChoiceType::class, [
                'choices' => $this->filter->monthsArray(),
                'placeholder' => 'Select month',
                'attr' => ['class' => 'form-control select2']
            ])
            ->add('province', ChoiceType::class, [
                'mapped' => false,
                'choice_label' => 'provinceName',
                'choice_value' => 'id',
                'placeholder' => 'Select a province',
                'attr' => ['class' => 'form-control select2']
            ])
            ->add('district', ChoiceType::class, [
                'mapped' => false,
                'choice_value' => 'id',
                'choice_label' => 'districtName',
                'choices'=>null,
                'attr' => ['class' => 'form-control select2']
            ])
            ->add('hfCode', ChoiceType::class, [
                'label' => 'Health Facility',
                'mapped' => true,
                'choices'=>null,
                'placeholder'=>'Select a district first',
                'attr' => ['class' => 'form-control select2']
            ])
            ->add('hfIndicator', EntityType::class, [
                'choices' => $this->filter->hfIndicators() ?? [],
                'placeholder' => 'Select facility first',
                'choice_value'=>'id',
                'choice_label'=>'indicator',
                'class' => BphsHfIndicator::class,
                'attr' => ['class' => 'form-control select2']
            ])
            ->add('reach', IntegerType::class, [
                'label' => 'Monthly Reach',
                'attr' => ['class' => 'form-control']
            ])
            ->add('facilityYear', HiddenType::class, [
                'mapped' => false,
                'data' => 'undefined'
            ])
        ;

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($builder) {
                $data = $event->getData();
                if(!$data)
                    return;
                $facility = $data->getHfCode();
                $district = $facility !==null ? $facility->getDistrict() : null;
                $province = $district  !== null ? $district->getProvince() : null;
                $districtId = $district  !== null ? $district->getId() : '';
                $facilityAndReportYear = $facility !== null ? $facility->getId().'-'.$data->getReportYear() : null;
                //dd($facility);
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

                $this->setIndicator(
                    $event->getForm(),
                    $facilityAndReportYear,
                    $builder
                );
            }
        );


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BphsIndicatorReach::class,
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
            'placeholder' => 'Select a province',
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
                $this->setIndicator(
                    $form->getParent(),
                    null,
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
            'placeholder'=> $districts ? 'Select district' : 'Select province first',
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
                $this->setIndicator(
                    $form->getParent(),
                    null,
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
        $placeHolder = 'Select district first';
        $facilities = null;
        if(strlen($district) > 0) {
            $placeHolder = 'Enter facility first';
            $districtObj = $this->filter->getEntityObject('District', ['id'=>$district]);
            $facilities = $this->filter->filter(
                'BphsHealthFacility',
                ['district'=>$districtObj]);
            if(count($facilities) > 0)
                $placeHolder = 'Select health facility';
        }
        $field = $builder->create('hfCode', ChoiceType::class, [
            'label'=>'Health Facility',
            'choices'=>$facilities,
            'placeholder'=> $placeHolder,
            'choice_value'=>'id',
            'choice_label'=>'facilityName',
            'auto_initialize' => false,
            'attr' => ['class' => 'form-control select2']
        ]);

        $field->addEventListener(
        FormEvents::POST_SUBMIT,
        function (FormEvent $event) use ($builder) {
            $form = $event->getForm();
            $this->setIndicator(
                $form->getParent(),
                $form->getViewData(),
                $builder
            );
        });

        $form->add($field->getForm());
    }

    private function setIndicator(FormInterface $form, $facilityAndReportYear, FormBuilderInterface $builder)
    {

        if(strpos($facilityAndReportYear, "-") > 0) {
            $params = explode("-", $facilityAndReportYear);
            $this->session->set('facilityYear', $params);
            $placeHolder = 'Set the indicators first';

            $indicators = $this->filter->hfIndicators();

            if(count($indicators) > 0)
                $placeHolder = 'Select an indicator';

            $field = $builder->create('hfIndicator', EntityType::class, [
                'choices'=>$indicators,
                'placeholder'=> $placeHolder,
                'choice_value'=>'id',
                'choice_label'=>'indicator',
                'auto_initialize' => false,
                'empty_data' => null,
                'class' => BphsHfIndicator::class,
                'attr' => ['class' => 'form-control select2']
            ]);

            $form->add($field->getForm());
        }
    }

}
