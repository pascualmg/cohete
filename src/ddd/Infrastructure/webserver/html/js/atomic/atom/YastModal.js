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
          --modal-button-bg: var(--green, #00ff88);
          --modal-button-hover: var(--head1, #ffffff);
          --modal-button-text: var(--bg1, #0a0a0a);
          --modal-button-border: var(--border, #444444);
        }
        button {
          background: linear-gradient(135deg, var(--modal-button-bg), var(--comp, #ff6b6b));
          color: var(--modal-button-text);
          border: 2px solid var(--modal-button-border);
          padding: 12px 20px;
          border-radius: 20px;
          cursor: pointer;
          font-size: 14px;
          font-weight: bold;
          font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
          transition: all 0.3s ease;
          box-shadow: 0 3px 12px rgba(0, 0, 0, 0.15);
          text-transform: uppercase;
          letter-spacing: 0.8px;
          position: relative;
          overflow: hidden;
          min-height: 44px;
          display: flex;
          align-items: center;
          justify-content: center;
        }
        button:hover {
          transform: translateY(-2px);
          box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
          background: linear-gradient(135deg, var(--modal-button-hover), var(--head2, #cccccc));
          color: var(--bg1, #0a0a0a);
        }
        button:active {
          transform: translateY(0);
          box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .modal-container {
          display: none;
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0, 0, 0, 0.8);
          backdrop-filter: blur(5px);
          justify-content: center;
          align-items: center;
          z-index: 1000;
          animation: fadeIn 0.3s ease-out;
        }
        .modal-content {
          background: var(--bg1, #0a0a0a);
          border: 2px solid var(--border, #444444);
          border-radius: 15px;
          max-width: 95%;
          max-height: 95%;
          overflow: auto;
          position: relative;
          box-shadow: 0 15px 50px rgba(0, 0, 0, 0.3);
          animation: modalSlideIn 0.4s ease-out;
          margin: 10px;
        }
        .close-button {
          position: absolute;
          top: 10px;
          right: 15px;
          background: var(--comp, #ff6b6b);
          border: 2px solid var(--border, #444444);
          border-radius: 50%;
          width: 35px;
          height: 35px;
          font-size: 18px;
          cursor: pointer;
          color: var(--bg1, #0a0a0a);
          font-weight: bold;
          transition: all 0.3s ease;
          display: flex;
          align-items: center;
          justify-content: center;
          min-height: 35px;
          min-width: 35px;
        }
        .close-button:hover {
          background: var(--green, #00ff88);
          transform: scale(1.1);
          box-shadow: 0 5px 15px rgba(255, 107, 107, 0.4);
        }
        @keyframes fadeIn {
          from { opacity: 0; }
          to { opacity: 1; }
        }
        @keyframes modalSlideIn {
          from { 
            opacity: 0; 
            transform: scale(0.8) translateY(50px); 
          }
          to { 
            opacity: 1; 
            transform: scale(1) translateY(0); 
          }
        }

        @media (min-width: 480px) {
          button {
            padding: 14px 25px;
            border-radius: 22px;
            font-size: 15px;
            letter-spacing: 0.9px;
          }

          .modal-content {
            max-width: 90%;
            max-height: 90%;
            border-radius: 18px;
            margin: 15px;
          }

          .close-button {
            top: 12px;
            right: 18px;
            width: 38px;
            height: 38px;
            font-size: 19px;
          }
        }

        @media (min-width: 768px) {
          button {
            padding: 15px 30px;
            border-radius: 25px;
            font-size: 16px;
            letter-spacing: 1px;
          }

          .modal-content {
            max-width: 85%;
            max-height: 85%;
            border-radius: 20px;
            margin: 20px;
          }

          .close-button {
            top: 15px;
            right: 20px;
            width: 40px;
            height: 40px;
            font-size: 20px;
          }
        }

        @media (min-width: 1024px) {
          button {
            padding: 16px 35px;
            font-size: 17px;
          }

          .modal-content {
            max-width: 80%;
            max-height: 80%;
          }

          .close-button {
            top: 18px;
            right: 22px;
            width: 42px;
            height: 42px;
            font-size: 21px;
          }
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