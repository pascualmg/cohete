class SPA extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.shadowRoot.innerHTML = `
            <style>
                /* Restablecer el estilo por defecto del navegador */
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                /* Estilo general */
                :host {
                    font-family: Arial, sans-serif;
                    display: block;
                    color: var(--base);
                    background-color: var(--bg1);
                }

                header, footer {
                    background-color: var(--head1-bg);
                    color: var(--head1);
                    text-align: center;
                    padding: 10px 0;
                }

                /* Estilo del contenedor principal */
                .container {
                    display: flex;
                    flex-direction: column; /* Mobile first: Columna por defecto */
                    background-color: var(--bg2);
                }

                .menu-button {
                    display: block;
                    background-color: var(--act1);
                    color: var(--act2);
                    border: none;
                    padding: 10px;
                    text-align: left;
                    font-size: 16px;
                    cursor: pointer;
                }

                .sidebar {
                    display: none; /* Oculta la barra lateral por defecto en móviles */
                    background-color: var(--bg3);
                    padding: 20px;
                }

                .sidebar ul {
                    list-style-type: none;
                }

                .sidebar ul li {
                    margin-bottom: 10px;
                    cursor: pointer;
                    color: var(--str);
                }

                .main-content {
                    padding: 20px;
                    background-color: var(--bg4);
                    flex: 1;
                }

                /* Media Queries para pantallas más grandes */
                @media (min-width: 768px) {
                    .container {
                        flex-direction: row; /* Pantallas más grandes: fila horizontal */
                    }

                    .menu-button {
                        display: none; /* Oculta el botón de menú en pantallas grandes */
                    }

                    .sidebar {
                        display: block; /* Muestra la barra lateral en pantallas grandes */
                        width: 200px;
                        flex-shrink: 0; /* Evita que la barra lateral se encoja */
                    }

                    .main-content {
                        overflow-y: auto; /* Permite desplazarse si el contenido es demasiado grande */
                    }
                }
            </style>
            <!-- El resto de tu código HTML -->
        `;

        // El resto de tu código JavaScript
    }

    // El resto de tus métodos
}

customElements.define('spa', SPA);