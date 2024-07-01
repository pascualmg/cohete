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
        "name": "Desarrollador Backend y Microservicios",
        "role": "Software Engineer",
        "highlights": [
          "Diseñé e implementé una solución backend en Symfony para gestión de Contenido Generado por Usuarios, utilizando eventos asíncronos y proyecciones para optimizar consultas y soportar rankings dinámicos.",
          "Lideré el desarrollo de microservicios API RESTful en Symfony, aplicando Domain-Driven Design (DDD) y RabbitMQ para Postventa y Atención al Cliente, automatizando procesos críticos.",
          "Refactoricé casos de uso legacy, implementando tests unitarios (PHPUnit) y de comportamiento (Behat), estableciendo una base sólida para el desarrollo futuro.",
          "Integré APIs de terceros (Swogo, Connectif, Salesmanago) para mejorar la eficiencia y funcionalidad de sistemas existentes.",
          "Contribuí al desarrollo del configurador de equipos a medida, implementando un sistema de reglas que previene errores de compatibilidad y reduce incidencias.",
          "Lideré la refactorización de proyectos legacy y ERP utilizando DDD y TDD, mejorando significativamente la estructura y mantenibilidad del código.",
          "Optimicé el buscador del sistema legacy implementando Doofinder, mejorando drásticamente la experiencia del usuario y el rendimiento de las búsquedas."
        ],
        "technologies": [
          "Symfony", "DDD", "RabbitMQ", "PHPUnit", "Behat", "APIs", "Doofinder"
        ],
        "achievements": []
      },
      {
        "name": "Desarrollador Frontend",
        "role": "Desarrollador Principal",
        "highlights": [
          "Desarrollé funcionalidades frontend avanzadas utilizando JavaScript, TypeScript y React, incluyendo un sistema de Bombas para campañas de Black Friday con lógica compleja."
        ],
        "technologies": [
          "JavaScript", "TypeScript", "React"
        ],
        "achievements": []
      },
      {
        "name": "Competencias Técnicas",
        "role": "Desarrollador Principal",
        "highlights": [
          "Dominio avanzado de sistemas operativos Linux.",
          "Experiencia sólida en control de versiones con Git.",
          "Proficiencia en Docker para gestión de entornos de desarrollo y despliegue.",
          "Amplia experiencia en diseño y desarrollo de arquitecturas de microservicios.",
            "Conocimientos avanzados en bases de datos relacionales y NoSQL.",
            "Experiencia en diseño y desarrollo de APIs RESTful.",
            "Conocimientos avanzados en PHP, JavaScript, HTML y CSS."
        ],
        "technologies": [
          "Linux", "Git", "Docker", "Microservicios", "Bases de Datos", "APIs", "PHP", "JavaScript", "HTML", "CSS"
        ],
        "achievements": []
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
            <chat-box 
                    hidden
                    group="tbl001"
                    host="pascualmg.dev"
                    port="8001"
            ></chat-box>


        `;
  }
}

customElements.define('pascualmg-portfolio', PortfolioElement);
export default PortfolioElement
