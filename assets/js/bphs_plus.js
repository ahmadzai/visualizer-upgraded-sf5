'use strict';

import $ from 'jquery';
import 'datatables.net-fixedcolumns';

import Filter from './filter/BphsFilter';
import ApiCall from './common/AjaxRequest';
import Alerts from './common/Alerts';
import FilterListener from './filter/FilterListener';
import { BphsDashboardSetting as Setting } from './setting/';
import FilterControl from './filter/FilterControl';

$(function () {

    new Filter();                                           // initialize filter

    let listener = new FilterListener();
    let filterControl = new FilterControl();

    $('.loading').hide();

    $('.btn-cum-res').hide();

    filterControl.setFilterState(listener.listenBphs());    // store the state of the filter

    // load the charts for the first time
    doAjax(true, filterControl, listener);

    // setting for the table
    $('.dash-Datatable').DataTable({
        //"lengthMenu": [[15, 25, 50, 100, -1], [15, 25, 50, 100, "All"]],
        'scrollX': true,
        'scrollY': "50vh",
        'scrollCollapse': true,
        'paging':         false,
        'fixedColumns': false,
        'dom': 'Bfrtip',
        'buttons': [
            'copyHtml5', 'csvHtml5'
        ]
    });
    // Filter Heatmap only table
    $(".btn-cum-res").click(function (event) {
        event.preventDefault();
        doAjax(true, filterControl, listener);

    });

    // When filter button is clicked
    $('#filterButton').click(function (event) {
        event.preventDefault();
        doAjax(false, filterControl, listener);
    });

});

function doAjax(cumulative, filterControl, listener) {

    let url = $('.route-url').data('route');                 // create url
    let apiCall = new ApiCall();                            // create an object of ajax calls
    let filterData = listener.listenBphs();

    if(filterData.campaign.length === 0) {
        Alerts.error("Please select at least one month");
        return false;
    }

    if(cumulative) {
        filterData = {...filterData, cumulative: true};
    }

    let filterState = filterControl.bphsFilterState(filterData);

    if(filterState === true) {
        // hack to change the number of fixedCols for the table
        setFixedColumns(filterData, Setting);

        apiCall.partiallyUpdate(url, Setting, filterData, 'loading');
    } else if (filterState === false)
        Alerts.filterInfo();
}

function setFixedColumns(filterData, Setting) {
    Setting.bphs_table_cum.setting.fixedColumns = false;
    Setting.bphs_table_months.setting.fixedColumns = {leftColumns: 1, heightMatch: 'none'}
    if(filterData.facility.length > 0) {
        Setting.bphs_table_cum.setting.fixedColumns = {leftColumns: 3, heightMatch: 'none'};
        Setting.bphs_table_months.setting.fixedColumns = {leftColumns: 4, heightMatch: 'none'}
    }
    else if(filterData.district.length > 0) {
        Setting.bphs_table_cum.setting.fixedColumns = {leftColumns: 2, heightMatch: 'none'};
        Setting.bphs_table_months.setting.fixedColumns = {leftColumns: 3, heightMatch: 'none'}
    }
    else if(filterData.province.length > 0) {
        Setting.bphs_table_cum.setting.fixedColumns = {leftColumns: 1, heightMatch: 'none'};
        Setting.bphs_table_months.setting.fixedColumns = {leftColumns: 2, heightMatch: 'none'}
    }
    return Setting;
}
