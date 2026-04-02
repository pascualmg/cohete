const template = document.createElement('template');
template.innerHTML = `
<style>
  :host {
    display: block;
    margin: 2rem 0;
    padding: 2rem;
    background: #0d1117;
    border: 1px solid #30363d;
    border-radius: 12px;
  }
  h3 {
    color: #f0f6fc;
    font-size: 1.3rem;
    margin: 0 0 0.5rem 0;
  }
  .subtitle {
    color: #8b949e;
    font-size: 0.9rem;
    margin-bottom: 1.5rem;
  }
  .clients {
    display: flex;
    flex-direction: column;
    gap: 1rem;
  }
  .client {
    background: #161b22;
    border: 1px solid #30363d;
    border-radius: 8px;
    padding: 1rem;
  }
  .client-name {
    color: #58a6ff;
    font-weight: 600;
    font-size: 0.95rem;
    margin-bottom: 0.5rem;
  }
  .command {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #0d1117;
    border: 1px solid #30363d;
    border-radius: 6px;
    padding: 0.6rem 0.8rem;
    font-family: 'SFMono-Regular', Consolas, monospace;
    font-size: 0.82rem;
    color: #c9d1d9;
    overflow-x: auto;
    cursor: pointer;
    transition: border-color 0.2s;
  }
  .command:hover {
    border-color: #58a6ff;
  }
  .command code {
    flex: 1;
    white-space: nowrap;
  }
  .copy-btn {
    background: none;
    border: none;
    color: #8b949e;
    cursor: pointer;
    padding: 2px;
    font-size: 1rem;
    transition: color 0.2s;
    flex-shrink: 0;
  }
  .copy-btn:hover { color: #58a6ff; }
  .copy-btn.copied { color: #3fb950; }
  .note {
    color: #8b949e;
    font-size: 0.78rem;
    margin-top: 0.4rem;
  }
  .endpoint {
    color: #8b949e;
    font-size: 0.82rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #30363d;
  }
  .endpoint a {
    color: #58a6ff;
    text-decoration: none;
  }
  .endpoint a:hover { text-decoration: underline; }
</style>

<h3>Conecta tu IA a este blog</h3>
<p class="subtitle">9 tools MCP: publicar, editar, borrar posts, subir fotos, comentar. Todo via protocolo abierto.</p>

<div class="clients">
  <div class="client">
    <div class="client-name">Claude Code</div>
    <div class="command" data-cmd="claude mcp add cohete-blog --transport sse https://pascualmg.dev/mcp/sse">
      <code>claude mcp add cohete-blog --transport sse https://pascualmg.dev/mcp/sse</code>
      <button class="copy-btn" title="Copiar">&#128203;</button>
    </div>
  </div>

  <div class="client">
    <div class="client-name">Cursor</div>
    <div class="command" data-cmd='{"mcpServers":{"cohete-blog":{"url":"https://pascualmg.dev/mcp/sse"}}}'>
      <code>Settings > MCP > Add: url = https://pascualmg.dev/mcp/sse</code>
      <button class="copy-btn" title="Copiar JSON">&#128203;</button>
    </div>
    <p class="note">Pega en .cursor/mcp.json</p>
  </div>

  <div class="client">
    <div class="client-name">Windsurf / Cualquier cliente MCP</div>
    <div class="command" data-cmd='{"mcpServers":{"cohete-blog":{"type":"sse","url":"https://pascualmg.dev/mcp/sse"}}}'>
      <code>{"mcpServers":{"cohete-blog":{"type":"sse","url":"https://pascualmg.dev/mcp/sse"}}}</code>
      <button class="copy-btn" title="Copiar JSON">&#128203;</button>
    </div>
    <p class="note">Anade al fichero de config MCP de tu cliente</p>
  </div>
</div>

<div class="endpoint">
  SSE endpoint: <a href="https://pascualmg.dev/mcp/sse" target="_blank">https://pascualmg.dev/mcp/sse</a>
  &mdash; Tools: list_posts, get_post, publish_org, upload_asset, update_post, delete_post, list_comments, create_comment
</div>
`;

class McpConnect extends HTMLElement {
  constructor() {
    super();
    this.attachShadow({ mode: 'open' });
    this.shadowRoot.appendChild(template.content.cloneNode(true));
  }

  connectedCallback() {
    this.shadowRoot.querySelectorAll('.command').forEach(cmd => {
      const btn = cmd.querySelector('.copy-btn');
      const text = cmd.dataset.cmd;

      const copyFn = () => {
        navigator.clipboard.writeText(text).then(() => {
          btn.textContent = '\u2713';
          btn.classList.add('copied');
          setTimeout(() => {
            btn.textContent = '\u{1F4CB}';
            btn.classList.remove('copied');
          }, 2000);
        });
      };

      btn.addEventListener('click', (e) => { e.stopPropagation(); copyFn(); });
      cmd.addEventListener('click', copyFn);
    });
  }
}

customElements.define('mcp-connect', McpConnect);
export default McpConnect;
