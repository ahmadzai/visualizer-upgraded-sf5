'use strict';

import $ from 'jquery'
import Filter from './filter/ClusterFilter';
import ApiCall from './common/AjaxRequest';
import {CatchupCluster, CoverageCluster, MainCluster, RefCommCluster} from './setting/';
import Alerts from './common/Alerts';
import FilterListener from './filter/FilterListener';
import FilterControl from './filter/FilterControl';

$(function () {

    new Filter();                                           // initialize filter
    let apiCall = new ApiCall();                            // create an object of ajax calls
    let listener = new FilterListener();
    let filterControl = new FilterControl();
    let urlPostFix = $('#ajaxUrl').val();                   // create an object of filter listener
    let url = 'ajax_cluster_' + urlPostFix;                 // create url

    let  Setting = MainCluster;                             // Dynamic Setting as per loaded page
    if(urlPostFix === "coverage_data")
        Setting = CoverageCluster;
    else if(urlPostFix === "catchup_data")
        Setting = CatchupCluster;
    else if(urlPostFix === "ref_committees")
        Setting = RefCommCluster;

    // check if the info window should be opend
    let preLoadedDistrict = $('#ajaxUrl').data('district'); // check pre-loaded district
    if(preLoadedDistrict == '0') {                          // show information dialog if no district
        Alerts.clusterInfo();
        $('.filter-loading').hide();
    }

    filterControl.setFilterState({campaign:[], province:'', district:'', cluster:[]});           // store the state of the filter
    // load it for first time, if there was some clusters
    let selectedClusters = $('#filterCluster').val();       // load for the first time if clusters
    if(selectedClusters.length > 0) {                       // was selected
        let filterData = listener.listenCluster();          // call listener to return filters data
        filterControl.setFilterState(filterData);
        filterData.calcType = 'trend';                     // this key is required for the api
        filterData.selectType = "";                         // this key is required for the api
        apiCall.updateAll(url, Setting, filterData, {...filterData, calcType:'info'});
        // to load the main data as well

    }
    // Filter Heatmap only table
    $(".filter-heatmap").click(function (event) {
        event.preventDefault();
        filterDashboard(listener, filterControl, apiCall, Setting, event, $(this).data('type'));

    });

    // When filter button is clicked
    $('#filterButton').click(function (event) {
        event.preventDefault();
        filterDashboard(listener, filterControl, apiCall, Setting, event);
    })


});

function filterDashboard(listener, filterControl, apiCall, Setting, event, selectType = 'per') {

    let url = 'ajax_cluster_' + $('#ajaxUrl').val();                 // create url
    let filterData = listener.listenCluster();
    let target = event.target.id;

    if(filterData.district === null || filterData.district === undefined) {
        Alerts.error("Please select a district");
        return false;
    }

    if(filterData.cluster.length === 0) {
        Alerts.warning("No cluster selected, or there is no data for any " +
            "cluster for the selected campaigns");
        return false;
    }

    if(target !== "filterButton") {

        filterData = {...filterData, selectType}
    }

    let filterState = filterControl.checkClusterFilterState(filterData);
    if(filterState !== false) {
        if (target === "filterButton") {
            filterState === "both" ?
                apiCall.updateAll(url, Setting, {...filterData, calcType:'trend'},
                    {...filterData, calcType: 'info'}) :
                apiCall.partiallyUpdate(url, Setting, {...filterData, calcType:filterState},
                    filterState+'-loader');
        } else {
            let calcType = "normal";
            if( selectType.indexOf("Per") !== -1)
                calcType = "percent";
            apiCall.partiallyUpdate(url, Setting, {...filterData, calcType, selectType}, 'trend-loader');
        }
    } else
        Alerts.filterInfo();

}
