class ChartHelper {
    constructor() {
        this.chart = {
            renderTo: '',
            type: '',
            zoomType: 'xy'
        };

        this.lang = {
            printChart: 'Print chart',
            downloadPNG: 'Export PNG',
            downloadPDF: 'Export PDF',
            downloadCSV: 'Export Chart Data',

        };

        this.exporting = {
            sourceWidth: 800,
            sourceHeight: 450,
            buttons: {
                contextButton: {
                    menuItems: [
                        'printChart',
                        'downloadPNG',
                        'downloadPDF',
                        'downloadCSV',
                    ]
                }
            }
        };
        this.xAxis = {
            categories: [],
            labels: {
                style: {fontSize: '75%'},
                groupedOptions: [{
                    style: {color:'#025464'}
                }, {
                    rotation: 60, // rotate labels for a 2nd-level
                    align: 'right'
                }],
                //rotation: 0 // 0-level options aren't changed, use them as always
            },
            drawHorizontalBorders: false,
        };
        this.yAxis = {
            min: 0,
            title: {
                text: 'Chart title'
            },
            labels: {
                style: {
                    fontSize: '75%'
                }
            }
        };
        this.colors = ['#FF0000', '#C99900', '#FFFF00'];
        this.series = [];
    }
}

export default ChartHelper;