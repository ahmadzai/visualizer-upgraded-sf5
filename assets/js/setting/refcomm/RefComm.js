'use strict';

import colors from './../colors';

const RefCommChartsSetting = {};


// The first row of one campaign
RefCommChartsSetting['refusal_recovery_pie_1'] = {
    'colors':[colors.BLUE, colors.GREEN, colors.LIME, colors.REM_REFUSAL],
    'chartType':{'type':'halfpie'}, 'legend':true
};


RefCommChartsSetting['total_remaining_refusal_1'] = {'chartType':{'type':"column"},
    'colors':[colors.REM_REFUSAL], 'legend':{'enabled':false}};
/*
RefCommChartsSetting['total_recovered_remaining_1'] = {'chartType':{'type':"column", 'stacking': 'normal'},
    'colors':[colors.RECOVERED_CATCHUP, colors.REM_MISSED],
    'legend':{'enabled':true, 'vAlign':'center', 'hAlign': 'left'},
    'menu':[{chart:'normal', title:'Normal Chart'},
        {chart: 'percent', title:'Percent Chart'}],
    'scrollbar': {min:0, max:15}
};

 */

// The first row of one campaign
RefCommChartsSetting['campaign_title'] = {'chartType':{'type':'html'}};

RefCommChartsSetting['info_box'] = {'chartType':{'type':'html'}};

RefCommChartsSetting['info_table'] = {'chartType':{'type':'html'}};


//RefCommChartsSetting['map_data'] = {'chartType': {'type': 'raw'}};

// Pie charts Row
RefCommChartsSetting['refusal_recovered_1'] = {'colors':[colors.RECOVERED_CATCHUP, colors.REM_REFUSAL],
    'chartType':{'type':'halfpie'}, 'legend':true
};
RefCommChartsSetting['refusal_recovered_general_1'] = {
    'colors':[colors.BLUE, colors.RECOVERED_CATCHUP, colors.LIME, colors.REM_REFUSAL],
    'chartType':{'type':'halfpie'}, 'legend':true
};
RefCommChartsSetting['refusal_recovered_detail_1'] = {
    'colors':[colors.BLUE, colors.RECOVERED_CATCHUP, colors.CRC, colors.RC, colors.CIP, colors.SENIOR, colors.REM_REFUSAL],
    'chartType':{'type':'halfpie'}, 'legend':true
};
/*
RefCommChartsSetting['recovered_refusal_1'] = {'colors':[colors.RECOVERED_CATCHUP, colors.REM_REFUSAL],
    'chartType':{'type':'halfpie'}, 'legend':true
};

*/
// 3 (default) campaigns location trends
// 10 Campaign absent percent stack chartType
RefCommChartsSetting['loc_trend_general'] = {
    // 'colors': [colors.REM_MISSED, colors.RECOVERED_CATCHUP],
    'colors': [colors.BLUE, colors.RECOVERED_CATCHUP, colors.LIME, colors.REM_REFUSAL],
    'chartType':{'type':"column", 'stacking':'normal'},
    'legend':{'enabled':true},
    'menu':[{chart:'normal', title:'Normal Chart'},
        {chart: 'percent', title:'Percent Chart'}],
    'scrollbar': {min:0, max:18}
};
// absent loc trends
RefCommChartsSetting['loc_trend_detail'] = {
    // 'colors': [colors.REM_ABSENT, colors.RECOVERED_CATCHUP],
    'colors': [colors.RECOVERED_CATCHUP, colors.CRC, colors.RC, colors.CIP, colors.SENIOR, colors.REM_REFUSAL],
    'chartType':{'type':"column", 'stacking':'normal'},
    'legend':{'enabled':true},
    'menu':[{chart:'normal', title:'Normal Chart'},
        {chart: 'percent', title:'Percent Chart'}],
    'scrollbar': {min:0, max:18}
};

