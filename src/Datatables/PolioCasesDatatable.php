<?php

namespace App\Datatables;

use Sg\DatatablesBundle\Datatable\AbstractDatatable;
use Sg\DatatablesBundle\Datatable\Filter\Select2Filter;
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
 * Class PolioCasesDatatable
 *
 * @package App\Datatables
 */
class PolioCasesDatatable extends AbstractDatatable
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

        $this->extensions->set(array(
            'responsive' => false,
            //'buttons' => true,
            'buttons' => array(
                'create_buttons' => array(
                    array(
                        'extend' => 'colvis',
                        'text' => 'Cols visibility',
                        'title_attr' => 'Enable or disable columns',
                    ),
                    array(
                        'extend' => 'csv',
                        'text' => 'Export to CSV',
                        'title_attr' => 'To export all, first select All from the length menu in the left',
                        'button_options' => array(
                            'title' => 'PolioCasesDataExport-'.date("Y-m-d")
                        )
                    ),
                    array(
                        'extend' => 'excel',
                        'text' => 'Export to Excel',
                        'title_attr' => 'To export all, first select All from the length menu in the left',
                        'button_options' => array(
                            'title' => 'PolioCasesDataExport-'.date("Y-m-d")
                        )
                    ),
                ),
            ),
        ));


        $this->options->set(array(
            'classes' => 'table table-bordered table-striped dataTable no-footer',
            'stripe_classes' => [],
            'individual_filtering' => true,
            'page_length' => 25,
            'length_menu' => array(array(10, 25, 50, 100, -1), array('10', '25', '50', '100', 'All')),
            'individual_filtering_position' => 'head',
            'order' => array(array($this->getDefaultOrderCol(), 'asc')),
            'order_cells_top' => true,
            //'global_search_type' => 'gt',
            'search_in_non_visible_columns' => true,
            'dom' => '<"row"
                        <"col-sm-4"l>
                        <"col-sm-4"B>
                        <"col-sm-4"f>r
                        >
                        t
                        <"row"
                        <"col-sm-5"i>
                        <"col-sm-7"p>
                        >',
        ));

        $this->features->set(array(
            'paging' => true,
            'searching' => true,
            'auto_width' => null,
            'defer_render'  => true,
            'length_change' => true,
            'processing' => true,
            'scroll_x' => true,

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
                            'route' => 'poliocases_bulk_delete',
                            'icon' => 'glyphicon glyphicon-ok',
                            'label' => 'Delete Data',
                            'attributes' => array(
                                'rel' => 'tooltip',
                                'title' => 'Delete',
                                'class' => 'sg-datatables-poliocases_datatable-multiselect-action btn btn-primary btn-xs',
                                'role' => 'button',
                            ),
                            'confirm' => true,
                            'confirm_message' => 'Do you really want to delete?',
                            'start_html' => '<div class="start_delete_action">',
                            'end_html' => '</div>',
                            'render_if' => function () {
                                return $this->authorizationChecker->isGranted('ROLE_ADMIN');
                            },
                        ),
                    ),
                )
            )
            ->add('district.province.provinceRegion', Column::class, array(
                'title' => 'Region',
                //'width' => '90%',
                'visible'=>true,
                'filter' => array(Select2Filter::class,
                    array(
                        'search_type' => 'eq',
                        'cancel_button' => true,
                        'url' => 'select2_region',
                        'classes' => 'form-control input-sm',
                        'placeholder' => 'Region'
                    ),
                ),
            ))
            ->add('district.province.provinceName', Column::class, array(
                'title' => 'Province',
                //'width' => '90%',
                'filter' => array(Select2Filter::class,
                    array(
                        'search_type' => 'eq',
                        'cancel_button' => true,
                        'url' => 'select2_province',
                        'classes' => 'form-control input-sm',
                        'placeholder' => 'Province'
                    ),
                ),
            ))
            ->add('district.districtName', Column::class, array(
                'title' => 'District',
                //'width' => '90%',
                'filter' => array(Select2Filter::class,
                    array(
                        'search_type' => 'eq',
                        'cancel_button' => true,
                        'url' => 'select2_district',
                        'placeholder' => 'District',
                        'classes' => 'form-control input-sm',
                    ),
                ),
            ));



        $this->columnBuilder
            ->add('epid', Column::class, array(
                'title' => 'EPID',
                'searchable' => false
            ))
            ->add('onsetDate', DateTimeColumn::class, array(
                'title' => 'Onset',
                'searchable' => false,
                'date_format' => 'DD/MM/YYYY'
            ))
            ->add('sex', Column::class, array(
                'title' => 'Sex',
                'searchable' => false,
                'filter' => array(SelectFilter::class,
                    array(
                        'search_type' => 'eq',
                        'cancel_button' => true,
                        'classes' => 'form-control input-sm',
                        'select_options' => array(
                            '' => 'Any',
                            'm' => 'Male',
                            'f' => 'Female'
                        )
                    ),
                ),

            ))
            ->add('ageInMonths', DateTimeColumn::class, array(
                'title' => 'Age (Months)',
                'searchable' => false,
                'date_format' => 'DD/MM/YYYY'
            ))
            ->add('noRoutineDoses', Column::class, array(
                'title' => 'No. Routine Doses',
                'searchable' => false
            ))
            ->add('noSiaDoses', Column::class, array(
                'title' => 'No. SIAs Doses',
                'searchable' => false
            ))
            ->add('lastOpvDate', DateTimeColumn::class, array(
                'title' => 'Last OPV Date',
                'searchable' => false,
                'date_format' => 'DD/MM/YYYY'
            ))
            ->add('stoolDate', DateTimeColumn::class, array(
                'title' => 'Stool Date',
                'searchable' => false,
                'date_format' => 'DD/MM/YYYY',
            ))
            ->add('cluster', Column::class, array(
                'title' => 'Cluster',
                'searchable' => false
            ))
            ->add('linkage', Column::class, array(
                'title' => 'Linkage',
                'searchable' => false
            ))
        ;


        /*
        ->add(null, ActionColumn::class, array(
            'title' => $this->translator->trans('sg.datatables.actions.title'),
            'actions' => array(
                array(
                    'route' => 'catchupdata_show',
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
                array(
                    'route' => 'catchupdata_edit',
                    'route_parameters' => array(
                        'id' => 'id'
                    ),
                    'label' => $this->translator->trans('sg.datatables.actions.edit'),
                    'icon' => 'glyphicon glyphicon-edit',
                    'attributes' => array(
                        'rel' => 'tooltip',
                        'title' => $this->translator->trans('sg.datatables.actions.edit'),
                        'class' => 'btn btn-primary btn-xs',
                        'role' => 'button'
                    ),
                )
            )
        ))
        */
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'App\Entity\PolioCases';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'poliocases_datatable';
    }

    /**
     * Get User.
     *
     * @return mixed|null
     */
    private function getUser()
    {
        if ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->securityToken->getToken()->getUser();
        } else {
            return null;
        }
    }

    /**
     * Is admin.
     *
     * @return bool
     */
    private function isAdmin()
    {
        return $this->authorizationChecker->isGranted('ROLE_ADMIN');
    }

    /**
     * Get default order col.
     *
     * @return int
     */
    private function getDefaultOrderCol()
    {
        return true === $this->isAdmin()? 1 : 0;
    }
}
