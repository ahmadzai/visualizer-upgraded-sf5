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
 * Class CoverageDataDatatable
 *
 * @package App\Datatables
 */
class CoverageDataDatatable extends AbstractDatatable
{

    /**
     * {@inheritdoc}
     */
    public function buildDatatable(array $options = array())
    {
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
                            'title' => 'CoverageDataExport-'.date("Y-m-d")
                        )
                    ),
                    array(
                        'extend' => 'excel',
                        'text' => 'Export to Excel',
                        'title_attr' => 'To export all, first select All from the length menu in the left',
                        'button_options' => array(
                            'title' => 'CoverageDataExport-'.date("Y-m-d")
                        )
                    ),
                ),
            ),

//            'responsive' => array(
//                'details' => array(
//                    'display' => array(
//                        'template' => ':dtExtension:display.js.twig',
//                    ),
//                    'renderer' => array(
//                        'template' => ':dtExtension:renderer.js.twig',
//                    ),
//                ),
//            ),
        ));

        $this->language->set(array(
            'cdn_language_by_locale' => true
            //'language' => 'de'
        ));

        $this->ajax->set(array(
            'method' => 'POST'
        ));

//        $this->extensions->set(array(
//            'responsive' => true,
//        ));

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
                            'route' => 'coverage_data_bulk_delete',
                            'icon' => 'glyphicon glyphicon-ok',
                            'label' => 'Delete Data',
                            'attributes' => array(
                                'rel' => 'tooltip',
                                'title' => 'Delete',
                                'class' => 'sg-datatables-coveragedata_datatable-multiselect-action btn btn-primary btn-xs',
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
                        'search_type' => 'like',
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
                        'search_type' => 'like',
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
                        'search_type' => 'like',
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
                        'search_type' => 'like',
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
            ->add('noTeams', Column::class, array(
                'title' => 'No. Teams',
                'searchable' => false
            ))
            ->add('noHouses', Column::class, array(
                'title' => 'No. Houses',
                'searchable' => false
            ))
            ->add('targetChildren', Column::class, array(
                'title' => 'Target',
                'searchable' => false
            ))
            ->add('vialsReceived', Column::class, array(
                'title' => 'Received Vials',
                'visible' => false,
                'searchable' => false,
            ))
            ->add('vialsUsed', Column::class, array(
                'title' => 'Used Vials',
                'visible' => false,
                'searchable' => false,
            ))
//            ->add('vacWastage', Column::class, array(
//                'title' => 'Wastage',
//                'visible' => false,
//                'searchable' => false,
//            ))
            ->add('noHousesVisited', Column::class, array(
                'title' => 'HH Visited',
                'visible' => false,
                'searchable' => false,
            ))
            ->add('noResidentChildren', Column::class, array(
                'title' => 'Resident Child',
                'visible' => false,
                'searchable' => false,
            ))
            ->add('noGuestChildren', Column::class, array(
                'title' => 'Guest Child',
                'visible' => false,
                'searchable' => false,
            ))
            ->add('noChildInHouseVac', Column::class, array(
                'title' => 'Inhouse Vac',
                'visible' => false,
                'searchable' => false,
            ))
            ->add('noChildOutsideVac', Column::class, array(
                'title' => 'Outside Vac',
                'visible' => false,
                'searchable' => false,
            ))
            ->add('noAbsentSameDay', Column::class, array(
                'title' => 'Sameday Absent',
                'visible' => false,
                'searchable' => false,
            ))
            ->add('noAbsentSameDayFoundVac', Column::class, array(
                'title' => 'Sameday Found Vac',
                'visible' => false,
                'searchable' => false,
            ))
            ->add('noAbsentSameDayVacByTeam', Column::class, array(
                'title' => 'Sameday Vac Team',
                'visible' => false,
                'searchable' => false,
            ))
            ->add('AbsentSD', Column::class, array(
                'title' => 'Absent SD',
                'dql' => '(COALESCE(coveragedata.noAbsentSameDay,0) - 
                           COALESCE(coveragedata.noAbsentSameDayFoundVac,0) - 
                           COALESCE(coveragedata.noAbsentSameDayVacByTeam,0))',
                'searchable' => false,
                'orderable' => true,
            ))
            ->add('noAbsentNotSameDay', Column::class, array(
                'title' => 'Not Sameday Absent',
                'visible' => false,
                'searchable' => false,
            ))
            ->add('noAbsentNotSameDayFoundVac', Column::class, array(
                'title' => 'Not Sameday Found Vac',
                'visible' => false,
                'searchable' => false,
            ))
            ->add('noAbsentNotSameDayVacByTeam', Column::class, array(
                'title' => 'Not Sameday Vac Team',
                'visible' => false,
                'searchable' => false,
            ))
            ->add('AbsentNSD', Column::class, array(
                'title' => 'Absent NSD',
                'dql' => '(COALESCE(coveragedata.noAbsentNotSameDay,0) - 
                           COALESCE(coveragedata.noAbsentNotSameDayFoundVac,0) - 
                           COALESCE(coveragedata.noAbsentNotSameDayVacByTeam,0))',
                'searchable' => false,
                'orderable' => true,
            ))
            ->add('noNSS', Column::class, array(
                'title' => 'No. NSS',
                'visible' => false,
                'searchable' => false,
            ))
            ->add('noNSSFoundVac', Column::class, array(
                'title' => 'NSS Found Vac',
                'visible' => false,
                'searchable' => false,
            ))
            ->add('noNSSVacByTeam', Column::class, array(
                'title' => 'NSS Vac Team',
                'visible' => false,
                'searchable' => false,
            ))
            ->add('RemNSS', Column::class, array(
                'title' => 'Rem NSS',
                'dql' => '(COALESCE(coveragedata.noNSS,0) - 
                           COALESCE(coveragedata.noNSSFoundVac,0) - 
                           COALESCE(coveragedata.noNSSVacByTeam,0) )',
                'searchable' => false,
                'orderable' => true,
            ))
            ->add('noRefusal', Column::class, array(
                'title' => 'No. Refusal',
                'visible' => false,
                'searchable' => false,
            ))
            ->add('noRefusalFoundVac', Column::class, array(
                'title' => 'Refusal Found Vac',
                'visible' => false,
                'searchable' => false,
            ))
            ->add('noRefusalVacByTeam', Column::class, array(
                'title' => 'Refusal Vac Team',
                'visible' => false,
                'searchable' => false,
            ))
            ->add('Refusal', Column::class, array(
                'title' => 'Rem Refusal',
                'dql' => '(COALESCE(coveragedata.noRefusal,0) - 
                           COALESCE(coveragedata.noRefusalFoundVac,0) - 
                           COALESCE(coveragedata.noRefusalVacByTeam,0))',
                'searchable' => false,
                'orderable' => true,
            ))
            ->add('TotalVac', Column::class, array(
                'title' => 'Total Vac',
                'dql' => '(COALESCE(coveragedata.noRefusalFoundVac,0) + 
                           COALESCE(coveragedata.noRefusalVacByTeam,0) +
                           COALESCE(coveragedata.noNSSFoundVac,0) + 
                           COALESCE(coveragedata.noNSSVacByTeam,0) +
                           COALESCE(coveragedata.noAbsentNotSameDayVacByTeam,0) +
                           COALESCE(coveragedata.noAbsentNotSameDayFoundVac,0) +
                           COALESCE(coveragedata.noAbsentSameDayVacByTeam,0) +
                           COALESCE(coveragedata.noAbsentSameDayFoundVac,0) +
                           COALESCE(coveragedata.noChildInHouseVac,0) + 
                           COALESCE(coveragedata.noChildOutsideVac,0))',
                'searchable' => false,
                'orderable' => true,
            ))
            ->add('afpCase', Column::class, array(
                'title' => 'AFP Case',
                'searchable' => false,
                'visible' => false
            ))
            ->add('vacDay', Column::class, array(
                'title' => 'Day',
                'searchable' => false,
            ))
            ->add('id', Column::class, array(
                'title' => 'Id',
                'visible' => false,
            ))




