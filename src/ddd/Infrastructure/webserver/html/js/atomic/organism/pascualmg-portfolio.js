import TechnologiesList from "../molecule/technologiesList.js";
import ChatBox from "./ChatBox.js";
import PortFolioHeader from "../molecule/PortfolioHeader.js"
import ThemeToggler from "./ThemeToogler.js"
import ExperienceTimeline from "../molecule/ExperienceTimeline.js"

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
            </style>
           <portfolio-header></portfolio-header>
           <section>
                <h3>Sobre mí</h3>
                <p>Aquí es donde puedes añadir un resumen breve y conciso sobre ti, tus habilidades y tu experiencia
                    profesional.</p>
            </section>

<experience-timeline data='
[
  {
    "company": "PC COMPONENTES Y MULTIMEDIA S.L.L",
    "position": "Desarrollador Backend con Microservicios y desarrollador Frontend especializado en JS",
    "startDate": "2013",
    "endDate": "2023",
    "duration": "10 años",
    "projects": [
      {
        "name": "Sistema de Gestión de Contenido Generado por Usuarios",
        "role": "Desarrollador Backend",
        "highlights": [
          "Diseñé e implementé una solución backend en Symfony",
          "Utilicé eventos asíncronos y proyecciones para optimizar consultas",
          "Implementé soporte para rankings dinámicos"
        ],
        "technologies": ["Symfony", "Eventos Asíncronos", "Proyecciones"],
        "achievements": ["Mejoró la eficiencia en la gestión de contenido de usuarios"]
      },
      {
        "name": "Microservicios para Postventa y Atención al Cliente",
        "role": "Líder de Desarrollo",
        "highlights": [
          "Desarrollé microservicios API RESTful en Symfony",
          "Apliqué Domain-Driven Design (DDD)",
          "Implementé RabbitMQ para comunicación asíncrona",
          "Automaticé procesos críticos de atención al cliente"
        ],
        "technologies": ["Symfony", "DDD", "RabbitMQ", "API RESTful"],
        "achievements": ["Mejoró la eficiencia en procesos de postventa"]
      },
      {
        "name": "Refactorización de Código Legacy",
        "role": "Desarrollador Senior",
        "highlights": [
          "Refactoricé casos de uso legacy",
          "Implementé tests unitarios con PHPUnit",
          "Agregué tests de comportamiento con Behat"
        ],
        "technologies": ["PHPUnit", "Behat", "Refactorización"],
        "achievements": ["Establecí una base sólida para el desarrollo futuro"]
      },
      {
        "name": "Integración de APIs de Terceros",
        "role": "Desarrollador Backend",
        "highlights": [
          "Integré APIs de Swogo, Connectif y Salesmanago",
          "Mejoré la eficiencia y funcionalidad de sistemas existentes"
        ],
        "technologies": ["API Integration", "Swogo", "Connectif", "Salesmanago"],
        "achievements": ["Amplió las capacidades de los sistemas existentes"]
      },
      {
        "name": "Configurador de Equipos a Medida",
        "role": "Desarrollador Backend",
        "highlights": [
          "Implementé un sistema de reglas para prevenir errores de compatibilidad",
          "Reduje incidencias relacionadas con configuraciones incorrectas"
        ],
        "technologies": ["Sistema de Reglas", "Lógica de Negocio"],
        "achievements": ["Mejoró la satisfacción del cliente y redujo errores en pedidos"]
      },
      {
        "name": "Refactorización de Proyectos Legacy y ERP",
        "role": "Líder Técnico",
        "highlights": [
          "Apliqué Domain-Driven Design (DDD) y Test-Driven Development (TDD)",
          "Mejoré significativamente la estructura y mantenibilidad del código"
        ],
        "technologies": ["DDD", "TDD", "Refactorización"],
        "achievements": ["Facilitó el mantenimiento y evolución de sistemas críticos"]
      },
      {
        "name": "Optimización del Buscador",
        "role": "Desarrollador Backend",
        "highlights": [
          "Implementé Doofinder en el sistema legacy",
          "Mejoré drásticamente la experiencia del usuario y el rendimiento de las búsquedas"
        ],
        "technologies": ["Doofinder", "Optimización de Búsqueda"],
        "achievements": ["Aumentó la satisfacción del usuario y la eficiencia en búsquedas"]
      },
      {
        "name": "Desarrollo Frontend Avanzado",
        "role": "Desarrollador Principal",
        "highlights": [
          "Desarrollé funcionalidades frontend utilizando JavaScript, TypeScript y React",
          "Implementé un sistema de Bombas para campañas de Black Friday con lógica compleja"
        ],
        "technologies": ["JavaScript", "TypeScript", "React"],
        "achievements": ["Mejoró la interactividad y rendimiento de la interfaz de usuario"]
      }
    ]
  }
]'></experience-timeline>

            <section>
                <h3>Contacto</h3>
                <p>Indica cómo alguien puede ponerte en contacto. Recuerda, no compartas información de contacto que no
                    quieras que sea pública.</p>
            </section>
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
            </technologies-list>
<!--            <chat-box -->
<!--                    hidden-->
<!--                    group="tbl001"-->
<!--                    host="pascualmg.dev"-->
<!--                    port="8001"-->
<!--            ></chat-box>-->


        `;
  }
}

customElements.define('pascualmg-portfolio', PortfolioElement);
export default PortfolioElement
