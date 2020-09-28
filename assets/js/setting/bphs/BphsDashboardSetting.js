'use strict';
// ======================================= Composite indicators ===============================
import colors from "../colors";
import CoverageChartsSetting from "../coverage/Coverage";

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

BphsDashboardSetting['chart1'] = {'chartType':{'type':"column"}, 'colors':[colors.PURPLE], 'legend':{'enabled':false}};
BphsDashboardSetting['chart2'] = {'chartType':{'type':"column"}, 'colors':[colors.BLUE], 'legend':{'enabled':false}};
BphsDashboardSetting['chart3'] = {'chartType':{'type':"column"}, 'colors':[colors.ORANGE], 'legend':{'enabled':false}};

BphsDashboardSetting['chart4'] = {'chartType':{'type':"column"}, 'colors':['#2c9d76'], 'legend':{'enabled':false}};
BphsDashboardSetting['chart5'] = {'chartType':{'type':"column"}, 'colors':['#999608'], 'legend':{'enabled':false}};
BphsDashboardSetting['chart6'] = {'chartType':{'type':"column"}, 'colors':['#75cb5b'], 'legend':{'enabled':false}};

BphsDashboardSetting['chart7'] = {'chartType':{'type':"column"}, 'colors':['#a926a7'], 'legend':{'enabled':false}};
BphsDashboardSetting['chart8'] = {'chartType':{'type':"column"}, 'colors':['#cd7b15'], 'legend':{'enabled':false}};
BphsDashboardSetting['chart9'] = {'chartType':{'type':"column"}, 'colors':['#42a524'], 'legend':{'enabled':false}};

BphsDashboardSetting['chart10'] = {'chartType':{'type':"column"}, 'colors':['#9d2c39'], 'legend':{'enabled':false}};
BphsDashboardSetting['chart11'] = {'chartType':{'type':"column"}, 'colors':['#44cbf5'], 'legend':{'enabled':false}};
BphsDashboardSetting['chart12'] = {'chartType':{'type':"column"}, 'colors':['#af6de9'], 'legend':{'enabled':false}};

BphsDashboardSetting['chart13'] = {'chartType':{'type':"column"}, 'colors':['#155f45'], 'legend':{'enabled':false}};
BphsDashboardSetting['chart14'] = {'chartType':{'type':"column"}, 'colors':['#efed67'], 'legend':{'enabled':false}};
BphsDashboardSetting['chart15'] = {'chartType':{'type':"column"}, 'colors':['#c0ec18'], 'legend':{'enabled':false}};

export default BphsDashboardSetting;