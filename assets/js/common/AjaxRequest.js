import $ from 'jquery';
import Routing from './Routing';
import Chart from '../charts/Chart';

class AjaxRequest {

    constructor() {
        this.chart = new Chart();
    }

    partiallyUpdate = (url, charts, params,
        loaderClass = '_', mapSetting=false) => {
        let self = this;
        $('.'+loaderClass).show();

        let ajax = this._ajaxPromise(url, params);

        ajax.done(data => {

            self._populateDashboard(charts, data);

            if(mapSetting !== false) {
                $('.map-loader').show();
                self.updateMap(mapSetting);
            }

            $('.'+loaderClass).hide();
        }).fail(xhr => {
            console.error(xhr);
        });

    };

    updateAll = (url, container, param1,
                 param2, mapSetting = false) => {
        if(mapSetting === false) {
            this._chainTwoAjax(
                {url, params:param2, container},
                {url, params:param1, container}
                );

        } else {
            this._chainThreeAjax(
                {url, params:param2, container},
                {url, params:param1, container},
                mapSetting
            );
        }
    };

    updateMap = (mapSetting) => {

        let mapFilter = mapSetting.mapFilter,
            params = mapSetting.params,
            url = mapSetting.url,
            loaderClass = mapSetting.loaderClass === undefined ?
                'map-loader' : mapSetting.loaderClass;

        $('.' + loaderClass).show();

        // first update the map data
        this._updateMapData(mapFilter);
        //console.log(params);

        //console.log(mapFilter.getGeoData(params.geoType));
        //console.log(mapFilter);
        if(mapFilter.getGeoData(params.geoType) === undefined) {

            let ajax = this._ajaxPromise(url, params);

            ajax.done(data => {
                mapFilter.setGeoData(data, params.geoType);
                $('.' + loaderClass).hide();
                mapFilter.createMap(params.dataType, params.indicator, params.source);
            }).fail(xhr => {
                console.error(xhr);
            });
        } else {
            mapFilter.createMap(params.dataType, params.indicator, params.source);
            $('.' + loaderClass).hide();
        }


    };

    /**
     * @param req1
     * @param req2
     * @private
     */
    _chainTwoAjax = (req1 = {url, params, container},
                     req2 = {url, params, container}) => {
        $('.trend-loader, .info-loader, .loader').show();
        let self = this;
        let ajx1 = this._ajaxPromise(req1.url, req1.params);
        let ajx2 = ajx1.then(function (data) {
            self._populateDashboard({...req1.container}, data);
            $('.info-loader').hide();
            return self._ajaxPromise(req2.url, req2.params);
        });

        ajx2.done(function (data) {
            self._populateDashboard({...req2.container}, data);
            $('.trend-loader, .loader').hide();
        });
    };

    _chainThreeAjax = (req1, req2, req3) => {
        let self = this;

        $('.trend-loader, .info-loader, .loader').show();
        let ajx1 = this._ajaxPromise(req1.url, req1.params);
        //console.log(ajx1);
        let ajx2 = ajx1.then(function (ajx1Data) {
           self._populateDashboard({...req1.container}, ajx1Data);
           $('.info-loader').hide();
           return self._ajaxPromise(req2.url, req2.params);
        });

        ajx2.done(function (ajx2Data) {
            self._populateDashboard({...req2.container}, ajx2Data);
            $('.trend-loader, .loader').hide();
            self.updateMap(req3);
        });

    };

    /**
     * @param charts
     * @param data
     * @private
     */
    _populateDashboard = (charts, data) => {

        //console.log(data);
        let self = this;
        $.each(charts, function (index, value) {

            if(data.hasOwnProperty(index)) {
                this.data = data[index];
                this.renderTo = index;
                self.chart.visualize(this);
            }
        });
    };


    /**
     * @param url
     * @param params
     * @returns {*|{readyState, getResponseHeader,
     * getAllResponseHeaders, setRequestHeader,
     * overrideMimeType, statusCode, abort}|boolean}
     * @private
     */
    _ajaxPromise = (url, params) => {
        return $.ajax({
            url: Routing.generate(url),
            data: params,
            method: 'post'
        });
    };

    /**
     * @param mapFilter
     * @private
     */
    _updateMapData = (mapFilter) => {
        // if mapDataType was set
        if(mapFilter !== false) {
            mapFilter.setMapData($('#map_data').val());
        }
    };


}

export default AjaxRequest;