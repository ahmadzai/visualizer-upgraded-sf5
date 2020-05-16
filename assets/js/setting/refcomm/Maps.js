/**
 * Setting for Maps (Refusal Committee Data) are defined here
 */

import colors from './../colors';

export default {

    // General Options For All Level
    RemRefusal: {
        name: 'Refusal',
        title: 'Remaining Refusal',
        colors: [colors.GREEN, colors.LIGHT_BLUE, colors.YELLOW, colors.ORANGE, colors.RED]
    },
    RemAbsent: {
        name: 'Absent',
        title: 'Remaining Absent',
        colors: [colors.GREEN, colors.LIGHT_BLUE, colors.YELLOW, colors.ORANGE, colors.RED]
    },
    RemNSS: {
        name: 'New Born/Sick/Sleep',
        title: 'Remaining NSS',
        colors: [colors.GREEN, colors.LIGHT_BLUE, colors.YELLOW, colors.ORANGE, colors.RED]
    },
    TotalRemaining: {
        name: 'Total Remaining',
        title: 'Total Remaining',
        colors: [colors.GREEN, colors.LIGHT_BLUE, colors.YELLOW, colors.ORANGE, colors.RED]
    },
    PerRecovered: {
        name: 'Per Recovered',
        title: 'Catchup: Per of Recovered',
        colors: [colors.RED, colors.ORANGE, colors.YELLOW, colors.LIGHT_BLUE, colors.GREEN]
    },

    // these keys should be same as the indicators from database
    region: {
        RemRefusal: {
            classes: [
                {to: 150},
                {from: 150, to: 300},
                {from: 300, to: 450},
                {from: 450, to: 600},
                {from: 600}
            ]
        },

        RemAbsent: {
            classes: [
                {to: 250},
                {from: 250, to: 500},
                {from: 500, to: 750},
                {from: 750, to: 1000},
                {from: 1000}
            ]
        },

        RemNSS: {
            classes: [
                {to: 100},
                {from: 100, to: 200},
                {from: 200, to: 300},
                {from: 300, to: 4000},
                {from: 400}
            ]
        },

        TotalRemaining: {
            classes: [
                {to: 300},
                {from: 300, to: 600},
                {from: 600, to: 900},
                {from: 900, to: 1200},
                {from: 1200}
            ]

        },

        PerRecovered: {
            classes: [
                {to: 20},
                {from: 20, to: 40},
                {from: 40, to: 60},
                {from: 60, to: 80},
                {from: 80}
            ]
        }
    },

    province: {
        RemRefusal: {
            classes: [
                {to: 75},
                {from: 75, to: 150},
                {from: 150, to: 225},
                {from: 225, to: 300},
                {from: 300}
            ]
        },

        RemAbsent: {
            classes: [
                {to: 125},
                {from: 125, to: 250},
                {from: 250, to: 375},
                {from: 500, to: 625},
                {from: 625}
            ]
        },

        RemNSS: {
            classes: [
                {to: 50},
                {from: 50, to: 100},
                {from: 100, to: 150},
                {from: 150, to: 200},
                {from: 200}
            ]
        },

        TotalRemaining: {
            classes: [
                {to: 150},
                {from: 150, to: 300},
                {from: 300, to: 450},
                {from: 450, to: 600},
                {from: 600}
            ]

        },

        PerRecovered: {
            classes: [
                {to: 55},
                {from: 55, to: 65},
                {from: 65, to: 75},
                {from: 75, to: 85},
                {from: 85}
            ]
        }
    },

    district: {
        RemRefusal: {
            classes: [
                {to: 50},
                {from: 50, to: 100},
                {from: 100, to: 150},
                {from: 150, to: 200},
                {from: 200}
            ]
        },

        RemAbsent: {
            classes: [
                {to: 100},
                {from: 100, to: 200},
                {from: 200, to: 300},
                {from: 300, to: 400},
                {from: 400}
            ]
        },

        RemNSS: {
            classes: [
                {to: 30},
                {from: 30, to: 60},
                {from: 60, to: 90},
                {from: 90, to: 120},
                {from: 120}
            ]
        },

        TotalRemaining: {
            classes: [
                {to: 125},
                {from: 125, to: 250},
                {from: 250, to: 375},
                {from: 375, to: 500},
                {from: 500}
            ]

        },

        PerRecovered: {
            classes: [
                {to: 55},
                {from: 55, to: 65},
                {from: 65, to: 75},
                {from: 75, to: 85},
                {from: 85}
            ]
        }
    }

}