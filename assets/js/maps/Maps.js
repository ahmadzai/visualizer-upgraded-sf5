'use strict';

import HighMaps from 'highcharts/highmaps';
import * as Exporting from 'highcharts/modules/exporting';
import * as OfflineExport from 'highcharts/modules/offline-exporting';
Exporting(HighMaps);
OfflineExport(HighMaps);

class Maps {

    /**
     * @param settings = {
     * data:{geoJson, data, joinBy, classes, keys, name, dataLabel},
     * title, colors, legend:{title}, renderTo
     * }
     */
    createMap = (settings) => {

        let newOptions = {

            chart: {
                map: settings.data.geoJson
            },

            // context menu
            lang: {
                printChart: 'Print Map',
                downloadPNG: 'Export to PNG',
                downloadPDF: 'Export to PDF',

            },

            exporting : {
                sourceWidth: 800,
                sourceHeight: 500,
                buttons: {
                    contextButton: {
                        menuItems: [
                            'printChart',
                            'downloadPNG',
                            'downloadPDF'
                        ]
                    }
                }
            },

            credits: {enabled: false}, // remove the url of the highcharts

            colors: settings.colors,

            title: {
                text: settings.title,
                style: {fontSize: '100%'},
            },

            mapNavigation: {
                enabled: true,
                buttonOptions: {
                    verticalAlign: 'bottom'
                },
            },

            legend: {
                title: {
                    text: settings.legend.title
                },
                align: 'right',
                verticalAlign: 'bottom',
                floating: true,
                labelFormatter: function () {
                    return (this.from || '<') + ' - ' + (this.to || '>');
                },
                layout: 'vertical',
                valueDecimals: 0,
                backgroundColor: 'transparent',
                padding: 12,
                itemMarginTop: 0,
                itemMarginBottom: 0,
                symbolRadius: 0,
                symbolHeight: 14,
                symbolWidth: 24,
                itemStyle: {
                    fontWeight: 'normal',
                    fontSize: '100%'
                }
            },

            // colorAxis: {
            //     tickPixelInterval: 100
            // },

            colorAxis: {
                dataClassColor: 'category',
                dataClasses: settings.data.classes,

            },

            series: [{
                data: settings.data.data,
                keys: settings.data.keys,//['pcode', 'value'],
                joinBy: settings.data.joinBy, //'pcode',
                name: settings.data.name, //'Random data',
                states: {
                    hover: {
                        color: '#d6edea'
                    }
                },
                dataLabels: {
                    enabled: false,
                    format: settings.data.dataLabel, //'{point.properties.name}'
                    style: {
                        fontWeight: 'normal',
                        fontSize: '90%',
                        textOutline: '0.4px contrast',
                    },
                    color: 'black'
                }
            }]
        };

        // Add Label Toggle Menu
        let toggleMenu = this._dataLabelToggleMenu(settings.renderTo);
        newOptions.exporting.buttons.contextButton.menuItems.push(toggleMenu);
        HighMaps.mapChart(settings.renderTo, newOptions);

    };

    /**
     * @param renderTo
     * @returns {{text: string, onclick: onclick, separator: boolean}}
     * @private
     */
    _dataLabelToggleMenu = (renderTo) => {
        let self = this;
        return {
            text: 'Toggle Labels',
            onclick: function () {
                self._toggleLabels(renderTo);
            },
            separator: false
        };
    };

    /**
     * @param renderTo
     * @private
     */
    _toggleLabels = (renderTo) => {
        let chart = HighMaps.charts[document.querySelector('#'+renderTo).
            getAttribute('data-highcharts-chart')],
            //let chart = chart,
            s = chart.series,
            sLen = s.length;
        //console.log("hello something" + s[0].dataLabels.enabled);
        for(let i =0; i < sLen; i++){
            s[i].update({
                dataLabels: {
                    enabled: !s[i].options.dataLabels.enabled
                }
            }, false);
        }
        chart.redraw();

    }


}

export default Maps;