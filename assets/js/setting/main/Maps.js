/**
 * Setting for Maps (Main Dashboard) are defined here
 */

import colors from './../colors';

export default {

    // General Options
    DiscFinalRemainingRefusal: {
        name: 'Refusal',
        title: 'Remaining Refusal',
        colors: [colors.GREEN, colors.YELLOW, colors.ORANGE, colors.RED]
    },
    DiscRemainingAbsent: {
        name: 'Absent',
        title: 'Remaining Absent',
        colors: [colors.GREEN, colors.YELLOW, colors.ORANGE, colors.RED]
    },
    DiscRemainingNSS: {
        name: 'New Born/Sick/Sleep',
        title: 'Remaining NSS',
        colors: [colors.GREEN, colors.YELLOW, colors.ORANGE, colors.RED]
    },
    DiscFinalRemaining: {
        name: 'Total Remaining',
        title: 'Total Remaining',
        colors: [colors.GREEN, colors.YELLOW, colors.ORANGE, colors.RED]
    },

    // Percentage Benchmark
    PerRefusal: {
        name: '% Refusal',
        title: '% of Remaining Refusal',
        colors: [colors.GREEN, colors.ORANGE, colors.RED]
    },
    PerAbsent: {
        name: '% Absent',
        title: '% of Remaining Absent',
        colors: [colors.GREEN, colors.ORANGE, colors.RED]
    },
    PerNSS: {
        name: '% New Born/Sick/Sleep',
        title: '% of Remaining NSS',
        colors: [colors.GREEN, colors.ORANGE, colors.RED]
    },
    PerRemaining: {
        name: '% Total Remaining',
        title: '% of Total Remaining',
        colors: [colors.GREEN, colors.ORANGE, colors.RED]
    },

    // these keys should be same as the indicators from database
    region: { // district values multiply by ten the absolute values
        DiscFinalRemainingRefusal: {
            classes: [
                {from:0, to: 2000},
                {from: 2000, to: 5000},
                {from: 5000, to: 10000},
                {from: 10000}
            ]
        },

        DiscRemainingAbsent: {
            classes: [
                {from:0, to: 4000},
                {from: 4000, to: 8000},
                {from: 8000, to: 16000},
                {from: 16000}
            ]
        },

        DiscRemainingNSS: {
            classes: [
                {from:0, to: 1000},
                {from: 1000, to: 2500},
                {from: 2500, to: 10000},
                {from: 10000}
            ]
        },

        DiscFinalRemaining: {
            classes: [
                {from:0, to: 5000},
                {from: 5000, to: 10000},
                {from: 10000, to: 25000},
                {from: 25000}
            ]

        },

        PerRefusal: {
            classes: [
                {from: 0, to: 1},
                {from: 1, to: 2},
                {from: 2}
            ]
        },

        PerAbsent: {
            classes: [
                {from:0, to: 1.5},
                {from: 1.5, to: 3},
                {from:3}
            ]
        },

        PerNSS: {
            classes: [
                {from: 0, to: 0.5},
                {from: 0.5, to: 1},
                {from: 1}
            ]
        },

        PerRemaining: {
            classes: [
                {from: 0, to: 2},
                {from: 2, to: 5},
                {from: 5}
            ]

        },

    },

    province: {
        DiscFinalRemainingRefusal: {
            classes: [
                {from:0, to: 1000},
                {from: 1000, to: 2500},
                {from: 2500, to: 10000},
                {from: 10000}
            ]
        },

        DiscRemainingAbsent: {
            classes: [
                {from:0, to: 2000},
                {from: 2000, to: 4000},
                {from: 4000, to: 16000},
                {from: 16000}
            ]
        },

        DiscRemainingNSS: {
            classes: [
                {from:0, to: 500},
                {from: 500, to: 2000},
                {from: 2000, to: 8000},
                {from: 8000}
            ]
        },

        DiscFinalRemaining: {
            classes: [
                {from:0, to: 5000},
                {from: 5000, to: 10000},
                {from: 10000, to: 20000},
                {from: 20000}
            ]

        },
        PerRefusal: {
            classes: [
                {from: 0, to: 1},
                {from: 1, to: 2},
                {from: 2}
            ]
        },

        PerAbsent: {
            classes: [
                {from:0, to: 1.5},
                {from: 1.5, to: 3},
                {from:3}
            ]
        },

        PerNSS: {
            classes: [
                {from: 0, to: 1},
                {from: 1, to: 2},
                {from: 2}
            ]
        },

        PerRemaining: {
            classes: [
                {from: 0, to: 2},
                {from: 2, to: 5},
                {from: 5}
            ]

        },
    },

    district: {
        DiscFinalRemainingRefusal: {
            classes: [
                {from:0, to: 200},
                {from: 200, to: 500},
                {from: 500, to: 2000},
                {from: 2000}
            ]
        },

        DiscRemainingAbsent: {
            classes: [
                {from:0, to: 400},
                {from: 400, to: 800},
                {from: 800, to: 2400},
                {from: 2400}
            ]
        },

        DiscRemainingNSS: {
            classes: [
                {from:0, to: 200},
                {from: 200, to: 500},
                {from: 500, to: 1000},
                {from: 1000}
            ]
        },

        DiscFinalRemaining: {
            classes: [
                {from:0, to: 500},
                {from: 500, to: 1000},
                {from: 1000, to: 3000},
                {from: 3000}
            ]

        },

        PerRefusal: {
            classes: [
                {from: 0, to: 1},
                {from: 1, to: 2},
                {from: 2}
            ]
        },

        PerAbsent: {
            classes: [
                {from:0, to: 1.5},
                {from: 1.5, to: 3},
                {from:3}
            ]
        },

        PerNSS: {
            classes: [
                {from: 0, to: 1},
                {from: 1, to: 2},
                {from: 2}
            ]
        },

        PerRemaining: {
            classes: [
                {from: 0, to: 2},
                {from: 2, to: 5},
                {from: 5}
            ]

        },
    }

}