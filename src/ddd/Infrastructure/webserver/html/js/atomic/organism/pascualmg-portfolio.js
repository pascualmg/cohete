import TechnologiesList from "../molecule/technologiesList.js";
import ChatBox from "./ChatBox.js";
import PortFolioHeader from "../molecule/PortfolioHeader.js"
import ThemeToggler from "./ThemeToogler.js"
import ExperienceTimeline from "../molecule/ExperienceTimeline.js"
import SocialLinks from "../molecule/SocialLinks.js"
import AboutThisPortfolio from "../molecule/AboutThisPortfolio.js";
import YastModal from "../atom/YastModal.js";
import SoftSkills from "../molecule/SoftSkills.js";

class PortfolioElement extends HTMLElement {
  constructor() {
    super();
    this.attachShadow({ mode: 'open' });

  }
  connectedCallback() {
    this.shadowRoot.innerHTML = `
            <style>
                :host {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    gap: 20px;
                    max-width: 900px;
                    margin: 0 auto;
                    padding: 10px;
                    width: 100%;
                    box-sizing: border-box;
                }

                :host > * {
                    width: 100%;
                }

                .about-this-portfolio-container {
                    display: flex;
                    justify-content: center;
                    width: 100%;
                }

                @media (min-width: 768px) {
                    :host {
                        gap: 25px;
                        padding: 15px;
                    }
                }
            </style>
           <portfolio-header></portfolio-header>

  <social-links links='[
         {"name": "phone", "url": "tel:+34693000983"},
         {"name": "email", "url": "mailto:info@pascualmg.dev"},
         {"name": "linkedin", "url": "https://www.linkedin.com/in/pascual-mu%C3%B1oz-gali%C3%A1n-7b4b3896/"},
         {"name": "github", "url": "https://github.com/pascualmg"},
         {"name": "twitter", "url": "https://twitter.com/pascualmg"}
         ]'>
  </social-links>

<experience-timeline  data='[
  {
    "company": "UST (para Vocento)",
    "position": "Backend PHP/Symfony Engineer - Identity Team",
    "startDate": "2024-07-01",
    "projects": [
      {
        "name": "Webhooks 2.0 - Data Hub Event-Driven",
        "role": "Backend Developer / Arquitecto",
        "highlights": [
          "Reescritura desde cero de sistema legacy: ingesta, procesamiento batch y sincronización outbound",
          "Idempotencia E2E con timestamps duales, descubierta analizando 410.000+ webhooks reales en producción",
          "Patrón Claim-Then-Process con memoria estable (~50-80MB) procesando 93.000+ eventos sin OOM",
          "15 ADRs documentados con contexto, decisión y consecuencias"
        ],
        "technologies": ["PHP 8.1", "Symfony 6", "MongoDB 6", "DDD", "Domain Events", "RabbitMQ", "Behat ATDD"],
        "achievements": [
          "490.000+ webhooks procesados en DES con 0 errores",
          "Migración coordinada de MongoDB 3.6 a 6"
        ]
      },
      {
        "name": "Migración Legacy + Developer Experience",
        "role": "Backend Developer",
        "highlights": [
          "Migración de endpoints legacy al nuevo core de identidad con paridad 100% validada por ATDD cruzado (Behat)",
          "Documentación API auto-generada con Swagger para +100 servidores",
          "Sistema de distribución de conocimiento técnico via ficheros MD unificados en repo agnóstico como best practice para el equipo"
        ],
        "technologies": ["PHP", "Symfony", "Kubernetes", "Swagger", "Nix Flakes", "Behat"],
        "achievements": [
          "Deprecación completa de servicios legacy, 6+ releases en 3 meses",
          "Entornos de desarrollo reproducibles con Nix Flakes para proyectos PHP 7.1 y 8.1"
        ]
      }
    ]
  },
  {
    "company": "Proyecto Personal",
    "position": "Creador de Cohete Framework",
    "startDate": "2023-05-24",
    "projects": [
      {
        "name": "Cohete - Framework PHP Asíncrono",
        "role": "Creador y Líder Técnico",
        "highlights": [
          "Framework PHP asíncrono sobre ReactPHP con observables RxPHP y arquitectura DDD",
          "Servidor HTTP non-blocking, single-process: HTTP + MCP en el mismo event loop",
          "En producción en pascualmg.dev: portfolio, blog multi-autor y servidor MCP integrado"
        ],
        "technologies": ["PHP", "ReactPHP", "RxPHP", "DDD", "CQRS", "MySQL"],
        "achievements": [
          "~3000 líneas que sirven portfolio SPA, blog con autores y API REST completa",
          "Primer framework PHP con servidor MCP (Model Context Protocol) nativo integrado"
        ]
      },
      {
        "name": "Blog Cohete - Plataforma Multi-Autor (Humanos + IAs)",
        "role": "Creador",
        "highlights": [
          "Blog donde humanos e IAs publican juntos con sistema de autenticación por claim tokens",
          "Servidor MCP/SSE integrado: cualquier agente IA puede publicar, leer y comentar",
          "Frontend con Web Components nativos (Atomic Design) y temas Spacemacs",
          "Indexado por Google como framework PHP asíncrono"
        ],
        "technologies": ["PHP", "ReactPHP", "MCP", "SSE", "Web Components", "DDD"],
        "achievements": [
          "Primer blog donde IAs externas publican de forma autónoma via MCP",
          "UX diseñada para humanos y agentes IA por igual"
        ]
      }
    ]
  },
  {
    "company": "Grupo Euroformac",
    "position": "Formador en Desarrollo Web",
    "startDate": "2024-06-14",
    "endDate": "2024-06-28",
    "projects": [
      {
        "name": "Experto Web y Multimedia para E-Commerce",
        "role": "Profesor",
        "highlights": [
          "Formación en PHP, JavaScript, HTML5 y CSS a profesionales del sector"
        ],
        "technologies": ["PHP", "JavaScript", "HTML5", "CSS"]
      }
    ]
  },
  {
    "company": "PcComponentes",
    "position": "Software Engineer",
    "startDate": "2013-01-05",
    "endDate": "2023-05-23",
    "projects": [
      {
        "name": "Microservicios y APIs Backend",
        "role": "Backend Developer / Líder Técnico",
        "highlights": [
          "Desarrollo de microservicios API RESTful con Symfony y DDD",
          "Gestión de contenido de usuarios con eventos asíncronos y proyecciones",
          "Integración de APIs de terceros (Swogo, Connectif, Salesmanago)",
          "Automatización de procesos de Postventa y Atención al Cliente con RabbitMQ"
        ],
        "technologies": ["Symfony", "DDD", "RabbitMQ", "API REST"]
      },
      {
        "name": "Modernización de Legacy y ERP",
        "role": "Desarrollador Senior",
        "highlights": [
          "Refactorización de código legacy aplicando DDD y TDD",
          "Implementación de tests con PHPUnit y Behat",
          "Optimización del buscador con Doofinder"
        ],
        "technologies": ["PHPUnit", "Behat", "DDD", "TDD", "Doofinder"]
      },
      {
        "name": "Frontend y Configurador de Equipos",
        "role": "Desarrollador Frontend",
        "highlights": [
          "Desarrollo frontend con JavaScript, TypeScript y React",
          "Configurador de equipos a medida con sistema de reglas de compatibilidad",
          "Campañas de Black Friday con lógica de negocio compleja"
        ],
        "technologies": ["JavaScript", "TypeScript", "React"]
      }
    ]
  },
  {
    "company": "PcComponentes",
    "position": "Analista y Gestor",
    "startDate": "2012-09-05",
    "endDate": "2013-01-04",
    "projects": [
      {
        "name": "Auditoría y Compras Internacionales",
        "role": "Analista",
        "highlights": [
          "Sistema de tracking con Qlik para monitoreo de operadores",
          "Gestión de relaciones con proveedores internacionales"
        ],
        "technologies": ["Qlik", "SQL", "ERP"]
      }
    ]
  }
]'></experience-timeline>

 <technologies-list images='[
    {"src": "https://raw.githubusercontent.com/NixOS/nixos-artwork/53ea652ec7d8af5d21fd2b79b6c49cb39078ddfb/logo/nixos.svg", "alt": "nixos"},
    {"src": "https://upload.wikimedia.org/wikipedia/commons/7/7f/Haskell-logo2.svg", "alt": "haskell"},
    {"src": "https://chuidiang.org/images/f/f3/Logo_php.png", "alt": "php"},
    {"src": "https://upload.wikimedia.org/wikipedia/commons/a/ae/Github-desktop-logo-symbol.svg", "alt": "github"},
    {"src": "https://raw.githubusercontent.com/reactphp/branding/main/reactphp-logo.svg", "alt": "reactphp"},
    {"src": "https://symfony.com/logos/symfony_black_03.png", "alt": "symfony"},
    {"src": "https://upload.wikimedia.org/wikipedia/commons/9/99/Unofficial_JavaScript_logo_2.svg", "alt": "javascript"},
    {"src": "https://upload.wikimedia.org/wikipedia/commons/6/61/HTML5_logo_and_wordmark.svg", "alt": "html5"},
    {"src": "https://upload.wikimedia.org/wikipedia/commons/d/d5/CSS3_logo_and_wordmark.svg", "alt": "css3"}
]'></technologies-list>

 <soft-skills
        skills='[
          "Trabajo muy bien en equipo.",
          "Tengo una alta disposición para aprender nuevos lenguajes y tecnologías.",
          "Buen comunicador con compañeros y clientes no técnicos",
          "Capacidad para encontrar formas creativas de resolver problemas técnicos y adaptarse a nuevos desafíos",
          "Autodidacta y entusiasta de la programación",
          "Creador de mi propio framework para crear API RESTful asíncronas con ReactPHP."
        ]'

      ></soft-skills>

<div class="about-this-portfolio-container">
<yast-modal button-text="Que tiene de especial este Portfolio" class="custom-button">
    <about-this-portfolio slot="content"></about-this-portfolio>
</yast-modal>
</div>
        `;
  }
}

customElements.define('pascualmg-portfolio', PortfolioElement);
export default PortfolioElement
