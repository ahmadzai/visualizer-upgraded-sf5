<?php

namespace App\Datatables;

use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Style;
use Sg\DatatablesBundle\Datatable\Column\Column;
use Sg\DatatablesBundle\Datatable\Column\BooleanColumn;
use Sg\DatatablesBundle\Datatable\Column\ActionColumn;
use Sg\DatatablesBundle\Datatable\Column\MultiselectColumn;
use Sg\DatatablesBundle\Datatable\Column\VirtualColumn;
use Sg\DatatablesBundle\Datatable\Column\DateTimeColumn;
use Sg\DatatablesBundle\Datatable\Column\ImageColumn;
use Sg\DatatablesBundle\Datatable\Filter\TextFilter;
use Sg\DatatablesBundle\Datatable\Filter\NumberFilter;
use Sg\DatatablesBundle\Datatable\Filter\SelectFilter;
use Sg\DatatablesBundle\Datatable\Filter\DateRangeFilter;
use Sg\DatatablesBundle\Datatable\Editable\CombodateEditable;
use Sg\DatatablesBundle\Datatable\Editable\SelectEditable;
use Sg\DatatablesBundle\Datatable\Editable\TextareaEditable;
use Sg\DatatablesBundle\Datatable\Editable\TextEditable;

/**
 * Class ImportedFilesDatatable
 *
 * @package App\Datatables
 */
class ImportedFilesDatatable extends AbstractDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {
        $this->language->set(array(
            'cdn_language_by_locale' => true
            //'language' => 'de'
        ));

        $this->ajax->set(array(
        ));

        $this->options->set(array(
            'classes' => 'table table-bordered table-striped dataTable no-footer',
            'stripe_classes' => [],
            'individual_filtering' => false,
            'page_length' => 25,
            'length_menu' => array(array(10, 25, 50, 100), array('10', '25', '50', '100')),
            'individual_filtering_position' => 'head',
            'order_cells_top' => true,
            //'global_search_type' => 'gt',
            'search_in_non_visible_columns' => true,
        ));

        $this->features->set(array(
            'paging' => true,
            'searching' => true,
            'auto_width' => null,
            'defer_render'  => true,
            'length_change' => true,
            'processing' => true,

        ));

        $this->columnBuilder
            ->add(
                null,
                MultiselectColumn::class,
                array(
                    'start_html' => '<div class="start_checkboxes">',
                    'end_html' => '</div>',
                    'add_if' => function () {
                        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
                    },
                    'value' => 'id',
                    'value_prefix' => true,
                    //'render_actions_to_id' => 'sidebar-multiselect-actions',
                    'actions' => array(
                        array(
                            'route' => 'imported_files_bulk_delete',
                            'icon' => 'glyphicon glyphicon-ok',
                            'label' => 'Delete Data',
                            'attributes' => array(
                                'rel' => 'tooltip',
                                'title' => 'Delete',
                                'class' => 'sg-datatables-importedfiles_datatable-multiselect-action btn btn-primary btn-xs',
                                'role' => 'button',
                            ),
                            'confirm' => true,
                            'confirm_message' => 'Do you really want to delete the file?',
                            'start_html' => '<div class="start_delete_action">',
                            'end_html' => '</div>',
                            'render_if' => function () {
                                return $this->authorizationChecker->isGranted('ROLE_ADMIN');
                            },
                        ),
                    ),
                )
            )
            ->add('id', Column::class, array(
                'title' => 'Id',
                ))
            ->add('fileName', Column::class, array(
                'title' => 'File Name',
                ))
            ->add('fileSize', Column::class, array(
                'title' => 'File Size',
                ))
            ->add('dataType', Column::class, array(
                'title' => 'Data Source',
                ))
            ->add('updatedAt', DateTimeColumn::class, array(
                'title' => 'Updated At',
                ))
            ->add(null, ActionColumn::class, array(
                'title' => $this->translator->trans('sg.datatables.actions.title'),
                'add_if' => function () {
                    return $this->authorizationChecker->isGranted('ROLE_EDITOR');
                },
                'actions' => array(
                    array(
                        'route' => 'uploaded_files_download',
                        'route_parameters' => array(
                            'id' => 'id'
                        ),
                        'label' => $this->translator->trans('sg.datatables.actions.show'),
                        'icon' => 'glyphicon glyphicon-eye-open',
                        'attributes' => array(
                            'rel' => 'tooltip',
                            'title' => $this->translator->trans('sg.datatables.actions.show'),
                            'class' => 'btn btn-primary btn-xs',
                            'role' => 'button'
                        ),
                    ),
                )
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'App\Entity\ImportedFiles';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'importedfiles_datatable';
    }
}
