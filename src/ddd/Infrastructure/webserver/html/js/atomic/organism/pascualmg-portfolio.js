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
    "company": "Estamos Trabajando en ello! Enseguida os cuento...",
    "position": "Software Engineer",
    "projects": [
      {
        "name": "????????????",
        "role": "PHP Backend Developer",
        "highlights": [
          "Seguro que van a ser muy interesantes"
        ],
        "technologies": ["PHP", "SYMPHONY"],
        "achievements": [
            "Por el momento nada :) pero, seguro que pronto habrá mucho que contar"
        ]
      }
    ]
  },
  {
    "company": "GRUPO EUROFORMAC",
    "position": "PHP Software Engineer",
    "startDate": "2024-06-14",
    "endDate": "2024-06-28",
    "projects": [
      {
        "name": "EXPERTO WEB Y MULTIMEDIA PARA E-COMMERCE II",
        "role": "Software Engineer Teacher",
        "highlights": [
          "Participación en proyectos de formación empresarial"
        ],
        "technologies": ["PHP", "JS", "HTML5", "CSS4" ],
        "achievements": [
            "Formación en tecnologías web",
            "Desarrollo de habilidades en e-commerce"
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
      "name": "Creación de Framework PHP Asíncrono con ReactPHP (Cohete)",
      "role": "Creador y Líder Técnico",
      "highlights": [
        "Desarrollé un framework en PHP para manejo asíncrono de peticiones utilizando ReactPHP.",
        "Implementé el uso de observables para mejorar la interactividad y respuesta del framework.",
        "Apliqué principios de Domain-Driven Design (DDD) para estructurar el framework de manera funcional.",
        "Realicé pruebas exhaustivas para asegurar la calidad y estabilidad del framework."
      ],
      "technologies": ["PHP", "ReactPHP", "DDD", "Observables"],
      "achievements": [
        "Creación de un framework PHP innovador para manejo asíncrono de peticiones.",
        "Contribución a la comunidad de PHP con una herramienta de desarrollo avanzada y moderna."
      ]
    }
  ]
},
  {
    "company": "PC COMPONENTES Y MULTIMEDIA S.L.L",
    "position": "Desarrollador Backend con Microservicios y desarrollador Frontend especializado en JS",
    "startDate": "2013-01-05",
    "endDate": "2023-05-23",
    "projects": [
      {
        "name": "Gestión de Contenido Generado por Usuarios",
        "role": "Software Engineer Backend",
        "highlights": [
          "Diseñé e implementé una solución backend en Symfony",
          "Utilicé eventos asíncronos y proyecciones para optimizar consultas",
          "Implementé soporte para rankings dinámicos"
        ],
        "technologies": ["Symfony", "Eventos asíncronos", "Proyecciones"],
        "achievements": ["Optimización de consultas", "Soporte de rankings dinámicos"]
      },
      {
        "name": "Desarrollo de Microservicios para Postventa y Atención al Cliente",
        "role": "Líder de Desarrollo",
        "highlights": [
          "Lideré el desarrollo de microservicios API RESTful en Symfony",
          "Apliqué Domain-Driven Design (DDD) y RabbitMQ",
          "Automaticé procesos críticos de Postventa y Atención al Cliente"
        ],
        "technologies": ["Symfony", "DDD", "RabbitMQ", "API RESTful"],
        "achievements": ["Automatización de procesos críticos"]
      },
      {
        "name": "Refactorización de Código Legacy",
        "role": "Desarrollador Senior",
        "highlights": [
          "Refactoricé casos de uso legacy",
          "Implementé tests unitarios (PHPUnit) y de comportamiento (Behat)",
          "Establecí una base sólida para el desarrollo futuro"
        ],
        "technologies": ["PHPUnit", "Behat"],
        "achievements": ["Mejora de la calidad del código", "Incremento de la cobertura de tests"]
      },
      {
        "name": "Integración de APIs de Terceros",
        "role": "Integrador de Sistemas",
        "highlights": [
          "Integré APIs de terceros como Swogo, Connectif y Salesmanago",
          "Mejoré la eficiencia y funcionalidad de sistemas existentes"
        ],
        "technologies": ["APIs", "Integración de sistemas"],
        "achievements": ["Mejora de la eficiencia operativa"]
      },
      {
        "name": "Configurador de Equipos a Medida",
        "role": "Desarrollador de Sistemas",
        "highlights": [
          "Contribuí al desarrollo del configurador de equipos a medida",
          "Implementé un sistema de reglas flexible y extensible",
          "Reduje errores de compatibilidad e incidencias"
        ],
        "technologies": ["Sistemas de reglas", "Lógica de negocio compleja"],
        "achievements": ["Reducción de errores de compatibilidad", "Mejora de la experiencia del usuario"]
      },
      {
        "name": "Refactorización de Proyectos Legacy y ERP",
        "role": "Líder Técnico",
        "highlights": [
          "Lideré la refactorización de proyectos legacy y ERP",
          "Utilicé Domain-Driven Design (DDD) y Test-Driven Development (TDD)",
          "Mejoré significativamente la estructura y mantenibilidad del código"
        ],
        "technologies": ["DDD", "TDD", "Refactorización"],
        "achievements": ["Mejora de la mantenibilidad del código", "Modernización de sistemas legacy"]
      },
      {
        "name": "Optimización del Buscador con Doofinder",
        "role": "Desarrollador de Optimización",
        "highlights": [
          "Optimicé el buscador del sistema legacy implementando Doofinder",
          "Mejoré drásticamente la experiencia del usuario y el rendimiento de las búsquedas"
        ],
        "technologies": ["Doofinder", "Optimización de búsquedas"],
        "achievements": ["Mejora significativa en la experiencia de búsqueda del usuario"]
      },
      {
        "name": "Desarrollo Frontend",
        "role": "Desarrollador Frontend",
        "highlights": [
          "Desarrollé funcionalidades frontend avanzadas utilizando JavaScript, TypeScript y React",
          "Implementé un sistema de Bombas para campañas de Black Friday con lógica compleja"
        ],
        "technologies": ["JavaScript", "TypeScript", "React"],
        "achievements": ["Implementación de funcionalidades complejas para campañas especiales"]
      }
    ]
  },
  {
    "company": "PC COMPONENTES Y MULTIMEDIA S.L.L",
    "position": "Analista de Atención al Cliente",
    "startDate": "2012-09-05",
    "endDate": "2013-01-04",
    "projects": [
      {
        "name": "Implementación de Sistema de Auditoría con Qlik",
        "role": "Analista y Desarrollador",
        "highlights": [
          "Desarrollé e implementé un sistema de tracking usando Qlik",
          "Diseñé un dashboard para monitorear en tiempo real las actividades de los operadores",
          "Establecí KPIs para medir la eficiencia y calidad del servicio al cliente"
        ],
        "technologies": ["Qlick", "SQL", "Análisis de datos"],
        "achievements": [
          "Incremento del 30% en la eficiencia de los operadores",
          "Reducción del 25% en el tiempo de resolución de incidencias",
          "Mejora significativa en la capacidad de auditoría y control de calidad"
        ]
      },
      {
        "name": "Optimización de Procesos de Atención al Cliente",
        "role": "Gestor de Atención al Cliente",
        "highlights": [
          "Implementé nuevos protocolos de atención basados en los datos recopilados",
          "Desarrollé un programa de capacitación continua para el equipo"
        ],
        "technologies": ["CRM", "Herramientas de comunicación"],
        "achievements": [
          "Aumento del 20% en la satisfacción del cliente"
        ]
      }
    ]
  },
  {
    "company": "PC COMPONENTES Y MULTIMEDIA S.L.L",
    "position": "Gestor de Compras Internacionales",
    "startDate": "2012-09-05",
    "endDate": "2013-01-04",
    "projects": [
      {
        "name": "Optimización de Compras Internacionales",
        "role": "Gestor de Compras",
        "highlights": [
          "Gestioné relaciones con proveedores internacionales",
          "Negocié contratos y términos de compra favorables"
        ],
        "technologies": ["ERP", "Herramientas de gestión de inventario"],
        "achievements": [
            "Reducción del en los costes de adquisición de algunas marcas"
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
