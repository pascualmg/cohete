class ChatBoxComponent extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({mode: 'open'});
    }

    connectedCallback() {
        this.render();
        this.initWebSocket();
    }

    render() {
        this.shadowRoot.innerHTML = `
    <style>
    #chat-container {
        max-width: 400px;
        margin: auto;
        padding: 20px; 
        background-color: #002b36; 
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.5); 
        border-radius: 10px;
    }
    #messageInput { 
        width: 80%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #073642;
        font-size: 1em;
        margin-bottom: 10px;
        color: #586e75;
        background-color: #002b36;
    }
    #chat-box {
        color: #859900;
        max-height: 400px;
        overflow-y: auto;
        border: 1px solid #073642;
        border-radius: 10px;
        padding: 20px;
        background-color: #002b36;
        margin-bottom: 10px;
    }
    #userInputSection {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    #sendButton {
        font-size: 1em;
        padding: 10px;
        border: none;
        border-radius: 5px;
        color: #002b36;
        background-color: #859900;
        cursor: pointer;
    }
    #sendButton:hover {
        background-color: #586e75;
    }
    </style>
    <div id="chat-container">
      <div id="chat-box"></div>
      <div id="userInputSection">
        <input id="messageInput" type="text" placeholder="Write a message">
        <button id="sendButton">Send</button>
      </div>
    </div>`;
    }

    initWebSocket() {
        const chatbox = this.shadowRoot.querySelector('#chat-box');
        const messageInput = this.shadowRoot.querySelector('#messageInput');
        const sendButton = this.shadowRoot.querySelector('#sendButton');
        const websocket = new WebSocket('ws://localhost:8001');

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
        };

        sendButton.addEventListener('click', () => {
            const message = messageInput.value.trim();
            if (message !== "") {
                websocket.send(message);
                messageInput.value = '';
            }
        });
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
