<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Created by PhpStorm.
 * User: wakhan
 * Date: 8/26/2017
 * Time: 12:10 PM
 */
class MenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private $factory;
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    /**
     * @var RoleHierarchyInterface
     */
    private $hierarchy;
    /**
     * @var RequestStack
     */
    private $requestStack;


    public function __construct(FactoryInterface $factory,
                                TokenStorageInterface $tokenStorage,
                                RoleHierarchyInterface $hierarchy,
                                RequestStack $requestStack)
    {

        $this->factory = $factory;
        $this->tokenStorage = $tokenStorage;
        $this->hierarchy = $hierarchy;
        $this->requestStack = $requestStack;
    }


    /**
     * This Menu is needed to be replaced by dynamic menu, which will be loading from database
     * Entity, this is a temporary solution
     */
    public function mainMenu(array $options)
    {
        $reachableRoles = $this->userRoles();

        $menu = $this->factory->createItem('Home');
        $menu->setAttribute('icon', 'fa-home');
        $menu->setChildrenAttributes(array('class'=>'sidebar-menu', 'data-widget'=>'tree'));
        // ---------------------------------------------- COVID 19 Link ------------------------------------------
        $menu->addChild('COVID-19', array('route'=>'covid19_cases'));
        $menu['COVID-19']->setAttribute('icon','fa-warning text-red');
        // ----------------------------------------------- End of COVID 19 Link ----------------------------------

        $menu->addChild("Home", array('route'=>'home'))
            ->setExtra('info', 'the main dashboard')
            ->setExtra('routes', ['home', 'main_cluster_dashboard']);
        $menu['Home']->setAttribute('icon','fa-home');
        // ------------------------------------------------ Coverage Data ----------------------------------------
        $menu->addChild("Coverage Data", array('uri'=>'#'))
            ->setExtra('info', 'the main dashboard');
        $menu['Coverage Data']->setAttribute('icon','fa-database');
        $menu['Coverage Data']->setAttribute('sub_menu_icon', 'fa-angle-left');

        // Sub menu (child of Coverage Data)
        // Dashboard
        $menu['Coverage Data']->addChild("Dashboard", array('route'=>'coverage_data'))
            ->setExtra('info', 'Coverage Data')
            ->setExtra('routes', ['coverage_data','coverage_data_cluster']);
        $menu['Coverage Data']->setChildrenAttributes(array('class'=>'treeview-menu'));
        $menu['Coverage Data']['Dashboard']->setAttribute('icon','fa-dashboard');
        if(in_array("ROLE_EDITOR", $reachableRoles)) {
            // Data Download
            $menu['Coverage Data']->addChild("Download", array('route'=>'coverage_data_download'))
                ->setExtra('info', 'Coverage Data');
            // if the user had edit role

            $menu['Coverage Data']['Download']->setAttribute('icon', 'fa-download');

            $menu['Coverage Data']->addChild("Upload", array('route' => 'import_data', 'routeParameters' => ['entity' => 'coverage_data']))
                ->setExtra('info', 'Coverage Data')
                ->setExtra('routes', [
                    ['route' => 'import_data', 'parameters' => ['entity'=>'coverage_data']],
                    ['route' => 'import_data_handle', 'parameters' => [
                            'entity'=>'coverage_data',
                            'fileId'=>$this->requestStack->getCurrentRequest()->get('fileId')
                    ]],
                    ['route' => 'sync_data_view', 'parameters' => [
                        'entity'=>'coverage_data',
                        'fileId'=>$this->requestStack->getCurrentRequest()->get('fileId')
                    ]],
                ]);
            $menu['Coverage Data']['Upload']->setAttribute('icon', 'fa-upload');
            // Data Entry
            $menu['Coverage Data']->addChild("Data Entry", array('uri' => '#'))->setExtra('info', 'of Coverage Data');
            $menu['Coverage Data']['Data Entry']->setAttribute('icon', 'fa-table');

        }

        //------------------------------------------------------- Catchup Data ---------------------------------------
        $menu->addChild("Catchup Data", array('uri'=>'#'))->setExtra('info', 'the main dashboard');
        $menu['Catchup Data']->setAttribute('icon','fa-database');
        $menu['Catchup Data']->setAttribute('sub_menu_icon', 'fa-angle-left');

        // Sub menu (child of Catchup Data
        // Dashboard
        $menu['Catchup Data']->addChild("Dashboard", array('route'=>'catchup_data'))
            ->setExtra('info', 'Catchup Data')
            ->setExtra('routes', ['catchup_data','catchup_data_cluster']);
        $menu['Catchup Data']->setChildrenAttributes(array('class'=>'treeview-menu'));
        $menu['Catchup Data']['Dashboard']->setAttribute('icon','fa-dashboard');
        if(in_array("ROLE_EDITOR", $reachableRoles)) {
            // Data Download
            $menu['Catchup Data']->addChild("Download", array('route' => 'catchup_data_download'))->setExtra('info', 'Catchup Data');
            $menu['Catchup Data']['Download']->setAttribute('icon', 'fa-download');
            // Data Upload
            $menu['Catchup Data']->addChild("Upload", array('route' => 'import_data', 'routeParameters' => ['entity' => 'catchup_data']))
                ->setExtra('info', 'Catchup Data')
                ->setExtra('routes', [
                    ['route' => 'import_data', 'parameters' => ['entity'=>'catchup_data']],
                    ['route' => 'import_data_handle', 'parameters' => [
                        'entity'=>'catchup_data',
                        'fileId'=>$this->requestStack->getCurrentRequest()->get('fileId')
                    ]],
                    ['route' => 'sync_data_view', 'parameters' => [
                        'entity'=>'catchup_data',
                        'fileId'=>$this->requestStack->getCurrentRequest()->get('fileId')
                    ]],
                ]);
            $menu['Catchup Data']['Upload']->setAttribute('icon', 'fa-upload');
            // Data Entry
            $menu['Catchup Data']->addChild("Data Entry", array('uri' => '#'))->setExtra('info', 'Catchup Data');
            $menu['Catchup Data']['Data Entry']->setAttribute('icon', 'fa-table');
        }

        //------------------------------------------------------- Refusals Committees Performance Dash-Board --------
        $menu->addChild("Refusals Committees", array('uri'=>'#'))->setExtra('info', 'refusals committees performance dashboard');
        $menu['Refusals Committees']->setAttribute('icon','fa-database');
        $menu['Refusals Committees']->setAttribute('sub_menu_icon', 'fa-angle-left');

        // Sub menu (child of Catchup Data
        // Dashboard
        $menu['Refusals Committees']->addChild("Dashboard", array('route'=>'ref_committees'))
            ->setExtra('info', 'Refusals Committees Data')
            ->setExtra('routes', ['ref_committees', 'cluster_ref_committees']);
        $menu['Refusals Committees']->setChildrenAttributes(array('class'=>'treeview-menu'));
        $menu['Refusals Committees']['Dashboard']->setAttribute('icon','fa-dashboard');
        if(in_array("ROLE_EDITOR", $reachableRoles)) {
            // Data Download
            $menu['Refusals Committees']->addChild("Download", array('route' => 'ref_committees_data_download'))
                ->setExtra('info', 'Download Refusals Committees Data');
            $menu['Refusals Committees']['Download']->setAttribute('icon', 'fa-download');
            // Data Upload
            $menu['Refusals Committees']->addChild("Upload", array('route' => 'import_data', 'routeParameters' => ['entity' => 'refusal_comm']))
                ->setExtra('info', 'Catchup Data')
                ->setExtra('routes', [
                    ['route' => 'import_data', 'parameters' => ['entity'=>'refusal_comm']],
                    ['route' => 'import_data_handle', 'parameters' => [
                        'entity'=>'refusal_comm',
                        'fileId'=>$this->requestStack->getCurrentRequest()->get('fileId')
                    ]]
                ]);
            $menu['Refusals Committees']['Upload']->setAttribute('icon', 'fa-upload');
            // Data Entry
            $menu['Refusals Committees']->addChild("Data Entry", array('uri' => '#'))->setExtra('info', 'Catchup Data');
            $menu['Refusals Committees']['Data Entry']->setAttribute('icon', 'fa-table');
        }

        if(in_array("ROLE_NORMAL_USER", $reachableRoles)) {
            //------------------------------------------------------- ICN Data TPM ---------------------------------------
            $menu->addChild("ICN & Other Staff", array('uri' => '#'))
                ->setExtra('info', 'ICN Monitoring Report');
            $menu['ICN & Other Staff']->setAttribute('icon', 'fa-database');
            $menu['ICN & Other Staff']->setAttribute('sub_menu_icon', 'fa-angle-left');

            // Sub menu
            // Dashboard
            $menu['ICN & Other Staff']->addChild("Dashboard", array('route' => 'staff_dashboard'))
                ->setExtra('info', 'of Polio/ICN staff');
            $menu['ICN & Other Staff']->setChildrenAttributes(array('class' => 'treeview-menu'));
            $menu['ICN & Other Staff']['Dashboard']->setAttribute('icon', ' fa-bar-chart');

            // Provincial Staff
            $menu['ICN & Other Staff']->addChild("Provincial Staff", array('route' => 'staff-pco_index'))
                ->setExtra('routes', ['staff-pco_show', 'staff-pco_index'])
                ->setExtra('info', 'Details');
            $menu['ICN & Other Staff']->setChildrenAttributes(array('class' => 'treeview-menu'));
            $menu['ICN & Other Staff']['Provincial Staff']->setAttribute('icon', ' fa-users');

            // District Level Staff
            $menu['ICN & Other Staff']->addChild("District Level Staff", array('route' => 'staff-icn_index'))
                ->setExtra('routes', ['staff-icn_show', 'staff-icn_index'])
                ->setExtra('info', 'Details');
            $menu['ICN & Other Staff']->setChildrenAttributes(array('class' => 'treeview-menu'));
            $menu['ICN & Other Staff']['District Level Staff']->setAttribute('icon', ' fa-users');
        }

        // TPM SM/CCS Upload Option
        if(in_array("ROLE_EDITOR", $reachableRoles)) {
            $menu['ICN & Other Staff']->addChild("Upload", array('route' => 'import_staff_icn',
                'extras' => ['route' => '']))
                ->setExtra('info', 'Provincial or District Staff')
                ->setExtra('routes', [
                    ['route'=> 'import_staff_icn'],
                    ['route' => 'import_data', 'parameters' => ['entity'=>'staff_pco']],
                    ['route' => 'import_data_handle', 'parameters' => [
                        'entity'=>'staff_pco',
                        'fileId'=>$this->requestStack->getCurrentRequest()->get('fileId')
                    ]],
                    ['route' => 'import_data', 'parameters' => ['entity'=>'staff_icn']],
                    ['route' => 'import_data_handle', 'parameters' => [
                        'entity'=>'staff_icn',
                        'fileId'=>$this->requestStack->getCurrentRequest()->get('fileId')
                    ]]
                ]);
            $menu['ICN & Other Staff']['Upload']->setAttribute('icon', 'fa-upload');
        }

        if(in_array("ROLE_PARTNER", $reachableRoles)) {
            //------------------------------------------------------- ICN Data TPM ---------------------------------------
            $menu->addChild("BPHS Plus", array('uri' => '#'))
                ->setExtra('info', 'Indicators dashboard');
            $menu['BPHS Plus']->setAttribute('icon', 'fa-plus');
            $menu['BPHS Plus']->setAttribute('sub_menu_icon', 'fa-angle-left');

            // Sub menu
            // Dashboard
            $menu['BPHS Plus']->addChild("Dashboard", array('route' => 'bphs_dashboard'))
                ->setExtra('info', 'of BPHS+ Indicators');
            $menu['BPHS Plus']->setChildrenAttributes(array('class' => 'treeview-menu'));
            $menu['BPHS Plus']['Dashboard']->setAttribute('icon', ' fa-bar-chart');

            // Health Facility
            $menu['BPHS Plus']->addChild("Health Facilities", array('route' => 'bphs_hf_index'))
                ->setExtra('info', 'List')
                ->setExtra('routes', [
                    'bphs_hf_index', 'bphs_hf_show', 'bphs_hf_new', 'bphs_hf_edit'
                ]);
            $menu['BPHS Plus']->setChildrenAttributes(array('class' => 'treeview-menu'));
            $menu['BPHS Plus']['Health Facilities']->setAttribute('icon', ' fa-hospital-o');

            // Indicators
            $menu['BPHS Plus']->addChild("BPHS+ Indicators", array('route' => 'bphs_indicator_index'))
                ->setExtra('info', 'List')
                ->setExtra('routes', [
                    'bphs_indicator_index', 'bphs_indicator_show', 'bphs_indicator_new', 'bphs_indicator_edit'
                ]);
            $menu['BPHS Plus']->setChildrenAttributes(array('class' => 'treeview-menu'));
            $menu['BPHS Plus']['BPHS+ Indicators']->setAttribute('icon', ' fa-line-chart');

            // Health Facility and Indicator
            $menu['BPHS Plus']->addChild("Health Facility/Indicators", array('route' => 'bphs_hf_indicator_index'))
                ->setExtra('info', 'List')
                ->setExtra('routes', [
                    'bphs_hf_indicator_index', 'bphs_hf_indicator_show', 'bphs_hf_indicator_new', 'bphs_hf_indicator_edit'
                ]);
            $menu['BPHS Plus']->setChildrenAttributes(array('class' => 'treeview-menu'));
            $menu['BPHS Plus']['Health Facility/Indicators']->setAttribute('icon', '  fa-table');

            // Health Facility's Indictors' Reach
            $menu['BPHS Plus']->addChild("Indicators Reach", array('route' => 'bphs_indicator_reach_index'))
                ->setExtra('info', 'List')
                ->setExtra('routes', [
                    'bphs_indicator_reach_index', 'bphs_indicator_reach_show', 'bphs_indicator_reach_edit',
                    'bphs_indicator_reach_new'
                ]);
            $menu['BPHS Plus']->setChildrenAttributes(array('class' => 'treeview-menu'));
            $menu['BPHS Plus']['Indicators Reach']->setAttribute('icon', '  fa-table');
        }

        // TPM SM/CCS Upload Option
        if(in_array("ROLE_EDITOR", $reachableRoles)) {
            $menu['BPHS Plus']->addChild("Upload", array('route' => 'import_data',
                'routeParameters' => ['entity'=>'bphs_indicator_reach']))
                ->setExtra('info', 'Monthly Indicators Reach Data')
                ->setExtra('routes', [
                    ['route' => 'import_data', 'parameters' => ['entity'=>'bphs_indicator_reach']],
                    ['route' => 'import_data_handle', 'parameters' => [
                        'entity'=>'bphs_indicator_reach',
                        'fileId'=>$this->requestStack->getCurrentRequest()->get('fileId')
                    ]]
                ]);
            $menu['BPHS Plus']['Upload']->setAttribute('icon', 'fa-upload');
        }


        if(in_array("ROLE_ADMIN", $reachableRoles))
            $menu->addChild('other', array('route'=>'home'))->setAttribute('icon','fa-link');


        return $menu;
    }

    public function sideMenu(array $options)
    {

        $reachableRoles = $this->userRoles();

        $menu = $this->factory->createItem('Site Control');
        $menu->setExtra('info', 'Contents Management');
        $menu->setAttribute('icon', 'fa-gear');
        $menu->setChildrenAttributes(array('class'=>'control-sidebar-menu sidebar-menu', 'data-widget'=>'tree'));

        // Campaign Management
        if(in_array("ROLE_EDITOR", $reachableRoles)) {
            $menu->addChild("Campaigns Management", array('route' => 'campaign_index'))
                ->setExtra('info', 'Manage Campaigns');
            $menu['Campaigns Management']->setAttribute('icon', 'fa-eyedropper')
                ->setExtra('routes', [
                    'campaign_new', 'campaign_show', 'campaign_edit', 'campaign_index'
                ]);
        }

        if(in_array("ROLE_ADMIN", $reachableRoles)) {
            // Users Management
            $menu->addChild("User Management", array('route' => 'user_index'))
                ->setExtra('info', 'Manage Users')
                ->setExtra('routes', []);
            $menu['User Management']->setAttribute('icon', 'fa-user');
            // ------------------------------------------------ Location Mgt ------------------------------------------------
            $menu->addChild("Location", array('uri' => '#'))->setExtra('info', 'Manage Locations');
            $menu['Location']->setAttribute('icon', 'fa-map');
            $menu['Location']->setAttribute('sub_menu_icon', 'fa-angle-left');
            $menu['Location']->setChildrenAttributes(array('class' => 'treeview-menu'));
            // Sub Menu
            $menu['Location']->addChild("Provinces", array('route' => 'province_index'))
                ->setExtra('info', 'Manage Provinces')
                ->setExtra('routes', [
                   'province_index', 'province_new', 'province_show', 'province_edit'
                ]);
            $menu['Location']['Provinces']->setAttribute('icon', 'fa-cog');
            $menu['Location']->addChild("Districts", array('route' => 'district_index'))
                ->setExtra('info', 'Manage Districts')
                ->setExtra('routes', [
                    'district_index', 'district_new', 'district_show', 'district_edit'
                ]);
            $menu['Location']['Districts']->setAttribute('icon', 'fa-cog');

        }
        if(in_array("ROLE_EDITOR", $reachableRoles)) {
            // ------------------------------------------------ Upload Mgt ------------------------------------------------
            $menu->addChild("Upload Mgt", array('uri' => '#'))->setExtra('info', 'Manage Data Upload');
            $menu['Upload Mgt']->setAttribute('icon', 'fa-upload');
            $menu['Upload Mgt']->setAttribute('sub_menu_icon', 'fa-angle-left');
            $menu['Upload Mgt']->setChildrenAttributes(array('class' => 'treeview-menu'));
        }

        if(in_array("ROLE_ADMIN", $reachableRoles)) {
            // Sub Menu
            $menu['Upload Mgt']->addChild("Uploader Mgt", array('route' => 'uploadmanager_index'))
                ->setExtra('info', 'Mange Uploader')
                ->setExtra('routes', [
                    'uploadmanager_index', 'uploadmanager_new', 'uploadmanager_show', 'uploadmanager_edit'
                ]);
            $menu['Upload Mgt']['Uploader Mgt']->setAttribute('icon', 'fa-upload');
        }

        if(in_array("ROLE_EDITOR", $reachableRoles)) {
            $menu['Upload Mgt']->addChild("Manage Files", array('route' => 'manage_uploaded_files'))
                ->setExtra('info', 'Manage Uploaded Files');
            $menu['Upload Mgt']['Manage Files']->setAttribute('icon', 'fa-file');
        }

        if(in_array("ROLE_EDITOR", $reachableRoles)) {

            // ------------------------------------------------ External Services Mgt ------------------------------------------------
            $menu->addChild("BPHS+ Control", array('uri' => '#'))
                ->setExtra('info', 'Manage Lookup Data');
            $menu['BPHS+ Control']->setAttribute('icon', 'fa-plus');
            $menu['BPHS+ Control']->setAttribute('sub_menu_icon', 'fa-angle-left');
            $menu['BPHS+ Control']->setChildrenAttributes(array('class' => 'treeview-menu'));
            // Sub Menu
            $menu['BPHS+ Control']->addChild("Health Facilities", array('route' => 'bphs_hf_index'))
                ->setExtra('info', 'Management')
                ->setExtra('routes', ['bphs_hf_new', 'bphs_hf_edit', 'bphs_hf_index', 'bphs_hf_show'])
                ->setAttribute('icon', 'fa-hospital-o');
            $menu['BPHS+ Control']->addChild("Indicators", array('route' => 'bphs_indicator_index'))
                ->setExtra('info', 'Managements')
                ->setExtra('routes', ['bphs_indicator_new', 'bphs_indicator_edit', 'bphs_indicator_index', 'bphs_indicator_show'])
                ->setAttribute('icon', 'fa-line-chart');
            $menu['BPHS+ Control']->addChild("Link Indicator with HF", array('route' => 'bphs_hf_indicator_index'))
                ->setExtra('info', 'Managements')
                ->setExtra('routes', ['bphs_hf_indicator_edit', 'bphs_hf_indicator_show',
                    'bphs_hf_indicator_new', 'bphs_hf_indicator_index'])
                ->setAttribute('icon', 'fa-link');
            $menu['BPHS+ Control']->addChild("Indicators Reach", array('route' => 'bphs_indicator_reach_index'))
                ->setExtra('info', 'Managements')
                ->setExtra('routes', ['bphs_indicator_reach_new', 'bphs_indicator_reach_edit',
                    'bphs_indicator_reach_index', 'bphs_indicator_reach_show'])
                ->setAttribute('icon', 'fa-bar-chart');
            $menu['BPHS+ Control']->addChild("Bulk Upload", array('route' => 'bphs_bulk_upload'))
                ->setExtra('info', 'Data')
                ->setExtra('routes', [
                    ['route'=> 'bphs_bulk_upload'],
                    ['route' => 'import_data', 'parameters' => ['entity'=>'bphs_health_facility']],
                    ['route' => 'import_data_handle', 'parameters' => [
                        'entity'=>'bphs_health_facility',
                        'fileId'=>$this->requestStack->getCurrentRequest()->get('fileId')
                    ]],
                    ['route' => 'import_data', 'parameters' => ['entity'=>'bphs_indicator']],
                    ['route' => 'import_data_handle', 'parameters' => [
                        'entity'=>'bphs_indicator',
                        'fileId'=>$this->requestStack->getCurrentRequest()->get('fileId')
                    ]],
                    ['route' => 'import_data', 'parameters' => ['entity'=>'bphs_hf_indicator']],
                    ['route' => 'import_data_handle', 'parameters' => [
                        'entity'=>'bphs_hf_indicator',
                        'fileId'=>$this->requestStack->getCurrentRequest()->get('fileId')
                    ]]
                ])
                ->setAttribute('icon', 'fa-upload');

        }

        if(in_array("ROLE_ADMIN", $reachableRoles)) {

            // ------------------------------------------------ External Services Mgt ------------------------------------------------
            $menu->addChild("Ext Services Mgt", array('uri' => '#'))
                ->setExtra('info', 'Manage External Services');
            $menu['Ext Services Mgt']->setAttribute('icon', 'fa-plug');
            $menu['Ext Services Mgt']->setAttribute('sub_menu_icon', 'fa-angle-left');
            $menu['Ext Services Mgt']->setChildrenAttributes(array('class' => 'treeview-menu'));
            // Sub Menu
            $menu['Ext Services Mgt']->addChild("Register Service", array('route' => 'apiconnect_index'))
                ->setExtra('info', 'Service Registration');
            $menu['Ext Services Mgt']['Register Service']->setAttribute('icon', 'fa-registered');
            $menu['Ext Services Mgt']->addChild("Sync ONA Data", array('route' => 'ona_connect'))
                ->setExtra('info', 'Sync remote data');
            $menu['Ext Services Mgt']['Sync ONA Data']->setAttribute('icon', 'fa-retweet');

            // BenchMark Management
            $menu->addChild("Heatmap Benchmark", array('route' => 'heatmap_benchmark_index'))
                ->setExtra('info', 'Manage Heatmap Benchmarks');
            $menu['Heatmap Benchmark']->setAttribute('icon', 'fa-table');
        }

        return $menu;
    }

    public function covid19Menu( array $options) {

        $menu = $this->factory->createItem('Covid19Menu');
        $menu->setChildrenAttributes(array('class'=>'nav navbar-nav', 'data-widget'=>''));

        $menu->addChild("Cases", array('route' => 'covid19_cases'))
            ->setExtra('info', 'COVID-19 Cases in Afghanistan');
//        $menu->addChild("Cases", array('route' => 'covid19_cases'))
//            ->setExtra('info', 'COVID-19 Cases in Afghanistan');
//        $menu->addChild("Response: Supplies", array('route' => 'covid19_supplies'))
//            ->setExtra('info', 'Supplies response by UNICEF Polio Team');
//        $menu->addChild("Response: C4D", array('route' => 'covid19_c4d'))
//            ->setExtra('info', 'C4D response by UNICEF Polio Team');

        return $menu;
    }

    /**
     * @return string[]
     */
    private function userRoles(): array
    {
        $roles = $this->tokenStorage->getToken()->getUser()->getRoles();
        $reachableRoles = $this->hierarchy->getReachableRoleNames($roles);
        $reachableRoles = array_unique($reachableRoles);
        return $reachableRoles;
    }


}
