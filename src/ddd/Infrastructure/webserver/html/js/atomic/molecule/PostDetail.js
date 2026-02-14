class PostDetail extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.post = null;
    }

    setPost(post) {
        this.post = post;
        this.render();
    }

    render() {
        if (!this.post) return;

        const date = (() => {
            try {
                return new Date(this.post.datePublished).toLocaleDateString('es-ES', {
                    year: 'numeric', month: 'long', day: 'numeric'
                });
            } catch {
                return this.post.datePublished;
            }
        })();

        this.shadowRoot.innerHTML = `
            <style>
                :host {
                    display: block;
                    font-family: 'Hasklig', 'Fira Code', monospace;
                    color: var(--base, #ccc);
                }

                .back-btn {
                    background: none;
                    border: 1px solid var(--border, #555);
                    color: var(--func, #bc6ec5);
                    padding: 6px 16px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-family: inherit;
                    font-size: 13px;
                    margin-bottom: 24px;
                    transition: border-color 0.2s;
                }

                .back-btn:hover {
                    border-color: var(--func, #bc6ec5);
                }

                .post-header {
                    margin-bottom: 30px;
                    padding-bottom: 20px;
                    border-bottom: 1px solid var(--border, #555);
                }

                .post-header h1 {
                    color: var(--head1, #4f97d7);
                    font-size: 26px;
                    margin: 0 0 10px 0;
                    line-height: 1.3;
                }

                .post-meta {
                    font-size: 13px;
                    color: var(--comment, #666);
                    display: flex;
                    gap: 16px;
                }

                .post-body {
                    line-height: 1.8;
                    font-size: 15px;
                }

                .post-body h1,
                .post-body h2,
                .post-body h3,
                .post-body h4 {
                    color: var(--head2, #2d9574);
                    margin-top: 1.5em;
                    margin-bottom: 0.5em;
                }

                .post-body h1 { font-size: 24px; color: var(--head1, #4f97d7); }
                .post-body h2 { font-size: 20px; }
                .post-body h3 { font-size: 17px; color: var(--head3, #67b11d); }

                .post-body p {
                    margin: 0.8em 0;
                }

                .post-body a {
                    color: var(--func, #bc6ec5);
                    text-decoration: none;
                }

                .post-body a:hover {
                    text-decoration: underline;
                }

                .post-body code {
                    background: var(--bg3, #333);
                    padding: 2px 6px;
                    border-radius: 3px;
                    font-size: 0.9em;
                }

                .post-body pre {
                    background: var(--bg3, #333);
                    border: 1px solid var(--border, #555);
                    border-radius: 6px;
                    padding: 16px;
                    overflow-x: auto;
                    font-size: 13px;
                    line-height: 1.5;
                }

                .post-body pre code {
                    background: none;
                    padding: 0;
                }

                .post-body blockquote {
                    border-left: 3px solid var(--act2, #4f97d7);
                    margin: 1em 0;
                    padding: 0.5em 1em;
                    color: var(--base-dim, #aaa);
                    background: var(--bg2, #292b2e);
                    border-radius: 0 4px 4px 0;
                }

                .post-body ul, .post-body ol {
                    padding-left: 24px;
                }

                .post-body li {
                    margin: 0.3em 0;
                }

                .post-body table {
                    border-collapse: collapse;
                    width: 100%;
                    margin: 1em 0;
                }

                .post-body th, .post-body td {
                    border: 1px solid var(--border, #555);
                    padding: 8px 12px;
                    text-align: left;
                }

                .post-body th {
                    background: var(--bg3, #333);
                    color: var(--keyword, #4f97d7);
                }

                .post-body img {
                    max-width: 100%;
                    border-radius: 4px;
                }
            </style>

            <button class="back-btn">Volver</button>

            <div class="post-header">
                <h1>${this.escapeHtml(this.post.headline)}</h1>
                <div class="post-meta">
                    <span>${this.escapeHtml(this.post.author)}</span>
                    <span>${date}</span>
                </div>
            </div>

            <div class="post-body">
                ${this.post.articleBody}
            </div>
        `;

        this.shadowRoot.querySelector('.back-btn').addEventListener('click', () => {
            this.dispatchEvent(new CustomEvent('back'));
        });
    }

    escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }
}

customElements.define('post-detail', PostDetail);
export default PostDetail;
