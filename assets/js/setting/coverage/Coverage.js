'use strict';
import colors from './../colors';

const CoverageChartsSetting = {};

// The first row of one campaign
CoverageChartsSetting['missed_by_reason_pie_1'] = {
    'colors':[colors.REM_ABSENT, colors.REM_NSS, colors.REM_REFUSAL],
    'chartType':{'type':'donut'}, 'legend':false, 'area':'small'
};

CoverageChartsSetting['vaccine_wastage_1'] = {'chartType':{'type':"column"},
    'colors':[colors.WASTAGE], 'legend':{'enabled':false}};

CoverageChartsSetting['total_recovered_remaining_1'] = {'chartType':{'type':"column", 'stacking': 'normal'},
    'colors':[colors.RECOVERED_3DAYS, colors.REM_MISSED],
    'legend':{'enabled':true, 'vAlign':'bottom', 'hAlign': 'center'},
    'menu':[{chart:'normal', title:'Normal Chart'},
        {chart: 'percent', title:'Percent Chart'}],
    'scrollbar': {min:0, max:15}
};

// The first row of one campaign
CoverageChartsSetting['campaign_title'] = {'chartType':{'type':'html'}};

CoverageChartsSetting['info_box'] = {'chartType':{'type':'html'}};

CoverageChartsSetting['info_table'] = {'chartType':{'type':'html'}};

CoverageChartsSetting['map_data'] = {'chartType': {'type': 'raw'}};

// Pie charts Row
CoverageChartsSetting['recovered_all_type_1'] = {
    'colors':[colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5, colors.REM_MISSED],
    'chartType':{'type':'halfpie'}, 'legend':true
};
CoverageChartsSetting['recovered_absent_1'] = {
    'colors':[colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5, colors.REM_ABSENT],
    'chartType':{'type':'halfpie'}, 'legend':true
};
CoverageChartsSetting['recovered_nss_1'] = {
    'colors':[colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5, colors.REM_NSS],
    'chartType':{'type':'halfpie'}, 'legend':true
};
CoverageChartsSetting['recovered_refusal_1'] = {
    'colors':[colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5, colors.REM_REFUSAL],
    'chartType':{'type':'halfpie'}, 'legend':true
};

// missed loc trends
CoverageChartsSetting['loc_trend_all_type'] = {
    // 'colors': [colors.REM_MISSED, colors.RECOVERED_DAY5, colors.RECOVERED_3DAYS],
    'colors': [colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5, colors.REM_MISSED],
    'chartType':{'type':"column", 'stacking':'normal'},
    'legend':{'enabled':true, 'vAlign':'center', 'hAlign': 'left'},
    'menu':[{chart:'normal', title:'Normal Chart'},
        {chart: 'percent', title:'Percent Chart'}],
    'scrollbar': {min:0, max:18}
};
// absent loc trends
CoverageChartsSetting['loc_trend_absent'] = {
    // 'colors': [colors.REM_ABSENT, colors.RECOVERED_DAY5, colors.RECOVERED_3DAYS],
    'colors': [colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5, colors.REM_ABSENT],
    'chartType':{'type':"column", 'stacking':'normal'},
    'legend':{'enabled':true, 'vAlign':'center', 'hAlign': 'left'},
    'menu':[{chart:'normal', title:'Normal Chart'},
        {chart: 'percent', title:'Percent Chart'}],
    'scrollbar': {min:0, max:18}
};
// nss loc trends
CoverageChartsSetting['loc_trend_nss'] = {
    // 'colors': [colors.REM_NSS, colors.RECOVERED_DAY5, colors.RECOVERED_3DAYS],
    'colors': [colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5, colors.REM_NSS],
    'chartType':{'type':"column", 'stacking':'normal'},
    'legend':{'enabled':true, 'vAlign':'center', 'hAlign': 'left'},
    'menu':[{chart:'normal', title:'Normal Chart'},
        {chart: 'percent', title:'Percent Chart'}],
    'scrollbar': {min:0, max:18}
};
// refusal loc trends
CoverageChartsSetting['loc_trend_refusal'] = {
    // 'colors': [colors.REM_REFUSAL, colors.RECOVERED_DAY5, colors.RECOVERED_3DAYS],
    'colors': [colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5, colors.REM_REFUSAL],
    'chartType':{'type':"column", 'stacking':'normal'},
    'legend':{'enabled':true, 'vAlign':'center', 'hAlign': 'left'},
    'menu':[{chart:'normal', title:'Normal Chart'},
        {chart: 'percent', title:'Percent Chart'}],
    'scrollbar': {min:0, max:18}
};

// 10 campaign vaccinated children column chartType
CoverageChartsSetting['vac_child_trend'] = {'chartType':{'type':"column"}, 'colors':[colors.VACCINATED]};
// 10 campaign missed children column chartType
CoverageChartsSetting['missed_child_trend'] = {'chartType':{'type':"column"}, 'colors':[colors.REM_MISSED]};
// 10 campaign missed by type stack column chartType
CoverageChartsSetting['missed_by_type_trend'] = {'chartType':{'type':"column", 'stacking':'normal'},
    'colors':[colors.REM_ABSENT, colors.REM_NSS, colors.REM_REFUSAL]};
// 10 Campaign absent percent stack chartType
CoverageChartsSetting['absent_recovered_trend'] = {
    // 'colors': [colors.REM_ABSENT, colors.RECOVERED_DAY5, colors.RECOVERED_3DAYS],
    'colors': [colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5, colors.REM_ABSENT],
    'chartType':{'type':"column", 'stacking':'percent'}
};
// 10 Campaign nss percent stack chartType
CoverageChartsSetting['nss_recovered_trend'] = {
    // 'colors': [colors.REM_NSS, colors.RECOVERED_DAY5, colors.RECOVERED_3DAYS],
    'colors': [colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5, colors.REM_NSS],
    'chartType':{'type':"column", 'stacking':'percent'}
};
// 10 Campaign refusal percent stack chartType
CoverageChartsSetting['refusal_recovered_trend'] = {
    // 'colors': [colors.REM_REFUSAL, colors.RECOVERED_DAY5, colors.RECOVERED_3DAYS],
    'colors': [colors.RECOVERED_3DAYS, colors.RECOVERED_DAY5, colors.REM_REFUSAL],
    'chartType':{'type':"column", 'stacking':'percent'}};

// 10 campaign missed recovery area percent chartType
CoverageChartsSetting['missed_child_recovery_trend'] = {
    // 'colors': [colors.REM_MISSED, colors.RECOVERED_ABSENT, colors.RECOVERED_NSS, colors.RECOVERED_REFUSAL],
    'colors': [colors.RECOVERED_REFUSAL, colors.RECOVERED_NSS, colors.RECOVERED_ABSENT, colors.REM_MISSED],
    'chartType':{'type':"area", 'stacking':'percent'}
};


export default CoverageChartsSetting;