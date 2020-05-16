'use strict';
import $ from "jquery";

import Highcharts from 'highcharts/highstock';
import * as Exporting from 'highcharts/modules/exporting';
import * as OfflineExport from 'highcharts/modules/offline-exporting';
import * as ExportCSV from 'highcharts/modules/export-data';

import * as Categories from 'highcharts-grouped-categories/grouped-categories';
Categories(Highcharts);
Exporting(Highcharts);
OfflineExport(Highcharts);
ExportCSV(Highcharts);

import ChartOptions from './ChartHelper';

class ChartFactory {

    /**
     * Public funcation to create any type of chart
     * @param settings
     */
    createChart(settings)  {

        let type = settings.chartType.type;
        // create the general options
        let options = new ChartOptions();
        // Common properties sets here
        options.chart.renderTo = settings.renderTo;
        options.chart.type = settings.chartType.type;
        options.colors = settings.colors;
        options.credits = {enabled:false}; // disable the credits link
        // Add Legend To Chartf
        if(settings.legend !== false ||
            ['pie', 'donut', 'halfpie'].indexOf(type) !== -1) {
            options['legend'] = {
                enabled: settings.legend.hasOwnProperty('enabled') ? settings.legend.enabled : false,
                align: settings.legend.hasOwnProperty('hAlign') ? settings.legend.hAlign : 'center',
                verticalAlign: settings.legend.hasOwnProperty('vAlign') ? settings.legend.vAlign : 'bottom',
                layout:settings.legend.hasOwnProperty('layout') ? settings.legend.layout : 'horizontal',
                itemStyle: {
                    fontWeight: 'normal',
                    fontSize: '100%'
                }
            };
        }
        // Add Menu Items If It was set
        if(settings.menu!== undefined) {
            let menu = settings.menu;
            for(let i = 0; i < menu.length; i++) {
                let menuItem = this._myMenuItems('', menu[i].chart, settings.renderTo, menu[i].title);
                options.exporting.buttons.contextButton.menuItems.push(menuItem);
            }
        }
        // Add Label Toggle Menu
        let toggleMenu = this._dataLabelToggleMenu(settings.renderTo);
        options.exporting.buttons.contextButton.menuItems.push(toggleMenu);

        let data = settings.data;//JSON.parse(settings.data);
        options['title'] = {text: data.title, style: {fontSize:'100%'}};
        options['subtitle'] = {text: data.subTitle, verticalAlign:'bottom', style: {fontSize:'80%'}};
        // Set the data in the setting again
        settings.data = data;

        switch (type) {
            case "bar":
            case "column":
            case "area":
                options = this._createColumnChart(options, settings);
                break;
            case "pie":
            case "donut":
            case "halfpie":
                options = this._createPieChart(options, settings);
                break;
            case "line":
                options = this._createLineChart(options, settings);
                break;
            case "heatmap":
                options = this._createHeatMap(options, settings);
                break;
            case "multi":
                options = this._createMultiAxisChart(options, settings);
                break;

        }
        // create the chart
        Highcharts.chart(options);
    }
    //private function to create pie chart
    _createPieChart(options, settings) {
        // set the type of chart, static in this case
        let chart =  {
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie',
            renderTo: settings.renderTo,
        };
        options.chart = chart;

        let plot = {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: settings.legend !== true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        fontWeight: 'normal',
                        fontSize: '80%',
                        textOutline: '0.4px contrast',
                    },
                    color: 'black',
                },
                showInLegend: settings.legend,
            }
        };
        let type = settings.chartType.type;
        // if type was halfpie
        if(type === 'halfpie') {
            plot.pie.startAngle = -90;
            plot.pie.endAngle = 90;
            plot.pie.center = ['50%', '75%'];
            plot.pie.dataLabels.distance = -30;
            plot.pie.dataLabels.format = '<b>{point.percentage:.1f}%</b>';
            options.chart.margin = -16; // just to make them a little bigger inside div
            if(settings.legend) {
                //options.chart.marginRight = 60;
                options.legend = {
                    itemDistance: 8,
                    padding: 0,
                    margin:0,
                    // align: 'right',
                    // verticalAlign: 'top',
                    // layout: 'vertical',
                    // y: 50,
                    itemStyle: {
                        fontWeight: 'normal',
                        fontSize: '90%',

                    }
                }
            }
        } else if(type === 'donut') {
            plot.pie.innerSize = 100;
            plot.pie.depth = 45;

        }
        // if area was small
        if(settings.area === 'small') {
            plot.pie.dataLabels.distance = -20;
            plot.pie.dataLabels.format = '<b>{point.name}</b>';
            options.chart.margin = 0;
            options.chart.marginTop = 15;
        }

        options['plotOptions'] = plot;

        options['tooltip'] = {
            pointFormat: '{point.y}<b> ({point.percentage:.1f}%)</b>'
        };

        let series = settings.data.series === null || settings.data.series === undefined ? [] :
            settings.data.series;

        // set the data/series

        if(series.length > 0) {

            if (type === 'halfpie')
                series[0].innerSize = '50%';
        }
        options.series = series;

        return options;

    }

    //private function to create multi-axises chart
    _createMultiAxisChart(options, settings) {


        options.chart['zoomType'] = 'xy';
        // the tooltip are shared for now
        options['tooltip'] = {shared:true};

        // check for the legend and replace it
        /*
         legend = {
         layout: 'text',
         align: 'text',
         vAlign: 'text',
         x: int,
         y: int,
         color: 'color'
         }
         */
        if(settings.hasOwnProperty('legend')) {
            //console.log("we are here in the legend");
            options['legend'] = {
                layout:settings.legend.layout,
                align:settings.legend.align,
                verticalAlign:settings.legend.vAlign,
                x:settings.legend.x,
                y:settings.legend.y,
                floating: true,
                backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'

            }
        }

        // check for the yAxises, should be set as plural
        /*
         yAxises = {
         format:'text',
         color:'color',
         opposite:'boolean',
         title:'text',
         indicator:'text',
         yAxis:number (1, 2),
         type: 'text',
         tooltip: 'suffix',
         marker: 'boolean'

         }
         */
        if(settings.hasOwnProperty('axises')) {
            let axises = settings.axises;
            let axis = [];
            for(let i = 0; i<axises.length; i++) {
                let tmpAxis = {
                    labels: {
                        format: '{value}'+axises[i].format,
                        style: {color:axises[i].color}
                    },
                    title: {
                        text:axises[i].title,
                        style:{color:axises[i].color}
                    },
                    opposite:axises[i].opposite,
                    gridLineWidth: axises[i].lineWidth,

                }
                axis.push(tmpAxis);
            }
            options['yAxis'] = axis;
        }

        // set the data of the chart to what assigned from TWIG
        let dataObj = settings.data;
        // set the dynamic categories
        options.xAxis.categories = dataObj.categories;
        // set the data/series
        let dataSeries = dataObj.series;
        //console.log(dataSeries);
        //console.log('We are here above the if');
        if (settings.hasOwnProperty('yAxises')) {
            //console.log('We are here in the right location');
            let newSeries = settings.yAxises;

            let series = [];
            for (let i = 0; i < newSeries.length; i++) {

                for (let x = 0; x < dataSeries.length; x++) {
                    if (dataSeries[x].name.toLowerCase() == newSeries[i].indicator.toLowerCase()) {
                        let tempSeries = {};
                        tempSeries['name'] = dataSeries[x].name;
                        tempSeries['type'] = newSeries[i].type;
                        tempSeries['data'] = dataSeries[x].data;
                        tempSeries['yAxis'] = newSeries[i].yAxis;
                        tempSeries['tooltip'] = {valueSuffix: newSeries[i].tooltip}
                        tempSeries['color'] = newSeries[i].color;
                        series.push(tempSeries);
                    }
                }

            }
            options.series = series;
        }

        return options;
    }

    _createAreaChart(options, settings) {

    }

    /**
     * @param options
     * @param settings
     * @returns {*}
     * @private
     */
    _createHeatMap(options, settings) {

        let dataObj = settings.data;
        let chart = {
            type: 'heatmap',
            marginBottom: 80,
            plotBorderWidth: 1,
            renderTo: settings.renderTo,
            marginTop: 80,
        };

        options.chart = chart;
        options.title.style.fontSize = '110%';

        let xAxis = (dataObj.xAxis === undefined || dataObj.xAxis === null) ? [] : dataObj.xAxis;
        options.xAxis = [
            {
                categories: xAxis,

                labels: {
                    style: {
                        fontSize:'80%'
                    },
                    //autoRotation: [-30],
                    autoRotation: false
                }
            },
            {
                linkedTo: 0,
                opposite: true,
                categories: xAxis,
                labels: {
                    style: {
                        fontSize:'80%'
                    },
                    //autoRotation: [30],
                    autoRotation: false
                }
            }
        ];

        options.yAxis = {
            categories: (dataObj.yAxis === undefined || dataObj.yAxis === null) ? [] : dataObj.yAxis,
            title: null,
            labels: {
                style: {
                    fontSize:'80%'
                }
            }
        };
        options.labels = {
            style: {
                fontSize: '80%'
            }
        };

        options.colorAxis = {
            min: (dataObj.stops === undefined || dataObj.stops === null) ? 5: dataObj.stops.minValue,
            max: (dataObj.stops === undefined || dataObj.stops === null) ? 20: dataObj.stops.maxValue,
            tickInterval: 1,
            startOnTick: false,
            endOnTick: false,
            stops: [
                [
                    0,
                    (dataObj.stops === undefined || dataObj.stops === null) ? "#43AB0D":
                        dataObj.stops.minColor
                ],
                [
                    (dataObj.stops === undefined || dataObj.stops === null) ? 0.5:
                        dataObj.stops.midStop,
                    (dataObj.stops === undefined || dataObj.stops === null) ? "#ffd927":
                        dataObj.stops.midColor
                ],
                [
                    1,
                    (dataObj.stops === undefined || dataObj.stops === null) ? "#FF0000":
                        dataObj.stops.maxColor
                ]
            ]
        };

        options.legend.enabled = false;

        options.tooltip = {
            formatter: function () {
                return '<b>' + this.series.xAxis.categories[this.point.x] +
                    '</b> <br>'+ tooltipTitle +' children <br><b>' +
                    this.point.value + '</b> in cluster <br><b>' +
                    this.series.yAxis.categories[this.point.y] + '</b>';
            }
        };
        //console.log(dataObj.data);
        options.series = [{
            name: dataObj.title,
            borderWidth: 1,
            turboThreshold: Number.MAX_VALUE,
            data: dataObj.data,
            dataLabels: {
                enabled: true,
                color: '#000000'
            }
        }];
        // Setting width and height of the container according to rows and columns
        let height = dataObj.yAxis === undefined ? '29px' : dataObj.yAxis.length*29+"px";
        let width = dataObj.xAxis === undefined ? '50px' : dataObj.xAxis.length*50+"px";
        $('#'+settings.renderTo).css({"height": height, "min-width": width});

        return options;
    }

    /**
     * @param options
     * @param settings
     * @returns {*}
     * @private
     */
    _createLineChart(options, settings) {

        let plot = {};
        plot[settings.chartType.type === 'line' ? 'series' : 'spline'] = {
            label: {
                connectorAllowed: false
            }
        };

        options['plotOptions'] = plot;
        // set the yAxis title
        options.yAxis.title.text = settings.titles.yTitle;
        let data = settings.data;
        // checking for undefined categories and series
        let categories = data.categories === null || data.categories === undefined ? [] : data.categories;
        let series = data.series === null || data.series === undefined ? [] : data.series;
        options.xAxis.categories = categories;
        options.series = series;

        return options;

    }

    /**
     * @param options
     * @param settings
     * @returns {*}
     * @private
     */
    _createColumnChart(options, settings) {

        let chartType = settings.chartType.type;

        if(chartType === 'area' && settings.chartType.hasOwnProperty('stacking') &&
            settings.chartType.stacking === 'percent') {
            options['tooltip'] = {
                pointFormat: '<span style="color:{series.color}">{series.name}</span>: ' +
                    '<b>{point.percentage:.1f}%</b> ({point.y:,.0f})<br/>',
                split: true
            }
        } else if(chartType === 'column' && settings.chartType.hasOwnProperty('stacking') &&
            settings.chartType.stacking === 'percent') {
            options['tooltip'] = {
                pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: ' +
                    '<b>{point.y} ({point.percentage:.1f}%)</b><br/>',
            }
        } else if(chartType === 'bar' && settings.chartType.hasOwnProperty('stacking') &&
            settings.chartType.stacking === 'percent') {
            options['tooltip'] = {
                pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: ' +
                    '<b>{point.y} ({point.percentage:.1f}%)</b><br/>',
            }
        }
        // set the plat option, empty, normal, percent
        let plot = {};
        plot[chartType] = {
            dataLabels: {
                style: {
                    fontSize: '70%',
                }
            }
        };
        if(settings.chartType.hasOwnProperty('stacking')) {
            plot[chartType] = {
                stacking:settings.chartType.stacking,
                dataLabels: {
                    style: {
                        fontSize: '70%',
                    }
                }
            }
        }

        options['plotOptions'] = plot;
        // set the yAxis title
        options.yAxis.title.text = settings.titles.yTitle;
        // Label, not clear yet
        if(settings.hasOwnProperty('label')) {
            options['labels'] = {
                items: [{
                    html: settings.label.title,
                    style: {
                        left: settings.label.position.left,
                        top: settings.label.position.top,
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                    }
                }]
            }
        }
        // setting dynamic categories
        let data = settings.data;
        // checking for undefined categories and series
        let categories = data.categories === null || data.categories === undefined ? [] : data.categories;
        //console.log(categories);
        let series = data.series === null || data.series === undefined ? [] : data.series;
        options.xAxis.categories = categories;
        // set scrollbar here
        if(settings.scrollbar !== false) {
            options.xAxis.min = settings.scrollbar.min;
            let countCat = this._countCategories(categories);
            let max = settings.scrollbar.max;
            options.xAxis.max =  countCat <= max ? countCat-1 : max;
            options.xAxis.scrollbar = {enabled:  max < countCat };
        }
        options.series = series;
        // change the formatter for yAxis if not empty
        if(settings.yAxisFormatter !== "")
            options.yAxis.labels = {format: '{value}'+settings.yAxisFormatter};

        // change the height of the renderTo (dynamic height)
        let height = categories.length === 0 ? 10: categories.length;
        if(settings.large !== null && settings.large === 'height')
        //console.log(dataObj.categories);
            $('#'+settings.renderTo).css("height", height*30+"px");

        // check if the requested chart was a combined chart
        if (settings.hasOwnProperty('combination')) {
            let secondCharts = settings.combination;
            for (let i = 0; i < secondCharts.length; i++) {
                let colors = options.colors;
                if (secondCharts[i].hasOwnProperty('colors')) {
                    colors = secondCharts[i].colors;
                }
                let newSettings = secondChart(dataObj, secondCharts[i], colors);
                options.series.push(newSettings);
            }
        }

        return options;

    }


    /**
     * @param sType
     * @param tType
     * @param renderTo
     * @param title
     * @returns {{text: *, onclick: onclick, separator: boolean}}
     * @private
     */
    _myMenuItems(sType, tType, renderTo, title) {
        let self = this;
        let menuItem = {
            text: title,
            onclick: function () {
                self._changeChartType(sType, tType, renderTo);
            },
            separator: false
        }
        return menuItem;
    }

    /**
     * @param renderTo
     * @returns {{text: string, onclick: onclick, separator: boolean}}
     * @private
     */
    _dataLabelToggleMenu(renderTo) {
        let self = this;
        let menuItemLabel = {
            text: 'Toggle Labels',
            onclick: function () {
                self._toggleLabels(renderTo);
            },
            separator: false
        };
        return menuItemLabel;
    }

    /**
     * @param sType
     * @param type
     * @param renderTo
     * @private
     */
    _changeChartType(sType, type, renderTo) {
        let chart = Highcharts.charts[document.querySelector('#'+renderTo).
        getAttribute('data-highcharts-chart')],
            s = chart.series,
            sLen = s.length;
        let inverted = false;
        let polar = false;
        if(type === 'pie' || type === 'halfpie' || type === 'donut') {
            //console.log(chart);
            //chart.userOptions
        } else if(type === 'percent' || type === 'normal') {
            for(let i =0; i < sLen; i++){

                s[i].update({
                    type: 'column',
                    stacking: type,
                }, false);
            }
        } else if(type === 'bar') {
            inverted = true;
        } else if(type === 'percent_area') {
            for (let i = 0; i < sLen; i++) {

                s[i].update({
                    type: 'area',
                    stacking: 'percent'
                }, false);
            }
        } else {
            for (let i = 0; i < sLen; i++) {

                s[i].update({
                    type: type,
                    stacking:null
                }, false);
            }
        }

        chart.update({
            chart: {
                inverted: inverted,
                polar: polar
            },
        });

        chart.redraw();
    }

    /**
     * @param renderTo
     * @private
     */
    _toggleLabels(renderTo) {
        let chart = Highcharts.charts[document.querySelector('#'+renderTo).
            getAttribute('data-highcharts-chart')],
        //let chart = chart,
            s = chart.series,
            sLen = s.length;
        //console.log("hello something" + s[0].dataLabels.enabled);
        for(let i =0; i < sLen; i++){
            s[i].update({
                dataLabels: {
                    enabled: !s[i].options.dataLabels.enabled,
                    style: {
                        fontWeight: 'normal',
                        fontSize: '80%',
                        textOutline: '0.4px contrast',
                    },
                    color: 'black'
                }
            }, false);
        }
        chart.redraw();

    }

    /**
     * @param sData
     * @param sOptions
     * @param sColors
     * @returns {*}
     * @private
     */
    _secondChart(sData, sOptions, sColors) {
        let data = sData;
        let mData = data.series;
        if(sOptions.type === 'pie' && sOptions.method === 'sum') {
            let tempData = [];
            for(let i = 0; i < mData.length; i++) {
                let name = mData[i].name;
                data = mData[i].data.reduce(function(prev, cur) {
                    return prev + cur;
                });
                let tmpObj = {name: name, y:data, color: sColors[i]};
                //tmpObj[dataName] = dataData;
                tempData.push(tmpObj);
            }
            // return pie chart
            return {
                type: 'pie',
                name: 'Total',
                data: tempData,
                center: [90, 10],
                size: 100,
                showInLegend: false,
                dataLabels: {
                    enabled: false
                }

            };

        } else if(sOptions.type ==='spline') {
            let newData = [];
            let arrLength = mData[0].data.length;
            if(sOptions.method === 'average') {
                //newData = null;
                let avg = 0;
                for (let i = 0; i < arrLength; i++) {
                    avg = 0;
                    for (let x = 0; x < mData.length; x++) {
                        let tmp = mData[x].data;
                        //console.log(tmp);
                        avg = avg + tmp[i];
                    }
                    newData.push(Math.round(avg / arrLength));
                }
            } else if(sOptions.method === 'sum') {
                //newData = null;
                let sum = 0;
                for (let i = 0; i < arrLength; i++) {
                    sum = 0;
                    for (let x = 0; x < mData.length; x++) {
                        let tmp = mData[x].data;
                        //console.log(tmp);
                        sum = sum + tmp[i];
                    }
                    newData.push(sum);
                }
            }
            // return line chart
            return {
                type: 'spline',
                name: sOptions.method === 'sum'? 'Total': 'Average',
                data: newData,
                marker: {
                    lineWidth: 2,
                    lineColor: Highcharts.getOptions().colors[3],
                    fillColor: '#ffffff'
                },
                color:Highcharts.getOptions().colors[3]
            }
        }
    }

    _countCategories(category) {
        let count = 0;
        if(category.length > 0) {
            for(let i = 0; i<category.length; i++) {
                let typeOf = typeof category[i];
                if(typeOf === "object") {
                    count += category[i]['categories'].length;
                } else
                    count ++;
            }
        }

        return count;
    }
}

export default ChartFactory;