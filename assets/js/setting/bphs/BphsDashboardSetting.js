'use strict';
// ======================================= Composite indicators ===============================
const BphsDashboardSetting = {};
// Table
BphsDashboardSetting['bphs_table_cum'] = {'chartType':{'type':'table'},
    'setting' : {
        'scrollX': true,
        'scrollY': "30vh",
        'scrollCollapse': true,
        'paging':         false,
        'fixedColumns':  false,
        'dom': 'Bfrtip',
        'buttons': [
            'copyHtml5', 'csvHtml5'
        ]
    }
};

BphsDashboardSetting['bphs_table_months'] = {'chartType':{'type':'table'},
    'setting' : {
        'scrollX': true,
        'scrollY': "30vh",
        'scrollCollapse': true,
        'paging':         false,
        'fixedColumns':   {
            'leftColumns': 1,
            'heightMatch': 'none'
        },
        'dom': 'Bfrtip',
        'buttons': [
            'copyHtml5', 'csvHtml5'
        ]
    }
};

export default BphsDashboardSetting;