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

    // setting for the table
    $('.dash-Datatable').DataTable({
        //"lengthMenu": [[15, 25, 50, 100, -1], [15, 25, 50, 100, "All"]],
        'scrollX': true,
        'scrollY': "50vh",
        'scrollCollapse': true,
        'paging':         false,
        'fixedColumns':   {
            leftColumns: $('#bphs_table').data('fixedCols'),
            heightMatch: 'none'
        },
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
        Setting.bphs_table.setting.fixedColumns.leftColumns = 1;
        if(filterData.district.length > 0)
            Setting.bphs_table.setting.fixedColumns.leftColumns = 2;
        if(filterData.facility.length > 0)
            Setting.bphs_table.setting.fixedColumns.leftColumns = 3;
        apiCall.partiallyUpdate(url, Setting, filterData, 'loading');
    } else if (filterState === false)
        Alerts.filterInfo();
}
