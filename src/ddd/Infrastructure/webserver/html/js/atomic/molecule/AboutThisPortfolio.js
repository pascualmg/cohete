
class AboutThisPortfolio extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        this.shadowRoot.innerHTML = `
      <style>
        :host {
          display: block;
          font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
          background: var(--bg1, #0a0a0a);
          color: var(--base, #ffffff);
          margin: 20px;
          padding: 30px;
          border-radius: 20px;
          border: 2px solid var(--border, #444444);
          box-shadow: 0 20px 60px rgba(0, 255, 136, 0.2);
          animation: fadeIn 0.5s ease-out;
        }
        h2 {
          color: var(--head1, #ffffff);
          text-align: center;
          font-size: 2em;
          margin-bottom: 30px;
          text-transform: uppercase;
          letter-spacing: 2px;
          border-bottom: 2px solid var(--green, #00ff88);
          padding-bottom: 10px;
        }
        .highlight {
          color: var(--green, #00ff88);
          font-weight: bold;
          text-shadow: 0 0 10px rgba(0, 255, 136, 0.3);
        }
        .tech-stack {
          display: flex;
          justify-content: center;
          flex-wrap: wrap;
          margin: 30px 0;
          gap: 10px;
        }
        .tech-item {
          background: linear-gradient(135deg, var(--green, #00ff88), var(--comp, #ff6b6b));
          color: var(--bg1, #0a0a0a);
          padding: 10px 20px;
          border-radius: 25px;
          font-size: 0.9em;
          font-weight: bold;
          transition: all 0.3s ease;
          border: 2px solid var(--border, #444444);
          box-shadow: 0 4px 15px rgba(0, 255, 136, 0.3);
        }
        .tech-item:hover {
          transform: translateY(-5px) scale(1.05);
          box-shadow: 0 8px 25px rgba(0, 255, 136, 0.5);
        }
        .cta {
          text-align: center;
          margin-top: 30px;
          font-style: italic;
          line-height: 1.6;
          color: var(--base-dim, #cccccc);
          padding: 20px;
          background: var(--head2-bg, rgba(255, 255, 255, 0.1));
          border-radius: 15px;
          border: 1px solid var(--border, #444444);
        }
        .feature-list {
          list-style-type: none;
          padding-left: 0;
        }
        .feature-list li {
          margin-bottom: 15px;
          position: relative;
          padding-left: 30px;
          transition: all 0.3s ease;
          color: var(--base, #ffffff);
        }
        .feature-list li:hover {
          transform: translateX(5px);
          color: var(--green, #00ff88);
        }
        .feature-list li::before {
          content: '🚀';
          position: absolute;
          left: 0;
          color: var(--comp, #ff6b6b);
        }
        p {
          line-height: 1.6;
          color: var(--base, #ffffff);
        }
        strong {
          color: var(--head1, #ffffff);
          font-size: 1.1em;
        }
        @keyframes fadeIn {
          from { opacity: 0; transform: translateY(20px); }
          to { opacity: 1; transform: translateY(0); }
        }
      </style>
      <h2>Sobre Este Portfolio</h2>
      <p>
        ¡Bienvenido a mi portfolio único! Este no es solo otro sitio web basado en plantillas. Es un <span class="highlight">proyecto full-stack completamente personalizado</span> construido sobre mi propio framework PHP asíncrono llamado <span class="highlight">Cohete</span>.
      </p>
      <p>
        Lo que hace especial a este portfolio:
      </p>
      <ul class="feature-list">
        <li>Powered by <span class="highlight">Cohete Framework</span> - mi framework PHP asíncrono creado con ReactPHP</li>
        <li>Backend asíncrono que maneja peticiones de forma no bloqueante para mejor performance</li>
        <li>Arquitectura <span class="highlight">Domain-Driven Design (DDD)</span> para un código limpio y mantenible</li>
        <li>Frontend modular usando <span class="highlight">Web Components</span> nativos con patrón Atomic Design</li>
        <li>Sistema de routing personalizado y gestión de contenido estático integrada</li>
        <li>Sin dependencias de frameworks frontend - puro JavaScript vanilla y Web Components</li>
        <li>Da igual los años y los frameworks que se pasen o se pongan de moda, estos componentes seguirán funcionando igual de bien :)</li>
      </ul>
      <div class="tech-stack">
        <span class="tech-item">Cohete Framework</span>
        <span class="tech-item">ReactPHP</span>
        <span class="tech-item">DDD Architecture</span>
        <span class="tech-item">Web Components</span>
        <span class="tech-item">Async PHP</span>
        <span class="tech-item">Atomic Design</span>
        <span class="tech-item">JavaScript Vanilla</span>
      </div>
      <p class="cta">
        ¡Abre las herramientas de desarrollo de tu navegador y explora el código! El repositorio está disponible en GitHub para que veas cómo funciona Cohete por dentro.
        <br><br>
        <strong>Este portfolio ES el framework Cohete en acción.</strong>
      </p>
    `;
    }
}

customElements.define('about-this-portfolio', AboutThisPortfolio);

export default AboutThisPortfolio;
