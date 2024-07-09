
class AboutThisPortfolio extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }

    connectedCallback() {
        this.shadowRoot.innerHTML = `
      <style>
        @import url('https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&family=Merriweather:wght@300;400&display=swap');
        
        :host {
          display: block;
          font-family: 'Merriweather', serif;
          background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
          color: #333;
          margin: 20px;
          padding: 30px;
          border-radius: 15px;
          box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        h2 {
          font-family: 'Roboto', sans-serif;
          color: #2c3e50;
          text-align: center;
          font-size: 2em;
          margin-bottom: 30px;
          text-transform: uppercase;
          letter-spacing: 2px;
        }
        .highlight {
          color: #e74c3c;
          font-weight: bold;
        }
        .tech-stack {
          display: flex;
          justify-content: center;
          flex-wrap: wrap;
          margin: 30px 0;
        }
        .tech-item {
          background-color: #3498db;
          color: white;
          padding: 10px 20px;
          margin: 5px;
          border-radius: 25px;
          font-size: 0.9em;
          transition: all 0.3s ease;
          font-family: 'Roboto', sans-serif;
        }
        .tech-item:hover {
          transform: translateY(-5px);
          box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        .cta {
          text-align: center;
          margin-top: 30px;
          font-style: italic;
          line-height: 1.6;
          color: #7f8c8d;
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
        }
        .feature-list li:hover {
          transform: translateX(5px);
        }
        .feature-list li::before {
          content: '➤';
          position: absolute;
          left: 0;
          color: #e74c3c;
        }
        @keyframes fadeIn {
          from { opacity: 0; transform: translateY(20px); }
          to { opacity: 1; transform: translateY(0); }
        }
        :host {
          animation: fadeIn 0.5s ease-out;
        }
      </style>
      <h2>Sobre Este Portfolio</h2>
      <p>
        ¡Bienvenido a mi portfolio único! Este no es solo otro sitio web basado en plantillas. Es un <span class="highlight">proyecto full-stack personalizado</span> que muestra mis habilidades desde el backend hasta el frontend.
      </p>
      <p>
        Lo que hace especial a este portfolio:
      </p>
      <ul class="feature-list">
        <li>Construido completamente desde cero, sin depender de frameworks existentes</li>
        <li>Utiliza modernos <span class="highlight">Web Components</span> para un frontend modular y eficiente</li>
        <li>Implementación de backend personalizada para una creación verdaderamente integral</li>
        <li>Demuestra dominio tanto en tecnologías de backend como de frontend</li>
        <li>Diseñado con un enfoque en la accesibilidad y el rendimiento</li>
      </ul>
      <div class="tech-stack">
        <span class="tech-item">Web Components</span>
        <span class="tech-item">CSS Personalizado</span>
        <span class="tech-item">JavaScript Vanilla</span>
        <span class="tech-item">Backend a Medida</span>
        <span class="tech-item">Diseño Responsivo</span>
      </div>
      <p class="cta">
        ¿Curioso sobre cómo funciona todo esto? ¡Abre las herramientas de desarrollo de tu navegador y explora el código! Me encantaría conocer tus pensamientos y feedback. 
        <br><br>
        Si eres reclutador o colega desarrollador, no dudes en contactarme para discutir más sobre las decisiones técnicas y el proceso de desarrollo.
      </p>
    `;
    }
}

customElements.define('about-this-portfolio', AboutThisPortfolio);

export default AboutThisPortfolio;