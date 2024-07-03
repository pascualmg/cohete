class ThemeSwitcher extends HTMLElement {

    colors = {
        'spacemacs-dark': {
            'act1': '#222226', 'act2': '#5d4d7a', 'base': '#b2b2b2', 'base-dim': '#686868',
            'bg1': '#292b2e', 'bg2': '#212026', 'bg3': '#100a14', 'bg4': '#0a0814',
            'bg-alt': '#353843', 'border': '#5d4d7a', 'cblk': '#cbc1d5', 'cblk-bg': '#2f2b33',
            'cblk-ln': '#827591', 'cblk-ln-bg': '#373040', 'cursor': '#e3dedd', 'const': '#a45bad',
            'comment': '#2aa1ae', 'comment-light': '#2aa1ae', 'comment-bg': '#292e34', 'comp': '#c56ec3',
            'err': '#e0211d', 'func': '#bc6ec5', 'head1': '#4f97d7', 'head1-bg': '#293239',
            'head2': '#2d9574', 'head2-bg': '#293235', 'head3': '#67b11d', 'head3-bg': '#293235',
            'head4': '#b1951d', 'head4-bg': '#32322c', 'highlight': '#444155', 'highlight-dim': '#3b314d',
            'keyword': '#4f97d7', 'lnum': '#44505c', 'mat': '#86dc2f', 'meta': '#9f8766',
            'str': '#2d9574', 'suc': '#86dc2f', 'ttip': '#9a9aba', 'ttip-sl': '#5e5079',
            'ttip-bg': '#34323e', 'type': '#ce537a', 'var': '#7590db', 'war': '#dc752f',
            'aqua': '#2d9574', 'aqua-bg': '#293235', 'green': '#67b11d', 'green-bg': '#293235',
            'green-bg-s': '#29422d', 'cyan': '#28def0', 'red': '#f2241f', 'red-bg': '#3c2a2c',
            'red-bg-s': '#512e31', 'blue': '#4f97d7', 'blue-bg': '#293239', 'blue-bg-s': '#2d4252',
            'magenta': '#a31db1', 'yellow': '#b1951d', 'yellow-bg': '#32322c'
        },
        'spacemacs-light': {
            'act1': '#e7e5eb', 'act2': '#d3d3e7', 'base': '#585858', 'base-dim': '#a8a8a8',
            'bg1': '#fbf8ef', 'bg2': '#efeae9', 'bg3': '#e3dedd', 'bg4': '#d2ceda',
            'bg-alt': '#eae6f4', 'border': '#b0aeb7', 'cblk': '#655370', 'cblk-bg': '#e2e2e2',
            'cblk-ln': '#9380b2', 'cblk-ln-bg': '#eceaf3', 'cursor': '#100a14', 'const': '#4e3163',
            'comment': '#2aa1ae', 'comment-light': '#7e8d9b', 'comment-bg': '#f0f4f7', 'comp': '#6c4173',
            'err': '#e0211d', 'func': '#6c3163', 'head1': '#3a81c3', 'head1-bg': '#f0f4f7',
            'head2': '#2d9574', 'head2-bg': '#f0f5f2', 'head3': '#67b11d', 'head3-bg': '#f0f5f2',
            'head4': '#b1951d', 'head4-bg': '#f6f2e8', 'highlight': '#d0d4e6', 'highlight-dim': '#e4e7f3',
            'keyword': '#3a81c3', 'lnum': '#a3a3b5', 'mat': '#ba2f59', 'meta': '#da8b55',
            'str': '#2d9574', 'suc': '#42ae2c', 'ttip': '#8c799f', 'ttip-sl': '#c8c6dd',
            'ttip-bg': '#e4e2ea', 'type': '#ba2f59', 'var': '#715ab1', 'war': '#dc752f',
            'aqua': '#2d9574', 'aqua-bg': '#f0f5f2', 'green': '#67b11d', 'green-bg': '#f0f5f2',
            'green-bg-s': '#f0f5f2', 'cyan': '#21b8c7', 'red': '#f2241f', 'red-bg': '#fdf4f3',
            'red-bg-s': '#fdf4f3', 'blue': '#3a81c3', 'blue-bg': '#f0f4f7', 'blue-bg-s': '#f0f4f7',
            'magenta': '#6c4173', 'yellow': '#b1951d', 'yellow-bg': '#f6f2e8'
        },
        'solarized-dark': {
            'act1': '#002b36', 'act2': '#003542', 'base': '#93a1a1', 'base-dim': '#72888e',
            'bg1': '#002b36', 'bg2': '#073642', 'bg3': '#00212b', 'bg4': '#002731',
            'bg-alt': '#00212b', 'border': '#586e75', 'cblk': '#839496', 'cblk-bg': '#00212b',
            'cblk-ln': '#586e75', 'cblk-ln-bg': '#07272d', 'cursor': '#eee8d5', 'const': '#d33682',
            'comment': '#586e75', 'comment-light': '#7e9dac', 'comment-bg': '#00212b', 'comp': '#d33682',
            'err': '#dc322f', 'func': '#268bd2', 'head1': '#859900', 'head1-bg': '#073642',
            'head2': '#b58900', 'head2-bg': '#07272d', 'head3': '#268bd2', 'head3-bg': '#07272d',
            'head4': '#2aa198', 'head4-bg': '#00212b', 'highlight': '#094554', 'highlight-dim': '#0e4b5e',
            'keyword': '#859900', 'lnum': '#475b62', 'mat': '#2aa198', 'meta': '#b58900',
            'str': '#2aa198', 'suc': '#859900', 'ttip': '#465a61', 'ttip-sl': '#657b83',
            'ttip-bg': '#07272d', 'type': '#b58900', 'var': '#268bd2', 'war': '#cb4b16',
            'aqua': '#2aa198', 'aqua-bg': '#07272d', 'green': '#859900', 'green-bg': '#073642',
            'green-bg-s': '#073642', 'cyan': '#2aa198', 'red': '#dc322f', 'red-bg': '#07272d',
            'red-bg-s': '#07272d', 'blue': '#268bd2', 'blue-bg': '#073642', 'blue-bg-s': '#073642',
            'magenta': '#d33682', 'yellow': '#b58900', 'yellow-bg': '#07272d'
        },
        'solarized-light': {
            'act1': '#eee8d5', 'act2': '#e9ddc9', 'base': '#657b83', 'base-dim': '#8a9ba3',
            'bg1': '#fdf6e3', 'bg2': '#eee8d5', 'bg3': '#fdf8ea', 'bg4': '#eee8d5',
            'bg-alt': '#f2f0e7', 'border': '#839496', 'cblk': '#586e75', 'cblk-bg': '#fcf3e0',
            'cblk-ln': '#93a1a1', 'cblk-ln-bg': '#eadec9', 'cursor': '#002b36', 'const': '#d33682',
            'comment': '#93a1a1', 'comment-light': '#8a9ba3', 'comment-bg': '#f3f8fd', 'comp': '#d33682',
            'err': '#dc322f', 'func': '#268bd2', 'head1': '#859900', 'head1-bg': '#f3f8fd',
            'head2': '#b58900', 'head2-bg': '#fcf4e2', 'head3': '#268bd2', 'head3-bg': '#fcf4e2',
            'head4': '#2aa198', 'head4-bg': '#f2f6f8', 'highlight': '#e6dccb', 'highlight-dim': '#f0e7d2',
            'keyword': '#859900', 'lnum': '#bcc5ca', 'mat': '#2aa198', 'meta': '#b58900',
            'str': '#2aa198', 'suc': '#859900', 'ttip': '#93a1a1', 'ttip-sl': '#7b8e9a',
            'ttip-bg': '#eadec9', 'type': '#b58900', 'var': '#268bd2', 'war': '#cb4b16',
            'aqua': '#2aa198', 'aqua-bg': '#f2f6f8', 'green': '#859900', 'green-bg': '#f3f8fd',
            'green-bg-s': '#f3f8fd', 'cyan': '#2aa198', 'red': '#dc322f', 'red-bg': '#fdf3f2',
            'red-bg-s': '#fdf3f2', 'blue': '#268bd2', 'blue-bg': '#f3f8fd', 'blue-bg-s': '#f3f8fd',
            'magenta': '#d33682', 'yellow': '#b58900', 'yellow-bg': '#fcf4e2'
        },
        'linkedin': {
            'act1': '#313335', 'act2': '#0077B5', 'base': '#262626', 'base-dim': '#6f7173',
            'bg1': '#FFFFFF', 'bg2': '#F3F6F8', 'bg3': '#E9EDF0', 'bg4': '#DCE4E7',
            'bg-alt': '#F3F6F8', 'border': '#0077B5', 'cblk': '#262626', 'cblk-bg': '#F3F6F8',
            'cblk-ln': '#0077B5', 'cblk-ln-bg': '#E9EDF0', 'cursor': '#262626', 'const': '#0077B5',
            'comment': '#6f7173', 'comment-light': '#9ea3a6', 'comment-bg': '#F3F6F8', 'comp': '#0073B1',
            'err': '#D11124', 'func': '#0077B5', 'head1': '#0077B5', 'head1-bg': '#E9EDF0',
            'head2': '#0077B5', 'head2-bg': '#E9EDF0', 'head3': '#0077B5', 'head3-bg': '#E9EDF0',
            'head4': '#0077B5', 'head4-bg': '#E9EDF0', 'highlight': '#DCE4E7', 'highlight-dim': '#E9EDF0',
            'keyword': '#0077B5', 'lnum': '#86888A', 'mat': '#0077B5', 'meta': '#86888A',
            'str': '#0077B5', 'suc': '#38A34B', 'ttip': '#86888A', 'ttip-sl': '#0077B5',
            'ttip-bg': '#F3F6F8', 'type': '#0077B5', 'var': '#0077B5', 'war': '#D11124',
            'aqua': '#2F8FA3', 'aqua-bg': '#F3F6F8', 'green': '#38A34B', 'green-bg': '#F3F6F8',
            'green-bg-s': '#F3F6F8', 'cyan': '#2F8FA3', 'red': '#D11124', 'red-bg': '#F3F6F8',
            'red-bg-s': '#F3F6F8', 'blue': '#0077B5', 'blue-bg': '#F3F6F8', 'blue-bg-s': '#F3F6F8',
            'magenta': '#8F3B8C', 'yellow': '#DD5143', 'yellow-bg': '#F3F6F8'
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
        const theme = this.getAttribute('theme') || 'spacemacs-dark';
        this.applyTheme(theme);
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
        document.documentElement.style.backgroundColor = themeColors['bg1'];
    }

    get themeNames() {
        return Object.keys(this.colors);
    }
}

customElements.define('theme-switcher', ThemeSwitcher);
export default ThemeSwitcher;