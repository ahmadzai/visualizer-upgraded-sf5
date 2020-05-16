'use strict';
import colors from './../colors';
// ======================================= Clusters Level Dashbaord ===============================
const CoverageCluster = {};
CoverageCluster['missed_recovery_chart_1'] = {
    'colors': [colors.RECOVERED_3DAYS, colors.REM_ABSENT, colors.REM_NSS, colors.REM_REFUSAL],
    'chartType':{'type':"bar", 'stacking':'percent'},
    'large':'height'
};
// Table
CoverageCluster['cluster_trend'] = {'chartType':{'type':'table'},
    'setting' : {
        "scrollX": true,
        'paging':false,
        'dom': 'frtipB',
        'buttons': [
            'copyHtml5', 'csvHtml5'
        ]
    }
};

// 3 (default) campaigns location trends
// 10 Campaign absent percent stack chartType
CoverageCluster['loc_trend_all_type'] = {
    'colors': [colors.RECOVERED_3DAYS, colors.REM_MISSED],
    'chartType':{'type':"column", 'stacking':'normal'},
    'legend':{'enabled':true, 'vAlign':'center', 'hAlign': 'left'},
    'menu':[{chart:'normal', title:'Normal Chart'},
        {chart: 'percent', title:'Percent Chart'}],
    'scrollbar': {min:0, max:40}
};
// absent loc trends
CoverageCluster['loc_trend_absent'] = {
    'colors': [colors.RECOVERED_3DAYS, colors.REM_ABSENT],
    'chartType':{'type':"column", 'stacking':'normal'},
    'legend':{'enabled':true, 'vAlign':'center', 'hAlign': 'left'},
    'menu':[{chart:'normal', title:'Normal Chart'},
        {chart: 'percent', title:'Percent Chart'}],
    'scrollbar': {min:0, max:40}
};
// nss loc trends
CoverageCluster['loc_trend_nss'] = {
    'colors': [colors.RECOVERED_3DAYS, colors.REM_NSS],
    'chartType':{'type':"column", 'stacking':'normal'},
    'legend':{'enabled':true, 'vAlign':'center', 'hAlign': 'left'},
    'menu':[{chart:'normal', title:'Normal Chart'},
        {chart: 'percent', title:'Percent Chart'}],
    'scrollbar': {min:0, max:40}
};
// refusal loc trends
CoverageCluster['loc_trend_refusal'] = {
    'colors': [colors.RECOVERED_3DAYS, colors.REM_REFUSAL],
    'chartType':{'type':"column", 'stacking':'normal'},
    'legend':{'enabled':true, 'vAlign':'center', 'hAlign': 'left'},
    'menu':[{chart:'normal', title:'Normal Chart'},
        {chart: 'percent', title:'Percent Chart'}],
    'scrollbar': {min:0, max:40}
};

export default CoverageCluster;