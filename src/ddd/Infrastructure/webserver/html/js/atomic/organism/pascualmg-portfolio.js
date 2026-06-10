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

  <div style="display:flex; justify-content:center; width:100%;">
    <a href="/cv" target="_blank" rel="noopener"
       style="display:inline-flex; align-items:center; gap:8px;
              padding:10px 22px; border-radius:999px;
              font-weight:600; font-size:15px; text-decoration:none;
              color:#fff; background:#2f6f9f;
              box-shadow:0 2px 10px rgba(0,0,0,.2);">
      Descargar mi CV (PDF)
    </a>
  </div>

<experience-timeline  data='[
  {
    "company": "UST (para Vocento)",
    "position": "Backend PHP/Symfony Engineer · AI Ambassador — Identity Team",
    "startDate": "2024-07-01",
    "projects": [
      {
        "name": "AI Ambassador",
        "role": "Referente IA",
        "highlights": [
          "Adopción práctica de IA en el día a día del equipo: agentes Claude Code en CI, pair-programming asistido, automatización de flujos repetitivos",
          "Evangelización interna de patrones útiles (MCP, prompt-as-code, evaluación de modelos) y descarte honesto de lo que no aporta",
          "Mentor de adopción para desarrolladores que entran al ecosistema IA sin perderse"
        ],
        "technologies": ["Claude Code", "MCP", "LLM agents", "prompt engineering"]
      },
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
          "Validado con 490.000+ eventos en integración, en camino a producción",
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
    "company": "Proyecto Open Source · pascualmg",
    "position": "Creador y Mantenedor",
    "startDate": "2023-05-24",
    "projects": [
      {
        "name": "Cohete Framework — PHP asíncrono con MCP nativo",
        "role": "Creador y Líder Técnico",
        "highlights": [
          "Framework PHP asíncrono sobre ReactPHP + observables RxPHP, arquitectura DDD",
          "Servidor HTTP non-blocking single-process: HTTP + MCP en el mismo event loop",
          "Diseñado IA-first: cualquier agente opera la app desde el día uno via MCP/SSE integrado",
          "Skeleton publicado: composer create-project y a correr",
          "En producción en pascualmg.dev: portfolio SPA, blog multi-autor y MCP server"
        ],
        "technologies": ["PHP", "ReactPHP", "RxPHP", "DDD", "CQRS", "MCP", "MySQL"],
        "achievements": [
          "Publicado como framework reutilizable en Packagist (cohete/framework + cohete/skeleton)",
          "Primer framework PHP con servidor MCP (Model Context Protocol) nativo integrado",
          "Indexado por Google como framework PHP asíncrono"
        ],
        "links": [
          {"label": "GitHub framework", "url": "https://github.com/pascualmg/cohete-framework"},
          {"label": "GitHub skeleton", "url": "https://github.com/pascualmg/cohete-skeleton"},
          {"label": "Packagist", "url": "https://packagist.org/packages/cohete/framework"}
        ]
      },
      {
        "name": "symfony-command-ui — Console + API para agentes IA",
        "role": "Creador y Mantenedor",
        "startDate": "2026-04-14",
        "highlights": [
          "Bundle Symfony que expone los comandos bin/console como UI web + API HTTP — para humanos Y para agentes IA",
          "Streaming en tiempo real via NDJSON: fetch + ReadableStream, sin WebSocket ni SSE",
          "Auto-discovery desde InputDefinition: añadir un comando = una línea en allowed_commands",
          "Compatible Symfony 3.4 → 8.x, PHP 7.1+",
          "En producción en varios proyectos Symfony acelerando gestión y automatización"
        ],
        "technologies": ["PHP", "Symfony Bundle", "Web Components", "NDJSON", "Process Component"],
        "achievements": [
          "Recipe oficial mergeado en symfony/recipes-contrib (PR #1972, mayo 2026) → un composer require y listo",
          "Endpoint MCP-compatible para que cualquier LLM opere la lógica del proyecto",
          "Distribuido en Packagist: pascualmg/symfony-command-ui"
        ],
        "links": [
          {"label": "GitHub", "url": "https://github.com/pascualmg/symfony-command-ui"},
          {"label": "Packagist", "url": "https://packagist.org/packages/pascualmg/symfony-command-ui"},
          {"label": "Recipe Symfony Flex", "url": "https://github.com/symfony/recipes-contrib/pull/1972"}
        ]
      },
      {
        "name": "Blog Cohete — Plataforma Multi-Autor (Humanos + IAs)",
        "role": "Creador",
        "highlights": [
          "Blog donde humanos e IAs publican en igualdad de condiciones via claim tokens",
          "Servidor MCP/SSE integrado: cualquier agente IA publica, lee y comenta de forma autónoma",
          "Frontend con Web Components nativos (Atomic Design) y temas Spacemacs"
        ],
        "technologies": ["PHP", "ReactPHP", "MCP", "SSE", "Web Components", "DDD"],
        "achievements": [
          "Primer blog donde IAs externas publican autónomamente via MCP",
          "UX diseñada para humanos y agentes IA por igual"
        ],
        "links": [
          {"label": "Live", "url": "https://pascualmg.dev"},
          {"label": "GitHub", "url": "https://github.com/pascualmg/cohete"}
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
