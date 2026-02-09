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
                /* Estilos generales */
                body {
                    background-color: var(--bg1);
                    color: var(--base);
                    font-family: Arial, sans-serif;
                }

                header {
                    background-color: var(--bg1);
                    padding: 10px;
                    color: var(--head1);
                    text-align: center;
                }

                h2 {
                color: var(--head2)
                }
                h3 {
                    color: var(--head3)
                }

                section {
                background: linear-gradient( var(--bg1), var(--base-dim));
                    color: var(--head2);
                    margin: 10px;
                    padding: 10px;
                    border-radius: 10px;
                    border: 1px solid var(--border);
                }
 
                .about-this-portfolio-container {
                    display: flex;
                    justify-content: center;
                    margin: 20px;
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
    "position": "Backend PHP/Symfony Engineer",
    "startDate": "2024-07-01",
    "projects": [
      {
        "name": "APIs de Contenido para Medios Digitales",
        "role": "Backend Developer",
        "highlights": [
          "Desarrollo y mantenimiento de APIs RESTful para servir contenido a los diferentes medios del grupo Vocento",
          "Refactorización y modernización de código legacy para mejorar mantenibilidad y rendimiento",
          "Optimización de rendimiento en endpoints de alto tráfico"
        ],
        "technologies": ["PHP", "Symfony", "REST APIs", "MySQL"],
        "achievements": [
          "Mejora del rendimiento y escalabilidad de las APIs de contenido",
          "Modernización de código legacy aplicando buenas prácticas y patrones de diseño"
        ]
      },
      {
        "name": "Sistema de Identidad y Autenticación OAuth2",
        "role": "Backend Developer",
        "highlights": [
          "Implementación de servidor OAuth2 con League OAuth2 Server para proteger APIs internas",
          "Diseño del flujo de autenticación y autorización entre servicios del grupo",
          "Integración del sistema de identidad con las plataformas existentes"
        ],
        "technologies": ["PHP", "Symfony", "League OAuth2 Server", "OAuth2"],
        "achievements": [
          "Securización de las APIs internas del grupo con estándar OAuth2",
          "Implementación robusta de autenticación con League OAuth2 Server"
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
    "company": "I+D Personal",
    "position": "Creador de Cohete Framework",
    "startDate": "2023-05-24",
    "endDate": "2024-06-13",
    "projects": [
      {
        "name": "Cohete - Framework PHP Asíncrono",
        "role": "Creador y Líder Técnico",
        "highlights": [
          "Framework PHP asíncrono sobre ReactPHP con observables RxPHP",
          "Arquitectura DDD con servidor HTTP non-blocking",
          "Este portfolio corre sobre Cohete"
        ],
        "technologies": ["PHP", "ReactPHP", "RxPHP", "DDD"]
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
        `;
  }
}

customElements.define('pascualmg-portfolio', PortfolioElement);
export default PortfolioElement
