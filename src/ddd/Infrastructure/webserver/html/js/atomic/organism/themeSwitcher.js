class ThemeSwitcher extends HTMLElement {
    colors = {
        dark: {
            'act1': '#222226',
            'act2': '#5d4d7a',
            'base': '#b2b2b2',
            'base-dim': '#686868',
            'bg1': '#292b2e',
            'bg2': '#212026',
            'bg3': '#100a14',
            'bg4': '#0a0814',
            'bg-alt': '#42444a',
            'border': '#5d4d7a',
            'cblk': '#cbc1d5',
            'cblk-bg': '#2f2b33',
            'cblk-ln': '#827591',
            'cblk-ln-bg': '#373040',
            'cursor': '#e3dedd',
            'const': '#a45bad',
            'comment': '#2aa1ae',
            'comment-light': '#2aa1ae',
            'comment-bg': '#292e34',
            'comp': '#c56ec3',
            'err': '#e0211d',
            'func': '#bc6ec5',
            'head1': '#4f97d7',
            'head1-bg': '#293239',
            'head2': '#2d9574',
            'head2-bg': '#293235',
            'head3': '#67b11d',
            'head3-bg': '#293235',
            'head4': '#b1951d',
            'head4-bg': '#32322c',
            'highlight': '#444155',
            'highlight-dim': '#3b314d',
            'keyword': '#4f97d7',
            'lnum': '#44505c',
            'mat': '#86dc2f',
            'meta': '#9f8766',
            'str': '#2d9574',
            'suc': '#86dc2f',
            'ttip': '#9a9aba',
            'ttip-sl': '#5e5079',
            'ttip-bg': '#34323e',
            'type': '#ce537a',
            'var': '#7590db',
            'war': '#dc752f'
        },

        light: {
            'act1': '#e7e5eb',
            'act2': '#d3d3e7',
            'base': '#655370',
            'base-dim': '#a094a2',
            'bg1': '#fbf8ef',
            'bg2': '#efeae9',
            'bg3': '#e3dedd',
            'bg4': '#d2ceda',
            'bg-alt': '#efeae9',
            'border': '#b3b9be',
            'cblk': '#655370',
            'cblk-bg': '#e8e3f0',
            'cblk-ln': '#9380b2',
            'cblk-ln-bg': '#ddd8eb',
            'cursor': '#100a14',
            'const': '#4e3163',
            'comment': '#2aa1ae',
            'comment-light': '#a49da5',
            'comment-bg': '#ecf3ec',
            'comp': '#6c4173',
            'err': '#e0211d',
            'func': '#6c3163',
            'head1': '#3a81c3',
            'head1-bg': '#edf1ed',
            'head2': '#2d9574',
            'head2-bg': '#edf2e9',
            'head3': '#67b11d',
            'head3-bg': '#edf2e9',
            'head4': '#b1951d',
            'head4-bg': '#f6f1e1',
            'highlight': '#d3d3e7',
            'highlight-dim': '#e7e7fc',
            'keyword': '#3a81c3',
            'lnum': '#a8a8bf',
            'mat': '#ba2f59',
            'meta': '#da8b55',
            'str': '#2d9574',
            'suc': '#42ae2c',
            'ttip': '#8c799f',
            'ttip-sl': '#c8c6dd',
            'ttip-bg': '#e2e0ea',
            'type': '#ba2f59',
            'var': '#715ab1',
            'war': '#dc752f'
        }
    };
    static get observedAttributes() {
        return ['theme'];
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (name === 'theme') {
            this.applyTheme(newValue);
        }
    }
    connectedCallback() {
        const theme = this.getAttribute('theme') || 'dark';
        this.applyTheme(theme)


    }
    applyTheme(themeName) {
        const themeColors = this.colors[themeName];
        if (!themeColors) {
            console.error(`Theme "${themeName}" does not exist.`);
            return;
        }

        for (let colorName in themeColors) {
            document.documentElement.style.setProperty(`--${colorName}`, themeColors[colorName]);
        }
    }
}
customElements.define('theme-switcher', ThemeSwitcher);
export default ThemeSwitcher;
