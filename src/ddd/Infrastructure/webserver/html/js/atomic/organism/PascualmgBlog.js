import '../molecule/PostDetail.js';

class PascualmgBlog extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.posts = [];
        this.render();
        this.fetchPosts();
    }

    fetchPosts() {
        const loading = this.shadowRoot.querySelector('.loading');
        if (loading) loading.style.display = 'block';

        fetch('/post')
            .then(res => res.json())
            .then(posts => {
                this.posts = posts;
                this.renderList();
            })
            .catch(err => {
                this.renderError(err.message);
            });
    }

    renderList() {
        const container = this.shadowRoot.querySelector('.content');
        if (!container) return;

        if (this.posts.length === 0) {
            container.innerHTML = `
                <div class="empty">
                    <h2>No hay posts todavia</h2>
                    <p>Publica el primero con <code>publish mi-post.org</code></p>
                </div>
            `;
            return;
        }

        container.innerHTML = `
            <div class="post-list">
                ${this.posts.map(post => `
                    <article class="post-card" data-id="${post.id}">
                        <h2 class="post-title">${this.escapeHtml(post.headline)}</h2>
                        <div class="post-meta">
                            <span class="post-author">${this.escapeHtml(post.author)}</span>
                            <span class="post-date">${this.formatDate(post.datePublished)}</span>
                        </div>
                        <p class="post-excerpt">${this.getExcerpt(post.articleBody)}</p>
                        <span class="read-more">Leer mas</span>
                    </article>
                `).join('')}
            </div>
        `;

        container.querySelectorAll('.post-card').forEach(card => {
            card.addEventListener('click', () => {
                const id = card.getAttribute('data-id');
                const post = this.posts.find(p => p.id === id);
                if (post) this.showPost(post);
            });
        });
    }

    showPost(post) {
        const container = this.shadowRoot.querySelector('.content');
        container.innerHTML = '';

        const detail = document.createElement('post-detail');
        detail.setPost(post);
        detail.addEventListener('back', () => this.renderList());
        container.appendChild(detail);
    }

    renderError(message) {
        const container = this.shadowRoot.querySelector('.content');
        if (container) {
            container.innerHTML = `<div class="error">Error: ${message}</div>`;
        }
    }

    getExcerpt(html) {
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        const text = tmp.textContent || tmp.innerText || '';
        return text.length > 200 ? text.substring(0, 200) + '...' : text;
    }

    formatDate(dateStr) {
        try {
            const d = new Date(dateStr);
            return d.toLocaleDateString('es-ES', {
                year: 'numeric', month: 'long', day: 'numeric'
            });
        } catch {
            return dateStr;
        }
    }

    escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    render() {
        this.shadowRoot.innerHTML = `
            <style>
                :host {
                    display: block;
                    font-family: 'Hasklig', 'Fira Code', monospace;
                    padding: 20px;
                    max-width: 800px;
                    margin: 0 auto;
                    color: var(--base, #ccc);
                }

                h1 {
                    color: var(--head1, #4f97d7);
                    font-size: 28px;
                    margin-bottom: 8px;
                }

                .subtitle {
                    color: var(--base-dim, #888);
                    font-size: 14px;
                    margin-bottom: 30px;
                }

                .loading {
                    color: var(--comment, #666);
                    text-align: center;
                    padding: 40px;
                }

                .error {
                    color: var(--err, #e0211d);
                    padding: 20px;
                    border: 1px solid var(--err, #e0211d);
                    border-radius: 4px;
                }

                .empty {
                    text-align: center;
                    padding: 60px 20px;
                    color: var(--base-dim, #888);
                }

                .empty code {
                    background: var(--bg3, #333);
                    padding: 2px 8px;
                    border-radius: 3px;
                    font-size: 14px;
                }

                .post-list {
                    display: flex;
                    flex-direction: column;
                    gap: 20px;
                }

                .post-card {
                    background: var(--bg2, #292b2e);
                    border: 1px solid var(--border, #555);
                    border-radius: 6px;
                    padding: 20px;
                    cursor: pointer;
                    transition: border-color 0.2s, transform 0.2s;
                }

                .post-card:hover {
                    border-color: var(--act2, #4f97d7);
                    transform: translateY(-2px);
                }

                .post-title {
                    color: var(--head2, #2d9574);
                    font-size: 20px;
                    margin: 0 0 8px 0;
                }

                .post-meta {
                    display: flex;
                    gap: 16px;
                    font-size: 13px;
                    color: var(--comment, #666);
                    margin-bottom: 12px;
                }

                .post-excerpt {
                    color: var(--base-dim, #aaa);
                    font-size: 14px;
                    line-height: 1.6;
                    margin: 0;
                }

                .read-more {
                    display: inline-block;
                    margin-top: 12px;
                    font-size: 13px;
                    color: var(--func, #bc6ec5);
                }

                .upload-section {
                    margin-top: 30px;
                    padding: 20px;
                    border: 2px dashed var(--border, #555);
                    border-radius: 6px;
                    text-align: center;
                }

                .upload-section label {
                    cursor: pointer;
                    color: var(--act2, #4f97d7);
                    font-size: 14px;
                }

                .upload-section label:hover {
                    color: var(--func, #bc6ec5);
                }

                .upload-section input[type="file"] {
                    display: none;
                }

                .upload-status {
                    margin-top: 10px;
                    font-size: 13px;
                    color: var(--comment, #666);
                }

                .upload-status.success {
                    color: var(--suc, #2d9574);
                }

                .upload-status.error {
                    color: var(--err, #e0211d);
                }
            </style>

            <h1>Blog</h1>
            <p class="subtitle">pascualmg.dev</p>

            <div class="upload-section">
                <label for="org-upload">Publicar archivo .org</label>
                <input type="file" id="org-upload" accept=".org">
                <div class="upload-status"></div>
            </div>

            <div class="content">
                <div class="loading">Cargando posts...</div>
            </div>
        `;

        const fileInput = this.shadowRoot.querySelector('#org-upload');
        fileInput.addEventListener('change', (e) => this.handleUpload(e));
    }

    handleUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        const status = this.shadowRoot.querySelector('.upload-status');
        status.textContent = `Subiendo ${file.name}...`;
        status.className = 'upload-status';

        const reader = new FileReader();
        reader.onload = (e) => {
            fetch('/post/org', {
                method: 'POST',
                headers: { 'Content-Type': 'text/plain' },
                body: e.target.result,
            })
                .then(res => res.json())
                .then(result => {
                    status.textContent = `Publicado: ${result.headline}`;
                    status.className = 'upload-status success';
                    this.fetchPosts();
                })
                .catch(err => {
                    status.textContent = `Error: ${err.message}`;
                    status.className = 'upload-status error';
                });
        };
        reader.readAsText(file);

        event.target.value = '';
    }
}

customElements.define('pascualmg-blog', PascualmgBlog);
export default PascualmgBlog;
