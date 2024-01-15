class ChatBoxComponent extends HTMLElement {
    foo;

    constructor() {
        super();
        this.attachShadow({mode: 'open'});
    }

    connectedCallback() {

        this.render(
            this.getAttribute("group") || 'general',
        );

        //después del render vamos a tener disponibles estos elementos
        //se sacan aquí para evitar hacer selectores dentro de las
        //funciones subsecuentes
        this.elements = {
            'chatContainer': this.shadowRoot.querySelector('#chat-container'),
            'chatBox': this.shadowRoot.querySelector('#chat-box'),
            'userInputSection': this.shadowRoot.querySelector('#userInputSection'),
            'messageInput': this.shadowRoot.querySelector('#messageInput'),
            'connectedButton' : this.shadowRoot.querySelector('#connectedButton')
        };


        this.initWebSocket(
            this.getAttribute("host") || '0.0.0.0',
            this.getAttribute("port") || '8001',
        );

    }

    render(group) {
        this.shadowRoot.innerHTML = `
<style>
    #chat-container {
        position: fixed;    
        bottom: 0;
        height: 66vh;
        width: 97%;
        display: flex;
        flex-direction: column;
        padding: 5px;
        background-color: #073642;
        color: #93a1a1;
        border-radius: 10px;
        border: 1px solid #586e75;
    }

    #chat-box {
        overflow-y: auto;
        flex-grow: 1;
    }

    #chat-box div {
        border: 1px solid #586e75;
        border-radius: 10px;
        padding: 10px;
        margin: 2px 0; /* Añade un pequeño margen vertical entre los mensajes */
        background-color: #073642;
        color: #93a1a1;
        font-size: xxx-large;
    }

    #userInputSection {
        margin-top: 100px;
    }

    #messageInput {
        width: 98%;
        padding: 5px;
        border-radius: 5px;
        border: none;
        background-color: #002b36;
        color: #839496;
        font-size: xxx-large;
    }
    #connectedButton {
        background: grey;
            position: absolute;
    right: 20px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    
    }
</style>
<div id="chat-container">
    <div id="chat-box"></div>
    <button id="connectedButton" disabled></button>
    <div id="userInputSection">
        <label for="messageInput"></label>
        <input 
         id="messageInput" 
         type="text" 
         placeholder="Write a message ${group} "
        >
    </div>
</div>
    `;

    }

    connectedButton(b) {
        this.elements.connectedButton.textContent = b? "conectado"  : "desconectado"
        this.elements.connectedButton.style.backgroundColor = b? "green" : "red"
    }
    initWebSocket(host, port) {
        const websocket = new WebSocket(`ws://${host}:${port}`);

        websocket.onerror = (error)  => {
            console.error(error)
            this.connectedButton(false)
        }



        websocket.onopen = () => {
            console.log('WebSocket Client Connected');
            this.connectedButton(true)
        };

        websocket.onmessage = (message) => {
            console.log(message);
            const payload = JSON.parse(message.data)
            console.log(payload);
            const div = document.createElement('div');
            div.textContent = payload.msg || ""
            this.elements.chatBox.appendChild(div);
            this.elements.chatBox.scrollTop = this.elements.chatBox.scrollHeight
        };

        this.elements.messageInput.addEventListener(
            'keypress',
            (keyPressed) => {
                const message = this.elements.messageInput.value.trim()

                if (keyPressed.key !== 'Enter' || message === '') {
                    return;
                }

                try {
                    //envía mensaje
                    websocket.send(message)
                    this.elements.messageInput.value = '';

                    //hace copy para que el usuario
                    const divWithMessage = document.createElement('div')
                    divWithMessage.textContent = message
                    this.elements.chatBox.appendChild(
                        divWithMessage
                    )
                } catch (e) {
                    console.error(e)
                }
            })
    }
}

window
    .customElements
    .define('chat-box', ChatBoxComponent)
;
