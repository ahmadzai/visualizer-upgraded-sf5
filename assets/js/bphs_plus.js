'use strict';

import $ from 'jquery'
import Filter from './filter/BphsFilter';
import ApiCall from './common/AjaxRequest';
import Alerts from './common/Alerts';
import FilterListener from './filter/FilterListener';
import FilterControl from './filter/FilterControl';

$(function () {

    new Filter();                                           // initialize filter

    let listener = new FilterListener();
    let filterControl = new FilterControl();

    $('.loading').hide();

    $('.btn-cum-res').hide();

    filterControl.setFilterState(listener.listenBphs());    // store the state of the filter

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
        apiCall.partiallyUpdate(url, Setting, filterData, 'loading');
    } else if (filterState === false)
        Alerts.filterInfo();
}
