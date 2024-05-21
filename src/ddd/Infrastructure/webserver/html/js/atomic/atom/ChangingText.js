class ChangingText extends HTMLElement {
    constructor() {
        super();
        this.currentTextIndex = 0;
        this.texts = [];
        this.duration = 3000; // Duración fija de 3 segundos
        this.initialDelay = 20; // Muy rápido al principio
        this.timeoutId = null;

        this.addEventListener('click', this.startTextAnimation)
    }

    static get observedAttributes() {
        return ['texts'];
    }

    attributeChangedCallback(name, oldValue, newValue) {
        this.parseAttributes();
        this.startTextAnimation();
    }

    connectedCallback() {
        this.parseAttributes();
        this.startTextAnimation();
    }

    parseAttributes() {
        const textsAttr = this.getAttribute('texts');

        if (textsAttr) {
            this.texts = JSON.parse(textsAttr);
        }
    }

    startTextAnimation() {
        clearTimeout(this.timeoutId);

        if (this.texts.length <= 1) return;

        const startTime = Date.now();
        let currentDelay = this.initialDelay;

        const changeText = () => {
            const elapsed = Date.now() - startTime;

            if (elapsed >= this.duration) {
                // Detén la animación y elige un texto aleatorio
                this.currentTextIndex = Math.floor(Math.random() * this.texts.length);
                this.innerHTML = this.texts[this.currentTextIndex];
                return;
            }

            this.currentTextIndex = (this.currentTextIndex + 1) % this.texts.length;
            this.innerHTML = this.texts[this.currentTextIndex];

            // Calcular el nuevo delay usando una función exponencial para desacelerar
            const progress = elapsed / this.duration;
            currentDelay = this.initialDelay + progress * (this.duration / 5); // Ajusta este valor para controlar la desaceleración

            this.timeoutId = setTimeout(changeText, currentDelay);
        };

        this.timeoutId = setTimeout(changeText, currentDelay);
    }

    disconnectedCallback() {
        clearTimeout(this.timeoutId);
    }
}

customElements.define('changing-text', ChangingText);
export default ChangingText;
