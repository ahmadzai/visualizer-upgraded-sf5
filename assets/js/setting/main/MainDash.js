'use strict';

/*
Charts Settings for Main Dashboard
1. Object key will be equal to the JSON key returning by Server
2. Object key will also be equal to the container charts is rendering to
3. ChartType, Color and Other Required Properties of the Chart should be Cleared
4. You can also add 'data' property, however, that will be added/replaced by the actual data
----------------------- MultiAxisesChart ----------------------------------------------
object:        { renderTo, data:[], titles:[], indicators:[], colors:[], chartType:{}}

----------------------- Column, Bar, Stack, Area charts  with default Values ----------
object:        {renderTo, data: [], chartType:{type:'column'},
                  combination: [],
                  colors: Highcharts.getOptions().colors,
                  titles: {xTitle: null, yTitle: null},
                  legend: {enabled:true, position:{vAlign:'bottom', hAlign:'center'}},
                  menu: [{chart:'percent', title:'Percent Stack'},
                      {chart: 'column', title:'Column Chart'}],
                  large:null,
                  yAxisFormatter:""}

---------------------  Pie, Donut Charts with Default Values -------------------------
object:         { renderTo, data : [], legend : false,
                  colors : Highcharts.getOptions().colors,
                  chartType : {type:'pie'}, area : 'large', menu : null }

--------------------- Line, Spline Charts with Default Values -----------------------
Arguments:     { renderTo, data : [], chartType : {type:'line'},
                 colors : Highcharts.getOptions().colors,
                 titles : {xTitle: null, yTitle: null},
                 legend : {enabled:true, position:{vAlign:'bottom', hAlign:'center'}},
                 menu : [{chart:'spline', title:'Spline Chart'},
                     {chart: 'line', title:'Line Chart'}]
                }

--------------------- Table, HTML --------------------------------------------------
Arguments:      { renderTo, data : [], chartType : {type:'table/html'} }

 */
import colors from "./../colors";

const MainChartsSetting = {};

// ------------------- PIE chart One campaign --------------------------------------

// The first row of one campaign
MainChartsSetting['missed_by_reason_pie_1'] = {
    'colors':[colors.REM_ABSENT, colors.REM_NSS, colors.REM_REFUSAL],
    'chartType':{'type':'pie'}, 'legend':false, 'area':'small'
};

MainChartsSetting['total_remaining_1'] = {'chartType':{'type':"column"},
    'colors':[colors.REM_MISSED], 'legend':{'enabled':false}};

MainChartsSetting['total_recovered_remaining_1'] = {'chartType':{'type':"column", 'stacking': 'normal'},
    'colors': [colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5, colors.RECOVERED_CATCHUP, colors.LIME,
        colors.DISCREP, colors.REM_MISSED],
    'legend':{'enabled':true, 'vAlign':'center', 'hAlign': 'left'},
    'menu':[{chart:'normal', title:'Normal Chart'},
        {chart: 'percent', title:'Percent Chart'}],
    'scrollbar': {min:0, max:15}
};

// The first row of one campaign
MainChartsSetting['campaign_title'] = {'chartType':{'type':'html'}};

MainChartsSetting['info_box'] = {'chartType':{'type':'html'}};

MainChartsSetting['info_table'] = {'chartType':{'type':'html'}};

MainChartsSetting['map_data'] = {'chartType': {'type': 'raw'}};


// All Type Missed
MainChartsSetting['recovered_all_type_1'] = {
    'colors': [colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5,
        colors.RECOVERED_CATCHUP, colors.LIME, colors.REM_MISSED, colors.DISCREP],
    'chartType' : {'type': 'halfpie'},
    'legend':true
};
// Absent
MainChartsSetting['recovered_absent_1'] = {
    'colors': [colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5,
        colors.RECOVERED_CATCHUP, colors.REM_ABSENT, colors.DISCREP],
    'chartType' : {'type': 'halfpie'},
    'legend':true
};
// NSS
MainChartsSetting['recovered_nss_1'] = {
    'colors': [colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5,
        colors.RECOVERED_CATCHUP, colors.REM_NSS, colors.DISCREP],
    'chartType' : {'type': 'halfpie'},
    'legend':true
};
// Refusal
MainChartsSetting['recovered_refusal_1'] = {
    'colors': [colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5,
        colors.RECOVERED_CATCHUP, colors.LIME, colors.REM_REFUSAL, colors.DISCREP],
    'chartType' : {'type': 'halfpie'},
    'legend':true
};

