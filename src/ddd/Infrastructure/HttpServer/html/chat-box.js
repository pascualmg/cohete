class ChatBoxComponent extends HTMLElement {

    constructor() {
        super();
        this.attachShadow({mode: 'open'});
        this.webSocket = null
    }

    connectedCallback() {
        const host = this.getAttribute("host") || '0.0.0.0'
        const port = this.getAttribute("port") || '8001'
        const uri = `ws://${host}:${port}`
        const group = this.getAttribute("group") || 'general'

        this.render(group);
        this.elements = {
            'chatContainer': this.shadowRoot.querySelector('#chat-container'),
            'chatBox': this.shadowRoot.querySelector('#chat-box'),
            'userInputSection': this.shadowRoot.querySelector('#userInputSection'),
            'messageInput': this.shadowRoot.querySelector('#messageInput'),
            'connectedButton': this.shadowRoot.querySelector('#connectedButton')
        };

        this.IncomingMessageFromWebSocket$(uri)
            .subscribe(this.renderIncomingMessage(this.elements.chatBox))

        this.userInput$()
            .subscribe(this.sendMessageToChat(this.webSocket))
    }

    renderIncomingMessage(chatBox) {
        return (messageEvent) => {
            const {
                msg = "",
                uuid = ""
            } = JSON.parse(messageEvent.data);

            const div = document.createElement('div');
            div.textContent = msg || "";
            chatBox.appendChild(div);
            chatBox.scrollTop = this.elements.chatBox.scrollHeight;
        }
    }

    sendMessageToChat(webSocket) {
        return (value) => {
            try {
                webSocket.send(value)
                this.elements.messageInput.value = '';

                const divWithMessage = document.createElement('div');
                divWithMessage.textContent = value;
                this.elements.chatBox.appendChild(
                    divWithMessage
                )
            } catch (e) {
                console.error(e);
            }
        }
    }

    /**
     * Returns an observable that emits an object whenever the Enter key is pressed on the message input field.
     * The emitted object contains the trimmed value of the input field and the key that triggered the event.
     * Only emits when the value is not empty.
     *
     * @return {Observable} An observable that emits an object when the Enter key is pressed on the message input field.
     */
    userInput$() {
        return rxjs.fromEvent(this.elements.messageInput, 'keypress')
            .pipe(
                rxjs.operators.map(event => ({
                    value: this.elements.messageInput.value.trim(),
                    key: event.key
                })),
                rxjs.operators.filter(({value, key}) => key === 'Enter' && value !== ''),
                rxjs.operators.map(({value, _}) => value)
            );
    }

    /**
     * Renders the chat interface with the given group name.
     *
     * @param {string} group - The name of the chat group.
     */
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

    /**
     * Sets the background color of the connected button element.
     *
     * @param {boolean} boolean - The boolean value to determine the background color of the connected button.
     *                            True sets the color to green, while false sets it to red.
     *
     * @return {void}
     */
    connectedButton(boolean) {
        this.elements.connectedButton.style.backgroundColor = boolean ? "green" : "red"
    }

    /**
     * Creates and returns an Observable that receives incoming messages from a WebSocket.
     *
     * @param {string} url - The URL of the WebSocket server.
     * @returns {Observable} - An Observable that emits incoming messages from the WebSocket.
     */
    IncomingMessageFromWebSocket$(url) {
        return new rxjs.Observable(subscriber => {
            /** lo crea y lo deja disponible en la class
             *  para poder usarlo enviando los mensajes.
             */
            this.webSocket = new WebSocket(url);

            this.webSocket.onopen = () => {
                console.log('WebSocket Client Connected');
                this.connectedButton(true);
            }

            this.webSocket.onmessage = event => {
                console.log(event)
                subscriber.next(event)
            };

            this.webSocket.onerror = error => {
                subscriber.error(error);
            };

            this.webSocket.onclose = event => {
                if (!event.wasClean) {
                    subscriber.error('WebSocket connection lost');
                }
            };

            return () => this.webSocket.close(1000, 'Closing connection normally');
        }).pipe(
            rxjs.operators.retryWhen((errors) => errors.pipe(
                // Log the error to console
                rxjs.operators.tap(val => console.error(`WebSocket connection failed with error: ${val}`)),
                // Retry every 2 seconds, up to a maximum of 10 attempts
                rxjs.operators.switchMap((error, index) =>
                    (index < 10) ?
                        rxjs.of(error).pipe(rxjs.delay(2000))
                        : rxjs.throwError(error))
            ))
        );
    }
}

window
    .customElements
    .define('chat-box', ChatBoxComponent)
;
