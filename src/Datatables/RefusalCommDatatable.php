<?php

namespace App\Datatables;

use Sg\DatatablesBundle\Datatable\AbstractDatatable;

use Sg\DatatablesBundle\Datatable\Column\Column;

use Sg\DatatablesBundle\Datatable\Column\MultiselectColumn;

use Sg\DatatablesBundle\Datatable\Filter\TextFilter;

use Sg\DatatablesBundle\Datatable\Filter\Select2Filter;

/**
 * Class CatchupDataDatatable
 *
 * @package App\Datatables
 */
class RefusalCommDatatable extends AbstractDatatable
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
                            'title' => 'RefusalCommitteesDataExport-'.date("Y-m-d")
                        )
                    ),
                    array(
                        'extend' => 'excel',
                        'text' => 'Export to Excel',
                        'title_attr' => 'To export all, first select All from the length menu in the left',
                        'button_options' => array(
                            'title' => 'RefusalCommitteesDataExport-'.date("Y-m-d")
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
                            'route' => 'refusal_committees_bulk_delete',
                            'icon' => 'glyphicon glyphicon-ok',
                            'label' => 'Delete Data',
                            'attributes' => array(
                                'rel' => 'tooltip',
                                'title' => 'Delete',
                                'class' => 'sg-datatables-refusal_comm_datatable-multiselect-action btn btn-primary btn-xs',
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
                'visible'=>true,
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
            ));


        $this->columnBuilder
            ->add('regRefusal', Column::class, array(
                'title' => 'Refusals at Day5',
                'searchable' => false
            ))
            ->add('refusalVacInCatchup', Column::class, array(
                'title' => 'Vac in Catchup',
                'searchable' => false
            ))
            ->add('refusalVacByCRC', Column::class, array(
                'title' => 'Vac By CRC',
                'searchable' => false
            ))
            ->add('refusalVacByRC', Column::class, array(
                'title' => 'Vac By RC',
                'searchable' => false
            ))
            ->add('refusalVacByCIP', Column::class, array(
                'title' => 'Vac By CIP',
                'searchable' => false
            ))
            ->add('refusalVacBySeniorStaff', Column::class, array(
                'title' => 'Vac By Seniors',
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
        return 'App\Entity\RefusalComm';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'refusal_comm_datatable';
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
