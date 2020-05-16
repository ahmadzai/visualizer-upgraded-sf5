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
use Sg\DatatablesBundle\Datatable\Filter\Select2Filter;

/**
 * Class CatchupDataDatatable
 *
 * @package App\Datatables
 */
class CatchupDataDatatable extends AbstractDatatable
{
    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {
        $type = $options['type'];

        $this->language->set(array(
            'cdn_language_by_locale' => true
            //'language' => 'de'
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
                            'title' => 'CatchupDataExport-'.date("Y-m-d")
                        )
                    ),
                    array(
                        'extend' => 'excel',
                        'text' => 'Export to Excel',
                        'title_attr' => 'To export all, first select All from the length menu in the left',
                        'button_options' => array(
                            'title' => 'CatchupDataExport-'.date("Y-m-d")
                        )
                    ),
                ),
            ),
        ));

        $this->ajax->set(array(
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
                            'route' => 'catchup_data_bulk_delete',
                            'icon' => 'glyphicon glyphicon-ok',
                            'label' => 'Delete Data',
                            'attributes' => array(
                                'rel' => 'tooltip',
                                'title' => 'Delete',
                                'class' => 'sg-datatables-catchupdata_datatable-multiselect-action btn btn-primary btn-xs',
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
            ))
            ->add('subDistrict', Column::class, array(
                'title' => 'SubDistrict',
                'visible'=>false,
                'filter' => array(TextFilter::class,
                    array(
                        'search_type' => 'like',
                        'cancel_button' => false,
                        'classes' => 'form-control input-sm'
                    ),
                ),
            ))
            ->add('campaign.campaignName', Column::class, array(
                'title' => 'Campaign',
                //'width' => '90%',
                'filter' => array(Select2Filter::class,
                    array(
                        'search_type' => 'eq',
                        'cancel_button' => true,
                        'url' => 'select2_campaign',
                        'classes' => 'form-control input-sm',
                        'placeholder'=>'Campaign'
                    ),
                ),
            ))
            ->add('clusterNo', Column::class, array(
                'title' => 'Cluster',
                'searchable' => false
                ))
            ->add('areaName', Column::class, array(
                'title' => 'AreaName',
                'visible' => false,
                'searchable' => false
                ));

            $this->columnBuilder
                ->add('dataSource', Column::class, array(
                    'title' => 'Source',
                    'searchable' => false,
                    'visible' => false
                ));

            $this->columnBuilder
                ->add('noSM', Column::class, array(
                    'title' => 'No.SMs',
                    'searchable' => false
                ))
                    ->add('noHH', Column::class, array(
                        'title' => 'No.HHs',
                        'searchable' => false
                    ))
                    ->add('noU5', Column::class, array(
                        'title' => 'No.U5',
                        'searchable' => false
                    ))
                    ->add('regAbsent', Column::class, array(
                        'title' => 'Reg Absent',
                        'searchable' => false
                    ))
                    ->add('vacAbsent', Column::class, array(
                        'title' => 'Vac Absent',
                        'searchable' => false
                    ))
                    ->add('regNSS', Column::class, array(
                        'title' => 'Reg NSS',
                        'searchable' => false
                    ))
                    ->add('vacNSS', Column::class, array(
                        'title' => 'Vac NSS',
                        'searchable' => false
                    ))
                    ->add('regRefusal', Column::class, array(
                        'title' => 'Reg Refusal',
                        'searchable' => false
                    ))
                    ->add('vacRefusal', Column::class, array(
                        'title' => 'Vac Refusal',
                        'searchable' => false
                    ))
                    ->add('unRecorded', Column::class, array(
                        'title' => 'Unrecorded',
                        'searchable' => false
                    ))
                    ->add('vacUnRecorded', Column::class, array(
                        'title' => 'Vac Unrecorded',
                        'searchable' => false
                    ))
                    ->add('vacGuest', Column::class, array(
                        'title' => 'Vac Guests',
                        'searchable' => false
                    ));


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
        return 'App\Entity\CatchupData';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'catchupdata_datatable';
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
