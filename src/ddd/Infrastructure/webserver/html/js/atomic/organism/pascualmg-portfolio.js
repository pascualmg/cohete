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
         {"name": "facebook", "url": "https://www.linkedin.com/in/pascual-mu%C3%B1oz-gali%C3%A1n-7b4b3896/"},
         {"name": "github", "url": "https://github.com/pascualmg"},
         {"name": "twitter", "url": "https://twitter.com/pascualmg"}
         ]'>
  </social-links>

<!-- IMPORTANTE: No usar comillas simples (') dentro de los strings JSON ya que el atributo data usa comillas simples -->
<experience-timeline  data='[
  {
    "company": "UST",
    "position": "PHP Software Engineer",
    "startDate": "2024-06-29",
    "endDate": "Actualidad",
    "projects": [
      {
        "name": "Sistema de Identidad OAuth2 para Vocento",
        "role": "Desarrollador Backend",
        "highlights": [
          "Colaboré en la implementación del flujo OAuth2 completo para el sistema de identidad",
          "Participé en el desarrollo de la integración que permite a empresas terceras acceder a información de usuarios",
          "Contribuí al sistema de autorización para impactar a usuarios de forma controlada",
          "Trabajé directamente con el cliente Vocento para mejorar su arquitectura de identidad"
        ],
        "technologies": ["OAuth2", "PHP", "Symfony", "API RESTful", "Sistemas de Identidad"],
        "achievements": [
          "Implementación exitosa del flujo OAuth2 para terceros",
          "Mejora en la arquitectura del sistema de identidad de Vocento"
        ]
      },
      {
        "name": "Modernización de Sistemas Legacy con DDD",
        "role": "Desarrollador Backend",
        "highlights": [
          "Colaboré en la implementación de Domain-Driven Design en códigos legacy existentes",
          "Participé en la extracción de lógicas de negocio desde sistemas monolíticos legacy",
          "Contribuí definiendo features usando Gherkin para documentar comportamientos de negocio",
          "Formé parte del equipo que migró lógica legacy a proyectos DDD con mejor arquitectura",
          "Ayudé a establecer patrones para la coexistencia entre sistemas legacy y modernos"
        ],
        "technologies": ["DDD", "Gherkin", "BDD", "PHP", "Symfony", "Behat"],
        "achievements": [
          "Migración exitosa de módulos críticos desde arquitectura legacy a DDD",
          "Mejora en mantenibilidad y testabilidad del código",
          "Documentación completa de reglas de negocio usando Gherkin"
        ]
      },
      {
        "name": "Configuración de Entornos de Desarrollo con NIX",
        "role": "Desarrollador Backend",
        "highlights": [
          "Me adapté a las necesidades del proyecto configurando entornos reproducibles con NIX para gestionar más de 25 microservicios",
          "Asumí la responsabilidad de resolver la gestión compleja de dependencias entre servicios",
          "Facilité la puesta en marcha de entornos locales complejos cuando el equipo lo necesitaba",
          "Utilicé herramientas de IA de forma práctica para resolver desafíos técnicos fuera de mi área habitual",
          "Documenté procesos y troubleshooting para que el equipo pudiera ser autónomo"
        ],
        "technologies": ["NIX", "Docker", "Microservicios", "Bash", "Problem Solving", "IA como herramienta"],
        "achievements": [
          "Resolución exitosa de necesidades técnicas fuera del desarrollo tradicional",
          "Mejora significativa en la productividad del equipo de desarrollo",
          "Demostración de capacidad de adaptación a tecnologías nuevas según necesidades del proyecto"
        ]
      }
    ]
  },
  {
    "company": "GRUPO EUROFORMAC",
    "position": "Formador Técnico PHP",
    "startDate": "2024-06-14",
    "endDate": "2024-06-28",
    "projects": [
      {
        "name": "Formación Especializada en Desarrollo Web y E-Commerce",
        "role": "Instructor de Tecnologías Web",
        "highlights": [
          "Impartí formación técnica especializada en desarrollo web moderno",
          "Diseñé contenido práctico enfocado en tecnologías actuales para e-commerce",
          "Desarrollé ejercicios hands-on para facilitar el aprendizaje de conceptos complejos",
          "Mentorización personalizada a profesionales en transición tecnológica"
        ],
        "technologies": ["PHP", "JavaScript", "HTML5", "CSS3", "E-commerce", "Metodologías de enseñanza"],
        "achievements": [
          "Transferencia efectiva de conocimientos técnicos avanzados",
          "Preparación de profesionales para el mercado actual de desarrollo web",
          "Desarrollo de material didáctico técnico especializado"
        ]
      }
    ]
  },
  {
    "company": "I+D",
    "position": "Investigador y Desarrollador de Software",
    "startDate": "2023-05-24",
    "endDate": "2024-06-13",
    "projects": [
      {
        "name": "Desarrollo de Framework PHP Asíncrono (Cohete)",
        "role": "Arquitecto Principal y Creador",
        "highlights": [
          "Diseñé y desarrollé un framework completo en PHP para manejo asíncrono de peticiones usando ReactPHP",
          "Implementé patrones observables y reactivos para mejorar la interactividad y respuesta del sistema",
          "Apliqué principios de Domain-Driven Design (DDD) para crear una arquitectura robusta y mantenible",
          "Establecí una suite completa de testing para garantizar calidad y estabilidad del framework",
          "Documenté la arquitectura y creé ejemplos de uso para facilitar la adopción"
        ],
        "technologies": ["PHP", "ReactPHP", "RxPHP", "DDD", "Event-Driven Architecture", "Async Programming", "PHPUnit", "Behat"],
        "achievements": [
          "Creación de framework PHP innovador con capacidades asíncronas nativas",
          "Implementación exitosa de arquitectura event-driven en PHP",
          "Contribución open-source para la comunidad de desarrollo PHP"
        ]
      }
    ]
  },
  {
    "company": "PC COMPONENTES Y MULTIMEDIA S.L.L",
    "position": "Full Stack Developer",
    "startDate": "2012-09-05",
    "endDate": "2023-05-23",
    "projects": [
      {
        "name": "Arquitectura de Microservicios y Modernización Legacy",
        "role": "Desarrollador Full Stack",
        "highlights": [
          "Participé activamente en la refactorización de sistemas legacy ERP hacia arquitectura de microservicios",
          "Colaboré en la implementación de Domain-Driven Design (DDD) y Test-Driven Development (TDD)",
          "Contribuí al desarrollo de microservicios API RESTful usando Symfony para Postventa y Atención al Cliente",
          "Apliqué patrones de testing con PHPUnit y Behat siguiendo las mejores prácticas del equipo",
          "Formé parte del equipo que automatizó procesos críticos de negocio"
        ],
        "technologies": ["Symfony", "DDD", "TDD", "RabbitMQ", "API RESTful", "PHPUnit", "Behat", "Microservicios"],
        "achievements": [
          "Contribución exitosa a la modernización de sistemas legacy críticos",
          "Participación en la mejora de mantenibilidad y testabilidad del código",
          "Colaboración en la automatización de procesos críticos de Postventa y Atención al Cliente"
        ]
      },
      {
        "name": "Plataforma de Contenido Generado por Usuarios",
        "role": "Software Engineer Backend",
        "highlights": [
          "Colaboré en el diseño e implementación del sistema de gestión de contenido generado por usuarios",
          "Participé en el desarrollo de arquitectura basada en eventos asíncronos para optimización de consultas",
          "Contribuí al sistema de rankings dinámicos con proyecciones de datos",
          "Desarrollé APIs escalables para manejo de grandes volúmenes de contenido user-generated"
        ],
        "technologies": ["Symfony", "Event-Driven Architecture", "Proyecciones", "Async Processing", "MySQL"],
        "achievements": [
          "Optimización significativa en consultas de contenido masivo",
          "Implementación exitosa de rankings dinámicos en tiempo real",
          "Mejora en la experiencia de usuario para contenido generado"
        ]
      },
      {
        "name": "Configurador de Equipos a Medida y Motor de Reglas",
        "role": "Desarrollador",
        "highlights": [
          "Participé activamente en el desarrollo del configurador de equipos informáticos",
          "Colaboré en la implementación del motor de reglas para lógica de negocio",
          "Contribuí significativamente al sistema de validación automática",
          "Trabajé en la optimización de la experiencia de usuario mediante interfaces intuitivas"
        ],
        "technologies": ["PHP", "Motor de Reglas", "Lógica de Negocio", "Validación de Compatibilidad"],
        "achievements": [
          "Contribución importante en la reducción de errores de compatibilidad",
          "Participación en mejoras sustanciales de experiencia de usuario",
          "Desarrollo de sistema robusto para reglas de negocio complejas"
        ]
      },
      {
        "name": "Desarrollo Frontend y Optimización de Performance",
        "role": "Senior Full Stack Developer",
        "highlights": [
          "Lideré el desarrollo de funcionalidades frontend complejas usando JavaScript, TypeScript y React",
          "Formé parte del equipo principal que desarrolló el sistema de Bombas para campañas especiales de Black Friday",
          "Optimicé de forma autónoma el motor de búsqueda legacy integrando Doofinder",
          "Gestioné la integración completa de múltiples APIs de terceros incluyendo Swogo, Connectif y Salesmanago"
        ],
        "technologies": ["JavaScript", "TypeScript", "React", "Doofinder", "API Integration", "Performance Optimization"],
        "achievements": [
          "Liderazgo exitoso en implementación de funcionalidades complejas para campañas especiales",
          "Mejora drástica en experiencia de búsqueda y performance del sistema",
          "Integración eficiente y autónoma de múltiples sistemas externos"
        ]
      },
      {
        "name": "Sistema de Auditoría y Analytics",
        "role": "Analista Junior y Desarrollador",
        "highlights": [
          "Apoyé en el desarrollo del sistema de auditoría usando Qlik para monitoreo operacional",
          "Colaboré en el diseño de dashboards en tiempo real para tracking de actividades",
          "Participé en el establecimiento de KPIs para medir eficiencia del servicio al cliente",
          "Contribuí a la implementación de protocolos de atención basados en análisis de datos"
        ],
        "technologies": ["Qlik", "SQL", "Business Intelligence", "KPIs", "Data Analysis"],
        "achievements": [
          "Participación exitosa en sistema de auditoría y control de calidad",
          "Colaboración en mejoras de eficiencia operacional",
          "Apoyo en optimización de procesos basada en datos"
        ]
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
