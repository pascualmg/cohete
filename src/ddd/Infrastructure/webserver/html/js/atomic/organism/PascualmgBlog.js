class PascualmgBlog extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.posts = [];
        this.selectedPost = null;
        this.loading = true;
        this.error = null;
    }

    connectedCallback() {
        this.render();
        this.fetchPosts();
    }

    async fetchPosts() {
        try {
            const response = await fetch('/post');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            this.posts = await response.json();
            this.loading = false;
            this.render();
        } catch (error) {
            this.error = error.message;
            this.loading = false;
            this.render();
        }
    }

    selectPost(post) {
        this.selectedPost = post;
        this.render();
    }

    goBack() {
        this.selectedPost = null;
        this.render();
    }

    formatDate(dateString) {
        try {
            const date = new Date(dateString);
            return date.toLocaleDateString('es-ES', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        } catch {
            return dateString;
        }
    }

    renderLoading() {
        return `
            <div class="loading">
                <div class="spinner"></div>
                <p>Cargando posts...</p>
            </div>
        `;
    }

    renderError() {
        return `
            <div class="error">
                <div class="icon">!</div>
                <h2>Error al cargar los posts</h2>
                <p>${this.error}</p>
                <button class="retry-btn">Reintentar</button>
            </div>
        `;
    }

    renderEmpty() {
        return `
            <div class="empty">
                <div class="icon">~</div>
                <h2>No hay posts todavia</h2>
                <p>El blog esta vacio. Pronto habra contenido interesante.</p>
            </div>
        `;
    }

    renderPostList() {
        if (this.posts.length === 0) {
            return this.renderEmpty();
        }

        const postsHtml = this.posts.map((post, index) => `
            <article class="post-card" data-index="${index}">
                <header>
                    <h2 class="post-title">${post.headline}</h2>
                    <div class="post-meta">
                        <span class="author">${post.author}</span>
                        <span class="date">${this.formatDate(post.datePublished)}</span>
                    </div>
                </header>
                <p class="post-excerpt">${post.articleBody.substring(0, 200)}...</p>
                <footer>
                    <span class="read-more">Leer mas &rarr;</span>
                </footer>
            </article>
        `).join('');

        return `
            <div class="posts-header">
                <h1>Blog</h1>
                <p class="subtitle">Reflexiones sobre PHP asincrono, DDD y desarrollo web</p>
            </div>
            <div class="posts-grid">
                ${postsHtml}
            </div>
        `;
    }

    renderPostDetail() {
        const post = this.selectedPost;
        // Convert org-mode style content to HTML-ish
        const formattedBody = post.articleBody
            .split('\n')
            .map(line => {
                // Headers
                if (line.startsWith('* ')) {
                    return `<h2>${line.substring(2)}</h2>`;
                }
                if (line.startsWith('** ')) {
                    return `<h3>${line.substring(3)}</h3>`;
                }
                // Code blocks (simplified)
                if (line.startsWith('#+BEGIN_SRC')) {
                    return '<pre><code>';
                }
                if (line.startsWith('#+END_SRC')) {
                    return '</code></pre>';
                }
                // Empty lines
                if (line.trim() === '') {
                    return '<br>';
                }
                // Regular paragraphs
                return `<p>${line}</p>`;
            })
            .join('');

        return `
            <article class="post-detail">
                <button class="back-btn">&larr; Volver al blog</button>
                <header>
                    <h1>${post.headline}</h1>
                    <div class="post-meta">
                        <span class="author">Por ${post.author}</span>
                        <span class="separator">|</span>
                        <span class="date">${this.formatDate(post.datePublished)}</span>
                    </div>
                </header>
                <div class="post-content">
                    ${formattedBody}
                </div>
            </article>
        `;
    }

    render() {
        let content;

        if (this.loading) {
            content = this.renderLoading();
        } else if (this.error) {
            content = this.renderError();
        } else if (this.selectedPost) {
            content = this.renderPostDetail();
        } else {
            content = this.renderPostList();
        }

        this.shadowRoot.innerHTML = `
            <style>
                :host {
                    display: block;
                    font-family: 'Hasklig', 'Fira Code', monospace;
                    padding: 20px;
                    max-width: 900px;
                    margin: 0 auto;
                    color: var(--base, #333);
                }

                /* Loading */
                .loading {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    min-height: 300px;
                }

                .spinner {
                    width: 40px;
                    height: 40px;
                    border: 3px solid var(--bg3, #eee);
                    border-top-color: var(--keyword, #7c3aed);
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                }

                @keyframes spin {
                    to { transform: rotate(360deg); }
                }

                /* Error */
                .error, .empty {
                    text-align: center;
                    padding: 40px;
                }

                .error .icon, .empty .icon {
                    font-size: 48px;
                    margin-bottom: 20px;
                    color: var(--err, #e53e3e);
                }

                .empty .icon {
                    color: var(--base-dim, #666);
                }

                .retry-btn {
                    margin-top: 20px;
                    padding: 10px 20px;
                    background: var(--keyword, #7c3aed);
                    color: white;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    font-family: inherit;
                }

                .retry-btn:hover {
                    opacity: 0.9;
                }

                /* Posts Header */
                .posts-header {
                    margin-bottom: 30px;
                    border-bottom: 1px solid var(--border, #e2e8f0);
                    padding-bottom: 20px;
                }

                .posts-header h1 {
                    font-size: 2rem;
                    margin: 0 0 10px 0;
                    color: var(--head1, #1a202c);
                }

                .subtitle {
                    color: var(--base-dim, #666);
                    margin: 0;
                }

                /* Posts Grid */
                .posts-grid {
                    display: grid;
                    gap: 20px;
                }

                /* Post Card */
                .post-card {
                    background: var(--bg2, #f7fafc);
                    border: 1px solid var(--border, #e2e8f0);
                    border-radius: 8px;
                    padding: 20px;
                    cursor: pointer;
                    transition: transform 0.2s, box-shadow 0.2s;
                }

                .post-card:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                }

                .post-title {
                    font-size: 1.25rem;
                    margin: 0 0 10px 0;
                    color: var(--head2, #2d3748);
                }

                .post-meta {
                    font-size: 0.85rem;
                    color: var(--base-dim, #666);
                    margin-bottom: 15px;
                }

                .post-meta .author {
                    color: var(--func, #805ad5);
                }

                .post-meta .date {
                    margin-left: 10px;
                }

                .post-excerpt {
                    color: var(--base, #4a5568);
                    line-height: 1.6;
                    margin: 0 0 15px 0;
                }

                .read-more {
                    color: var(--keyword, #7c3aed);
                    font-size: 0.9rem;
                    font-weight: 500;
                }

                /* Post Detail */
                .post-detail {
                    max-width: 700px;
                    margin: 0 auto;
                }

                .back-btn {
                    background: none;
                    border: none;
                    color: var(--keyword, #7c3aed);
                    cursor: pointer;
                    font-family: inherit;
                    font-size: 1rem;
                    padding: 10px 0;
                    margin-bottom: 20px;
                }

                .back-btn:hover {
                    text-decoration: underline;
                }

                .post-detail header {
                    margin-bottom: 30px;
                    border-bottom: 1px solid var(--border, #e2e8f0);
                    padding-bottom: 20px;
                }

                .post-detail h1 {
                    font-size: 2rem;
                    margin: 0 0 15px 0;
                    color: var(--head1, #1a202c);
                    line-height: 1.3;
                }

                .post-detail .post-meta {
                    font-size: 0.9rem;
                }

                .separator {
                    margin: 0 10px;
                    color: var(--border, #e2e8f0);
                }

                .post-content {
                    line-height: 1.8;
                    color: var(--base, #4a5568);
                }

                .post-content h2 {
                    font-size: 1.5rem;
                    color: var(--head2, #2d3748);
                    margin: 30px 0 15px 0;
                }

                .post-content h3 {
                    font-size: 1.25rem;
                    color: var(--head3, #4a5568);
                    margin: 25px 0 10px 0;
                }

                .post-content p {
                    margin: 0 0 15px 0;
                }

                .post-content pre {
                    background: var(--bg3, #1a202c);
                    color: var(--base, #e2e8f0);
                    padding: 15px;
                    border-radius: 4px;
                    overflow-x: auto;
                    font-size: 0.9rem;
                }

                .post-content code {
                    font-family: 'Hasklig', 'Fira Code', monospace;
                }

                /* Responsive */
                @media (max-width: 768px) {
                    :host {
                        padding: 15px;
                    }

                    .posts-header h1 {
                        font-size: 1.5rem;
                    }

                    .post-title {
                        font-size: 1.1rem;
                    }

                    .post-detail h1 {
                        font-size: 1.5rem;
                    }
                }
            </style>
            ${content}
        `;

        // Add event listeners
        this.addEventListeners();
    }

    addEventListeners() {
        // Post card click
        this.shadowRoot.querySelectorAll('.post-card').forEach(card => {
            card.addEventListener('click', () => {
                const index = parseInt(card.dataset.index);
                this.selectPost(this.posts[index]);
            });
        });

        // Back button
        const backBtn = this.shadowRoot.querySelector('.back-btn');
        if (backBtn) {
            backBtn.addEventListener('click', () => this.goBack());
        }

        // Retry button
        const retryBtn = this.shadowRoot.querySelector('.retry-btn');
        if (retryBtn) {
            retryBtn.addEventListener('click', () => {
                this.loading = true;
                this.error = null;
                this.render();
                this.fetchPosts();
            });
        }
    }
}

customElements.define('pascualmg-blog', PascualmgBlog);
export default PascualmgBlog;
