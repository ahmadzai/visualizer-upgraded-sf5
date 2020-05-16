/**
 * Setting for Maps (Covid19 Cases) are defined here
 */

import colors from './../colors';

export default {

    // General Options For All Level
    Cases: {
        name: 'Total Cases',
        title: 'Total Cases in Afghanistan',
        colors: ['#ffa331', '#ff7615', '#853503']
    },
    ActiveCases: {
        name: 'Active Cases',
        title: 'Active Cases in Afghanistan',
        colors: ['#ff5a4f', '#ff171f', '#610b0b']
    },
    Recovered: {
        name: 'Recovered Cases',
        title: 'Recovered Cases in Afghanistan',
        colors: ['#2fff83', '#0c842f', '#045315']
    },
    Death: {
        name: 'Death',
        title: 'Death in Afghanistan',
        colors: ['#706e6f', '#4f4f4b', '#353432']
    },

    province: {
        Cases: {
            classes: [
                {to: 10},
                {from: 10, to: 50},
                {from: 50}
            ]
        },

        ActiveCases: {
            classes: [
                {to: 10},
                {from: 10, to: 50},
                {from: 50}
            ]
        },

        Recovered: {
            classes: [
                {to: 4},
                {from: 4, to: 10},
                {from: 10}
            ]
        },

        Death: {
            classes: [
                {to: 2},
                {from: 2, to: 8},
                {from: 8}
            ]
        },
    }

}