/*
// nss loc trends
RefCommChartsSetting['loc_trend_nss'] = {
    // 'colors': [colors.REM_NSS, colors.RECOVERED_CATCHUP],
    'colors': [colors.RECOVERED_CATCHUP, colors.REM_NSS],
    'chartType':{'type':"column", 'stacking':'normal'},
    'legend':{'enabled':true, 'vAlign':'center', 'hAlign': 'left'},
    'menu':[{chart:'normal', title:'Normal Chart'},
        {chart: 'percent', title:'Percent Chart'}],
    'scrollbar': {min:0, max:18}
};
// refusal loc trends
RefCommChartsSetting['loc_trend_refusal'] = {
    // 'colors': [colors.REM_REFUSAL, colors.RECOVERED_CATCHUP],
    'colors': [colors.RECOVERED_CATCHUP, colors.REM_REFUSAL],
    'chartType':{'type':"column", 'stacking':'normal'},
    'legend':{'enabled':true, 'vAlign':'center', 'hAlign': 'left'},
    'menu':[{chart:'normal', title:'Normal Chart'},
        {chart: 'percent', title:'Percent Chart'}],
    'scrollbar': {min:0, max:18}
};

*/

// 10 campaign vaccinated children column chartType
RefCommChartsSetting['general_refusal_recovery_trend'] = {
    'chartType':{'type':"column", 'stacking':'normal'},
    'colors':[colors.BLUE, colors.RECOVERED_CATCHUP, colors.LIME, colors.REM_REFUSAL]};


// 10 campaign missed children column chartType
// RefCommChartsSetting['missed_child_trend'] = {'chartType':{'type':"column"}, 'colors':[colors.REM_MISSED]};
// 10 campaign missed by type stack column chartType
RefCommChartsSetting['detail_refusal_recovery_trend'] = {
    'chartType':{'type':"column", 'stacking':'normal'},
    'colors':[colors.BLUE, colors.RECOVERED_CATCHUP, colors.CRC, colors.RC, colors.CIP, colors.SENIOR, colors.REM_REFUSAL],
    'menu':[{chart:'percent', title:'Percent Chart'},
            {chart: 'normal', title:'Normal Chart'}]
};

/*
// 10 Campaign absent percent stack chartType
RefCommChartsSetting['absent_recovered_trend'] = {
        // 'colors': [colors.REM_ABSENT, colors.RECOVERED_CATCHUP],
        'colors': [colors.RECOVERED_CATCHUP, colors.REM_ABSENT],
        'chartType':{'type':"column", 'stacking':'percent'},
        'menu':[{chart:'percent', title:'Percent Chart'},
            {chart: 'normal', title:'Normal Chart'}]
};
// 10 Campaign nss percent stack chartType
RefCommChartsSetting['nss_recovered_trend'] = {
        // 'colors': [colors.REM_NSS, colors.RECOVERED_CATCHUP],
        'colors': [colors.RECOVERED_CATCHUP, colors.REM_NSS],
        'chartType':{'type':"column", 'stacking':'percent'},
        'menu':[{chart:'percent', title:'Percent Chart'},
            {chart: 'normal', title:'Normal Chart'}]
};
// 10 Campaign refusal percent stack chartType
RefCommChartsSetting['refusal_recovered_trend'] = {
    // 'colors': [colors.REM_REFUSAL, colors.RECOVERED_CATCHUP],
    'colors': [colors.RECOVERED_CATCHUP, colors.REM_REFUSAL],
    'chartType':{'type':"column", 'stacking':'percent'},
    'menu':[{chart:'percent', title:'Percent Chart'},
        {chart: 'normal', title:'Normal Chart'}]
};

// 10 campaign missed recovery area percent chartType
RefCommChartsSetting['missed_child_recovery_trend'] = {
    // 'colors': [colors.REM_MISSED, colors.RECOVERED_ABSENT, colors.RECOVERED_NSS, colors.RECOVERED_REFUSAL],
    'colors': [colors.RECOVERED_REFUSAL, colors.RECOVERED_NSS, colors.RECOVERED_ABSENT, colors.REM_MISSED],
    'chartType':{'type':"area", 'stacking':'percent'},
    'menu':[{chart:'line', title:'Line Chart'},
        {chart: 'percent_area', title:'Area Chart'}]
};

 */





export default RefCommChartsSetting;