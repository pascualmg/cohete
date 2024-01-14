class ChatBoxComponent extends HTMLElement {
    foo;
    constructor() {
        super();
       this.attachShadow({mode: 'open'});
    }

    connectedCallback() {

        this.render(
            this.getAttribute("foo")
        );
        this.initWebSocket();

    }

    render(name) {
        this.shadowRoot.innerHTML = `
<style>
    #chat-container {
        position: fixed;
        bottom: 0;
        height: 50vh;
        width: 100%;
        display: flex;
        flex-direction: column;
        padding: 5px;
        background-color: #073642;
        color: #93a1a1;
        border-radius: 5px;
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
    }

    #userInputSection {
        margin-top: 10px;
    }

    #messageInput {
        width: 100%;
        padding: 5px;
        border-radius: 5px;
        border: none;
        background-color: #002b36;
        color: #839496;
        font-size: larger;
    }
</style>
<div id="chat-container">
    <div id="chat-box"></div>
    <div id="userInputSection">
        <label for="messageInput"></label>
        <input 
         id="messageInput" 
         type="text" 
         placeholder="Write a message ${name} "
        >
    </div>
</div>
    `;
    }

    initWebSocket() {
        const chatbox = this.shadowRoot.querySelector('#chat-box');
        const messageInput = this.shadowRoot.querySelector('#messageInput');
        const websocket = new WebSocket('ws://192.168.2.119:8001');

        websocket.onopen = () => {
            console.log('WebSocket Client Connected');
        };

        websocket.onmessage = (message) => {
            console.log(message);
            const payload = JSON.parse(message.data)
            console.log(payload);
            const div = document.createElement('div');
            div.textContent = payload.msg || ""
            chatbox.appendChild(div);
            chatbox.scrollTop = chatbox.scrollHeight
        };

        messageInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter' && this.value.trim() !== '') {
                const message = messageInput.value.trim();

                websocket.send(message)
                messageInput.value = '';
            }
        })
    }
}

window
    .customElements
    .define('chat-box', ChatBoxComponent)
;
