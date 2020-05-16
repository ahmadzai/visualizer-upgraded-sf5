'use strict';

import Highcharts from 'highcharts';

import ChartFactory from './ChartFactory';
import TableHtml from './TableHtml';

class Chart {
    // Just to initialize one Factory
    constructor(){
        this.factory = new ChartFactory();
        this.dTitles = {xTitle: null, yTitle: null};
        this.dColors = Highcharts.getOptions().colors;
        this.dLegend = {enabled:true, position:{vAlign:'bottom', hAlign:'center'}};
    }

    /**
     * Switch Function To call Different Functions Based on the Chart Type
     * In this Case ChartType must be defined in the provided object
     * @param args
     */
    visualize(args) {
        let type = args.chartType.type;
        switch (type) {
            case "line":
            case "spline":
                this.lineChart(args);
                break;
            case "bar":
            case "column":
            case "area":
                this.columnBarChart(args);
                break;
            case "pie":
            case "donut":
            case "halfpie":
                this.pieDonutChart(args);
                break;
            case "table":
            case "html":
                TableHtml.tableHtml(args);
                break;
            case "raw":
                TableHtml.rawData(args);
                break;
            case "map":
                this.mapChart(args);
                break;
        }

    }

    multiAxisChart (args = {renderTo, data:[], titles, indicators, colors, chartType}) {
        args.titles = args.hasOwnProperty('titles') ? args.titles :
                                    ['Title1', 'Title2', 'Title3'];
        args.indicators = args.hasOwnProperty('indicators') ? args.indicators :
                                    ['Indicator1', 'Indicator1', 'Indicator1'];
        args.colors = args.hasOwnProperty('colors') ? args.colors :
                                    ['#048aff', '#43AB0D', '#F00000'];
        args.chartType = args.hasOwnProperty('chartType') ? args.chartType :
                                    {'type':'multi'};

        args['legend'] = {
                layout: 'vertical',
                align: 'left',
                vAlign: 'top',
                x: 80,
                y: 55,
                color: '#FFFFFF'
            };

        args['axises'] = [
                {
                    format:'',
                    color: args.colors[1],
                    opposite: true,
                    title: args.titles[1]

                },
                {
                    format:'',
                    color: colors[0],
                    opposite: false,
                    title:args.args.titles[0],
                    lineWidth: 1
                },
                {
                    format:' %',
                    color: args.colors[2],
                    opposite: true,
                    title:args.titles[2],
                    lineWidth: 0
                },
            ];

        args['yAxises'] = [
                {
                    color: args.colors[0],
                    indicator:args.indicators[0],
                    type: 'column',
                    tooltip: '',
                    yAxis: 1
                },
                {
                    color: args.colors[1],
                    yAxis: 0,
                    indicator:args.indicators[1],
                    type: 'spline',
                    tooltip: ''

                },
                {
                    color: args.colors[2],
                    indicator:args.indicators[2],
                    yAxis: 2,
                    type: 'spline',
                    tooltip: ' %'
                },


            ];

        this.factory.createChart(args);
    }

    /**
     * @param args
     */
    columnBarChart(args = { renderTo, data : [], chartType, combination, colors,
                   titles, legend, menu , large, yAxisFormatter, scrollbar})
    {
        args.chartType = args.hasOwnProperty('chartType') ? args.chartType : {type:'column'};
        args.combination = args.hasOwnProperty('combination') ? args.combination : [];
        args.colors = args.hasOwnProperty('colors') ? args.colors : this.dColors;
        args.titles = args.hasOwnProperty('titles') ? args.titles : this.dTitles;
        args.legend = args.hasOwnProperty('legend') ? args.legend : this.dLegend;
        args.yAxisFormatter = args.hasOwnProperty('yAxisFormatter') ? args.yAxisFormatter : "";
        args.scrollbar = args.hasOwnProperty('scrollbar') ? args.scrollbar : false;

        this.factory.createChart(args);

    }

    /**
     *
     * @param args
     */
    lineChart(args = {renderTo, data:[], chartType, colors, titles, legend, menu})
    {
        args.chartType = args.hasOwnProperty('chartType') ? args.chartType: {type:'line'};
        args.colors = args.hasOwnProperty('colors') ? args.colors : this.dColors;
        args.titles = args.hasOwnProperty('titles') ? args.titles : this.dTitles;
        args.legend = args.hasOwnProperty('legend') ? args.legend : this.dLegend;
        args.menu = args.hasOwnProperty('chartType') ? args.menu :
                               [{chart:'spline', title:'Spline Chart'},
                                {chart: 'line', title:'Line Chart'}];

        this.factory.createChart(args);

    }

    /**
     * @param args
     */
    pieDonutChart(args = {renderTo, data:[], legend, colors, chartType, area, menu}) {
        args.colors = args.hasOwnProperty('colors') ? args.colors : this.dColors;
        args.legend = args.hasOwnProperty('legend') ? args.legend : false;
        args.area = args.hasOwnProperty('area') ? args.area : 'large';
        args.chartType = args.hasOwnProperty('chartType') ? args.chartType : {type:'pie'};

        this.factory.createChart(args);

    }

    areaChart() {
        //Todo: call columnBarChart() instead with chartType{type:'area'}
        alert('This function is not implemented yet\n' +
        'call columnBarChart() instead with chartType{type:\'area\'}');

    }
    heatmapChart() {
        //Todo: implement heat map
        alert('This function is not implemented yet\n');
    }

    mapChart(args) {
        //console.log(args);
        this.factory.createChart(args);
    }
}

export default Chart;