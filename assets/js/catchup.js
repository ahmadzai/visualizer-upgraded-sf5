'use strict';

import $ from 'jquery';

import 'bootstrap-multiselect';
import MainFilter from './filter/MainFilter';
import FilterListener from './filter/FilterListener';
import ApiCall from './common/AjaxRequest';
import FilterControl from './filter/FilterControl';
import Alerts from './common/Alerts';
import { SettingCatchup } from './setting/';
import MapFilter from './maps/FilterMap';


$(document).ready(function () {

    // intitalize filter
    new MainFilter();
    let listener = new FilterListener();
    let apiCall = new ApiCall();
    let filterControl = new FilterControl();
    let mapFilter = new MapFilter();
    let mapSetting = {
        url: 'load_geojson',
        params: {'geoType':'province', 'dataType':'region', 'indicator':'TotalRemaining'},
        mapFilter,
    };

    let urlPostFix = $('#ajaxUrl').val();
    mapSetting.params['source'] = urlPostFix;              // define this in the map setting
    let url = "ajax_"+urlPostFix;
    let  Setting = SettingCatchup;                          // Dynamic Setting as per loaded page


    // load for this first time
    let filterData = listener.listenMain()   ;              // call listener to return filters data
    // set filterState
    filterControl.setFilterState({...filterData});
    filterData.loadWhat = 'trend';
    apiCall.updateAll(url, Setting, filterData,
        {...filterData, loadWhat:'info'}, {...mapSetting}); // passed mapFilter to set map data


    // when map indicator changed
    $('.filter-map').click(function (event) {

        event.preventDefault();
        let filterData = listener.listenMain();
        // check if districts were selected
        let dataType = "region";
        if(filterData.district.length > 0) {
            dataType = "district";
        } else if(filterData.province.length > 0)
            dataType = "province";

        mapFilter.createMap(dataType, $(this).data('type'), mapSetting.params.source);
    });

    // When filter button is clicked
    $('#filterButton').click(function () {

        // these vars used for controlling map geo data and legend data
        let geoType = {...mapSetting};

        let filterData = listener.listenMain();
        // check if districts were selected
        if(filterData.district.length > 0) {
            geoType.params.geoType = "district";
            geoType.params.dataType = "district";
        } else if(filterData.province.length > 0)
            geoType.params.dataType = "province";
        else
            geoType.params.dataType = "region";

        //console.log(filterData);
        if(filterData.campaign.length === 0)
            Alerts.error('Please select at least one campaign');
        else {
            let checkFilter = filterControl.checkFilterState(filterData);
            if (!checkFilter) {
                Alerts.filterInfo();
            } else {
                // three conditions there
                if(checkFilter === 'both') {
                    apiCall.updateAll(url, Setting, {...filterData, loadWhat: 'trend'},
                        {...filterData, loadWhat: 'info'}, geoType);
                    //apiCall.updateMap("load_geojson", geoType, mapFilter);

                } else {
                    let mapOptions = checkFilter === 'trend' ? false : geoType;
                    apiCall.partiallyUpdate(url, Setting, filterData,
                        checkFilter + '-loader', mapOptions);
                }
                //Todo: trigger update map here if dataType wasn't false
            }
        }
    })
});