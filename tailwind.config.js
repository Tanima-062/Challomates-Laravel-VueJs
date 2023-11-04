module.exports = {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],

    fontFamily: {
        // sans: ['Graphik', 'sans-serif'],
        // serif: ['Merriweather', 'serif'],
    },
    theme: {
        extend: {
            spacing: {
                '60': '60px',
                '130': '130px',
                '420': '420px',
                '440': '440px',
                '480': '480px',
                '890': '890px',
            },
            colors: {
                'background': '#E9F5F3',
                'shades': {
                    2: '#25D1FF',
                    3: '#00708E',
                },
                'tones': {
                    2: '#A0A0A0',
                },
                'tints': {
                    1: '#8ED5F6',
                    2: '#44B8F1',
                    3: '#1892CE',
                    4: '#1677A9',
                    5: '#135F84',
                },
                'gray': {
                    3: '#787878',
                    'text': '#A0A0A0',
                    'corner': "#C2C5C6",
                },
                'primary': {
                    1: '#1AA1E4',
                    2: '#06CEB5',
                    3: '#008CB0'
                },
                'secondary': {
                    2: '#FFBA49',
                    3: '#102327',
                },
                'warms': {
                    3: '#FFA013'
                },
                'gradient': {
                    1: 'linear-gradient(90deg, #03D48E 0%, #01CFB6 38.06%, #08BAEC 67.5%, #008DB1 99.99%)',
                    2: 'gradient(90deg, #B7EDE6 0%, #EAF3F9 99.99%)',
                    3: 'linear-gradient(183.02deg, #13A3E5 -4%, #B9C7AF 31.73%, #FFD698 46.45%)'
                },
                'error': '#FF0000'
            },
            fontSize: {
                '12': ['12px', {
                    lineHeight: '18px',
                }],
                '13': ['13px', {
                    lineHeight: '20px',
                }],
                '14': ['14px', {
                    lineHeight: '21px',
                }],
                '15': ['15px', {
                    lineHeight: '22px',
                }],
                '16': ['16px', {
                    lineHeight: '17px',
                }],
                '17': ['17px', {
                    lineHeight: '24px',
                }],
                '18x': ['18px', {
                    lineHeight: '24px'
                }],
                '18xl': ['18px', {
                    lineHeight: '27px'
                }],
                '22': ['22px', {
                    lineHeight: '33px',
                }],
                '35': ['35px', {
                    lineHeight: '38px',
                }

                ]

            },
            fontFamily: {
                'poppins': ['Poppins', 'sans-serif'],
                'ropa': ['Ropa Sans', 'sans-serif'],
            },
            borderRadius: {
                35: '35px',
                56: '56px',
                45: '45px'
            },
            maxHeight: {
                "10v": "10vh",
                "20v": "20vh",
                "30v": "30vh",
                "40v": "40vh",
                "50v": "50vh",
                "60v": "60vh",
                "70v": "70vh",
                "80v": "80vh",
                "90v": "90vh",
                "100v": "100vh",
            },
        },
    },
    plugins: [],
}