// Refusal
MainChartsSetting['campaign_title'] = {
    'chartType' : {'type': 'html'}
};

//------------------ Trends Starts here ----------------------------------------------

// Ten campaign vaccinated children
MainChartsSetting['vac_child_trend'] = {
    'colors':[colors.VACCINATED, colors.VACCINATED_CATCHUP, colors.LIME],
    'chartType':{'type':"column", 'stacking':'normal'}
};
// Ten campaign missed children
MainChartsSetting['missed_child_trend'] = {
    'colors':[colors.REM_AFTER_CAMP, colors.REM_AFTER_CATCHUP],
    'chartType':{'type':"column"}
};
// // Ten campaign missed by type percent chart
// MainChartsSetting['missed_by_type_trend'] = {
//     'colors': ['#B7B3BE', '#FFB32D', '#2DA810', '#45E490', '#048AFF'],
//     'chartType':{'type':"column", 'stacking':'percent'}
// };
// Ten campaign absent children percent chart
MainChartsSetting['missed_recovered_trend'] = {
    // 'colors': [colors.DISCREP, colors.REM_MISSED, colors.RECOVERED_CATCHUP,
    //     colors.RECOVERED_DAY5, colors.RECOVERED_3DAYS],
    'colors': [colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5, colors.RECOVERED_CATCHUP,
        colors.LIME, colors.DISCREP, colors.REM_MISSED],
    'chartType':{'type':"column", 'stacking':'percent'},
    'menu':[{chart:'percent', title:'Percent Chart'},
        {chart: 'normal', title:'Normal Chart'}]
};
// Ten campaign absent children percent chart
MainChartsSetting['absent_recovered_trend'] = {
    // 'colors': [colors.DISCREP, colors.REM_ABSENT, colors.RECOVERED_CATCHUP,
    //     colors.RECOVERED_DAY5, colors.RECOVERED_3DAYS],
    'colors': [colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5, colors.RECOVERED_CATCHUP,
        colors.DISCREP, colors.REM_ABSENT],
    'chartType':{'type':"column", 'stacking':'percent'},
    'menu':[{chart:'percent', title:'Percent Chart'},
        {chart: 'normal', title:'Normal Chart'}]
};
// Ten campaign nss children percent chart
MainChartsSetting['nss_recovered_trend'] = {
    // 'colors': [colors.DISCREP, colors.REM_NSS, colors.RECOVERED_CATCHUP,
    //     colors.RECOVERED_DAY5, colors.RECOVERED_3DAYS],
    'colors': [colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5, colors.RECOVERED_CATCHUP,
        colors.DISCREP, colors.REM_NSS],
    'chartType':{'type':"column", 'stacking':'percent'},
    'menu':[{chart:'percent', title:'Percent Chart'},
        {chart: 'normal', title:'Normal Chart'}]
};
// Ten campaign refusal children percent chart
MainChartsSetting['refusal_recovered_trend'] = {
    // 'colors': [colors.DISCREP, colors.REM_REFUSAL, colors.RECOVERED_CATCHUP,
    //     colors.RECOVERED_DAY5, colors.RECOVERED_3DAYS],
    'colors': [colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5, colors.RECOVERED_CATCHUP,
        colors.LIME, colors.DISCREP, colors.REM_REFUSAL],
    'chartType':{'type':"column", 'stacking':'percent'},
    'menu':[{chart:'percent', title:'Percent Chart'},
        {chart: 'normal', title:'Normal Chart'}]
};
// Ten campaign missed recovery area chart
MainChartsSetting['missed_child_recovery_trend'] = {
    // 'colors': [colors.REM_MISSED, colors.RECOVERED_ABSENT, colors.RECOVERED_NSS, colors.RECOVERED_REFUSAL],
    'colors': [colors.RECOVERED_REFUSAL, colors.RECOVERED_NSS, colors.RECOVERED_ABSENT, colors.REM_MISSED],
    'chartType':{'type':"area", 'stacking':'percent'}
};

export default MainChartsSetting;