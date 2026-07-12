/**
 * MERGE INSTRUCTIONS - do not create a new file.
 * Open the project's existing tailwind.config.js and merge the
 * `theme.extend` contents below into it. The reference code.html
 * you provided uses the Tailwind CDN with an inline config - that
 * approach doesn't apply here since CounselOS compiles Tailwind via
 * Vite, so these tokens need to live in the real config file instead.
 *
 * Keep whatever `content` paths already exist in the file - only the
 * `theme.extend` block below is new.
 */

module.exports = {
    content: [
        // ...keep existing content paths (resources/views/**, etc.)
    ],
    theme: {
        extend: {
            colors: {
                surface: '#f7f9fb',
                'surface-dim': '#d8dadc',
                'surface-bright': '#f7f9fb',
                'surface-container-lowest': '#ffffff',
                'surface-container-low': '#f2f4f6',
                'surface-container': '#eceef0',
                'surface-container-high': '#e6e8ea',
                'surface-container-highest': '#e0e3e5',
                'on-surface': '#191c1e',
                'on-surface-variant': '#45474c',
                'inverse-surface': '#2d3133',
                'inverse-on-surface': '#eff1f3',
                outline: '#75777d',
                'outline-variant': '#c5c6cd',
                'surface-tint': '#545f73',
                primary: '#091426',
                'on-primary': '#ffffff',
                'primary-container': '#1e293b',
                'on-primary-container': '#8590a6',
                'inverse-primary': '#bcc7de',
                secondary: '#4648d4',
                'on-secondary': '#ffffff',
                'secondary-container': '#6063ee',
                'on-secondary-container': '#fffbff',
                tertiary: '#1e1200',
                'on-tertiary': '#ffffff',
                'tertiary-container': '#35260c',
                'on-tertiary-container': '#a38c6a',
                error: '#ba1a1a',
                'on-error': '#ffffff',
                'error-container': '#ffdad6',
                'on-error-container': '#93000a',
                background: '#f7f9fb',
                'on-background': '#191c1e',
                'surface-variant': '#e0e3e5',
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
            },
            fontSize: {
                'headline-xl': ['36px', { lineHeight: '44px', letterSpacing: '-0.02em', fontWeight: '700' }],
                'headline-lg': ['30px', { lineHeight: '38px', letterSpacing: '-0.01em', fontWeight: '600' }],
                'headline-md': ['24px', { lineHeight: '32px', fontWeight: '600' }],
                'body-lg': ['18px', { lineHeight: '28px', fontWeight: '400' }],
                'body-md': ['16px', { lineHeight: '24px', fontWeight: '400' }],
                'body-sm': ['14px', { lineHeight: '20px', fontWeight: '400' }],
                'label-md': ['14px', { lineHeight: '20px', letterSpacing: '0.05em', fontWeight: '600' }],
                'label-sm': ['12px', { lineHeight: '16px', fontWeight: '500' }],
            },
            spacing: {
                'container-max': '1440px',
                gutter: '24px',
                'margin-x': '32px',
                'stack-sm': '8px',
                'stack-md': '16px',
                'stack-lg': '24px',
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
    ],
};
