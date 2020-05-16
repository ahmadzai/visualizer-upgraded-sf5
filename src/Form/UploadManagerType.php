<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use App\Service\Importer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;


class UploadManagerType extends AbstractType
{
    protected $importer;

    function __construct(Importer $importer)
    {
        $this->importer = $importer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $tables = $this->importer->listAllTables(['upload_manager']);
        $builder->add('tableName', ChoiceType::class, array('label'=>'Table Name',
            'choices' => $tables,
            'placeholder' => 'Select a table to add',
            'attr' => array('class'=>'form-control select2'),))
            ->add('enabled', ChoiceType::class, [
                'label' => 'Enable upload? ',
                'attr' => ['class'=>'form-control'],
                'choices'=>array(
                'Yes' => 1,
                'No' => 0,
                )
            ])
            ->add('hasTemp', ChoiceType::class, [
                'label'=> 'Has Temp Table?',
                'attr' => ['class'=>'form-control'],
                'choices'=>array(
                'Yes' => 1,
                'No' => 0,
                )
            ]);

        $formModifier = function (FormInterface $form, $entity = null) {
            $columns = null === $entity ? array() : $this->importer->listColumns($entity);

            $form->add('excludedColumns', ChoiceType::class, array(
                'choices' => $columns,
                'placeholder' => 'Select Excluded Columns',
                'attr' => array('class'=>'form-control'),
                'multiple' => true
            ))
                ->add('entityColumns', ChoiceType::class, array(
                'choices' => $columns,
                'placeholder' => 'Select Foreign Key Columns',
                    'attr' => array('class'=>'form-control'),
                    'multiple' => true
            ))
                ->add("uniqueColumns", ChoiceType::class, array(
                    'label' => 'Columns to make a row unique',
                    'choices' => $columns,
                    'placeholder' => 'Select Columns for Unique Records',
                    'attr' => array('class'=>'form-control'),
                    'multiple' => true));
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity
                $data = $event->getData();

                $formModifier($event->getForm(), $data->getTableName());
            }
        );

        $builder->get('tableName')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                // It's important here to fetch $event->getForm()->getData(), as
                // $event->getData() will get you the client data (that is, the ID)
                $entity = $event->getForm()->getData();

                // since we've added the listener to the child, we'll have to pass on
                // the parent to the callback functions!
                $formModifier($event->getForm()->getParent(), $entity);
            }
        );
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\UploadManager'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'upload_manager';
    }


}
