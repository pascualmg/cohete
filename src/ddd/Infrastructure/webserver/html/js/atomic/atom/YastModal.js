class YastModal extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this._isOpen = false;
    }

    static get observedAttributes() {
        return ['button-text'];
    }

    connectedCallback() {
        this.buttonText = this.getAttribute('button-text') || 'Pulsa aquí';
        this.render();
        this.shadowRoot.querySelector('button').addEventListener('click', () => this.toggleModal());
        this.shadowRoot.querySelector('.modal-background').addEventListener('click', () => this.toggleModal());
    }

    attributeChangedCallback(name, oldValue, newValue) {
        if (name === 'button-text') {
            this.buttonText = newValue;
            this.render();
        }
    }

    toggleModal() {
        this._isOpen = !this._isOpen;
        this.shadowRoot.querySelector('.modal-container').style.display = this._isOpen ? 'flex' : 'none';
    }

    render() {
        this.shadowRoot.innerHTML = `
      <style>
        :host {
          display: inline-block;
        }
        button {
          padding: 10px 20px;
          font-size: 16px;
          cursor: pointer;
          background-color: #3498db;
          color: white;
          border: none;
          border-radius: 5px;
          transition: background-color 0.3s;
        }
        button:hover {
          background-color: #2980b9;
        }
        .modal-container {
          display: none;
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background-color: rgba(0, 0, 0, 0.5);
          justify-content: center;
          align-items: center;
          z-index: 1000;
        }
        .modal-content {
          background-color: white;
          padding: 20px;
          border-radius: 5px;
          max-width: 80%;
          max-height: 80%;
          overflow: auto;
          position: relative;
        }
        .close-button {
          position: absolute;
          top: 10px;
          right: 10px;
          font-size: 24px;
          cursor: pointer;
          background: none;
          border: none;
          color: #333;
        }
      </style>
      <button>${this.buttonText}</button>
      <div class="modal-container">
        <div class="modal-content">
          <button class="close-button" onclick="this.getRootNode().host.toggleModal()">&times;</button>
          <slot name="content"></slot>
        </div>
      </div>
    `;
    }
}

customElements.define('yast-modal', YastModal);

export default YastModal;