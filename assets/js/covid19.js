'use strict';

import $ from 'jquery'
import ApiCall from './common/AjaxRequest';
import MapFilter from './maps/FilterMap';
import {Covid19CasesSetting} from './setting/';


$(document).ready(function () {

    // intitalize filter
    let apiCall = new ApiCall();
    let mapFilter = new MapFilter();
    let mapSetting = {
        url: 'load_geojson',
        params: {'geoType':'province', 'dataType':'province', 'indicator':'Cases'},
        mapFilter,
    };

    let urlPostFix = $('#ajaxUrl').val();
    mapSetting.params['source'] = urlPostFix;              // define this in the map setting
    let url = "ajax_"+urlPostFix;
    let params = {'province': $('#map_container_1').data('province')}; // this will get the province id or all
    apiCall.partiallyUpdate(url, Covid19CasesSetting, params, null, {...mapSetting});
    // when map indicator changed
    $('.filter-map').click(function (event) {

        event.preventDefault();
        let dataType = "province";

        mapFilter.createMap(dataType, $(this).data('type'), mapSetting.params.source);
    });
});