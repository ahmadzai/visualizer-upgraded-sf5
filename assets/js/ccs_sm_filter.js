'use strict';

import $ from 'jquery'
import Filter from './filter/OdkFilter';
import ApiCall from './common/AjaxRequest';
import {CcsSmSetting as Setting} from './setting/';
import Alerts from './common/Alerts';
import FilterListener from './filter/FilterListener';
import FilterControl from './filter/FilterControl';

$(function () {

    new Filter();                                           // initialize filter

    let listener = new FilterListener();
    let filterControl = new FilterControl();

    $('.loading').hide();

    $('.btn-cum-res').hide();

    $('#tbl-odk-data').DataTable({
        "lengthMenu": [[15, 25, 50, 100, -1], [15, 25, 50, 100, "All"]],
        'scrollX': true
    });

    filterControl.setFilterState(listener.listenCcsSm());    // store the state of the filter

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
    let filterData = listener.listenCcsSm();

    if(filterData.campaign.length === 0) {
        Alerts.error("Please select at least one month");
        return false;
    }

    if(cumulative) {
        filterData = {...filterData, cumulative: true};
    }

    let filterState = filterControl.ccsSmFilterState(filterData);

    if(filterState === true) {
        apiCall.partiallyUpdate(url, Setting, filterData, 'loading');
    } else if (filterState === false)
        Alerts.filterInfo();
}
