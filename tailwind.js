let defaultConfig = require('tailwindcss/defaultConfig')();

module.exports = {

    screens: {
        'sm': '576px',
        'md': '768px',
        'lg': '992px'
        // 'xl': '1200px',
    },

    width: Object.assign(defaultConfig.width, {
        '1': '0.25rem',
        '2': '0.5rem',
        '3': '0.75rem',
        '4': '1rem',
        '5': '1.25rem',
        '6': '1.5rem',
        '7': '1.75rem',
        '8': '2rem',
        '9': '2.25rem',
        '10': '2.5rem',


        '80':  '20rem',
        '96':  '24rem',
        '128': '32rem',
        '176': '44rem',

        '1/4': '25%',
        '2/4': '50%',
        '3/4': '75%',

        '1/5': '20%',
        '2/5': '40%',
        '3/5': '60%',
        '4/5': '80%',

        '1/6': '16.66667%',
        '2/6': '33.33333%',
        '3/6': '50%',
        '4/6': '66.66667%',
        '5/6': '83.33333%',

        '1/12': '8.33333%',
        '2/12': '16.66667%',
        '3/12': '25%',
        '4/12': '33.33333%',
        '5/12': '41.66667%',
        '6/12': '50%',
        '7/12': '58.33333%',
        '8/12': '66.66667%',
        '9/12': '75%',
        '10/12': '83.33333%',
        '11/12': '91.66667%',
    }),

    height: Object.assign(defaultConfig.height, {
        '5': '1.25rem',
    }),

    minWidth: Object.assign(defaultConfig.minWidth, {
        '1': '0.25rem',
        '2': '0.5rem',
        '3': '0.75rem',
        '4': '1rem',
        '5':  '1.25rem',
        '6': '1.5rem',
        '7': '1.75rem',
        '8': '2rem',
        '9': '2.25rem',
        '10': '2.5rem',
        '24': '6rem',
    }),

    margin: Object.assign(defaultConfig.margin, {
        '5': '1.25rem',
        '8': '2rem',
        '10': '2.5rem',
        '12': '3rem',
        '14': '3.5rem',
        '16': '4rem',
        '24': '6rem',
        '32': '8rem',
        '48': '12rem',
        '64': '16rem',
    }),

    negativeMargin: Object.assign(defaultConfig.negativeMargin, {
        '10': '2.5rem',
        '12': '3rem',
        '16': '4rem',
        '24': '6rem',
        '32': '8rem',
        '48': '12rem',
        '64': '16rem',
    }),

    maxHeight: Object.assign(defaultConfig.maxHeight, {
        '8': '2rem',
        '10': '2.5rem',
        '12': '3rem',
        '14': '3.5rem',
        '16': '4rem',
        '24': '6rem',
        '32': '8rem',
        '48': '12rem',
        '64': '16rem',
        '96': '20rem',
        '128': '32rem',

        '500': '500px', // Used for the "vlc fix boxes" blog
    }),

};