//            ->add('campaign.campaignType', Column::class, array(
//                'title' => 'Campaign CampaignType',
//                ))
//            ->add('campaign.campaignStartDate', Column::class, array(
//                'title' => 'Campaign CampaignStartDate',
//                ))
//            ->add('campaign.campaignEndDate', Column::class, array(
//                'title' => 'Campaign CampaignEndDate',
//                ))
//            ->add('campaign.entryDate', Column::class, array(
//                'title' => 'Campaign EntryDate',
//                ))
//            ->add('campaign.campaignYear', Column::class, array(
//                'title' => 'Campaign CampaignYear',
//                ))
//            ->add('campaign.campaignMonth', Column::class, array(
//                'title' => 'Campaign CampaignMonth',
//                ))
//            ->add('campaign.id', Column::class, array(
//                'title' => 'Campaign Id',
//                ))

//            ->add('district.districtNamePashtu', Column::class, array(
//                'title' => 'District DistrictNamePashtu',
//                ))
//            ->add('district.districtNameDari', Column::class, array(
//                'title' => 'District DistrictNameDari',
//                ))
//            ->add('district.districtLpdStatus', Column::class, array(
//                'title' => 'District DistrictLpdStatus',
//                ))
//            ->add('district.districtRiskStatus', Column::class, array(
//                'title' => 'District DistrictRiskStatus',
//                ))
//            ->add('district.districtIcnStatus', Column::class, array(
//                'title' => 'District DistrictIcnStatus',
//                ))
//            ->add('district.entryDate', Column::class, array(
//                'title' => 'District EntryDate',
//                ))
//            ->add('district.id', Column::class, array(
//                'title' => 'District Id',
//                ))
//            ->add(null, ActionColumn::class, array(
//                'title' => $this->translator->trans('sg.datatables.actions.title'),
//                'actions' => array(
//                    array(
//                        'route' => 'admindata_show',
//                        'route_parameters' => array(
//                            'id' => 'id'
//                        ),
//                        'label' => $this->translator->trans('sg.datatables.actions.show'),
//                        'icon' => 'glyphicon glyphicon-eye-open',
//                        'attributes' => array(
//                            'rel' => 'tooltip',
//                            'title' => $this->translator->trans('sg.datatables.actions.show'),
//                            'class' => 'btn btn-primary btn-xs',
//                            'role' => 'button'
//                        ),
//                    ),
//                    array(
//                        'route' => 'admindata_edit',
//                        'route_parameters' => array(
//                            'id' => 'id'
//                        ),
//                        'label' => $this->translator->trans('sg.datatables.actions.edit'),
//                        'icon' => 'glyphicon glyphicon-edit',
//                        'attributes' => array(
//                            'rel' => 'tooltip',
//                            'title' => $this->translator->trans('sg.datatables.actions.edit'),
//                            'class' => 'btn btn-primary btn-xs',
//                            'role' => 'button'
//                        ),
//                    )
//                )
//            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity()
    {
        return 'App\Entity\CoverageData';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'coveragedata_datatable';
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
