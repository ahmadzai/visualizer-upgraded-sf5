<?php


namespace App\Form;


use App\Service\DropdownFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;

class CopyIndicatorToHfType extends AbstractType
{
    private $filter;

    public function __construct(DropdownFilter $filter)
    {
        $this->filter = $filter;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldYear', ChoiceType::class, [
                'label' => 'Year to copy from:',
                'choices' => $this->filter->bphsIndicatorYears(),
                'attr' => ['class' => 'form-control select2']
            ])
            ->add('newYear', IntegerType::class, [
                'data' => date('Y')+1,
                'attr' => ['class' => 'form-control']
            ]);
    }

}