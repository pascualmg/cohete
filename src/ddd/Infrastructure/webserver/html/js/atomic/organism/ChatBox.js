class ChatBox extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({mode: 'open'});
        this.webSocket = null;
    }

    connectedCallback() {
        const host = this.getAttribute("host") || window.location.hostname;
        const port = this.getAttribute("port") || 443;
        const uri = `${window.location.protocol === 'https:' ? 'wss' : 'ws'}://${host}:${port}`;
        const group = this.getAttribute("group") || 'general';

        console.log('Attempting to connect to WebSocket at:', uri);

        this.render(group);
        this.elements = {
            'chatContainer': this.shadowRoot.querySelector('.chat-container'),
            'chatBox': this.shadowRoot.querySelector('.scrollable'),
            'userInputSection': this.shadowRoot.querySelector('.user-input-section'),
            'messageInput': this.shadowRoot.querySelector('.message-input'),
            'connectedButton': this.shadowRoot.querySelector('.button-round.left'),
            'closeButton': this.shadowRoot.querySelector('.button-round.right')
        };

        const removeElement = (function setHidden(elem) {
            return function () {
                elem.remove();
            }
        })(this);

        this.closeButtonClick$()
            .subscribe(removeElement);

        this.SocketMessage$(uri)
            .subscribe(
                this.renderIncomingMessage(this.elements.chatBox),
                error => console.error('WebSocket error:', error),
                () => console.log('WebSocket connection closed')
            );

        this.userInput$()
            .subscribe(this.sendMessageToChat(this.webSocket));
    }

    renderIncomingMessage(chatBox) {
        return (messageEvent) => {
            console.log('Received message:', messageEvent);
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
                console.log('Sending message:', value);
                webSocket.send(value);
                this.elements.messageInput.value = '';

                const divWithMessage = document.createElement('div');
                divWithMessage.textContent = value;
                this.elements.chatBox.appendChild(divWithMessage);
            } catch (e) {
                console.error('Error sending message:', e);
            }
        }
    }

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

    render(group) {
        this.shadowRoot.innerHTML = `
        <style>
            .chat-container {
                position: fixed;  
                bottom: 1%;
                right: 1%;
                height: 500px;
                width: 500px;
                display: flex;
                flex-direction: column;
                padding: 5px;
                background-color: var(--bg2);
                color: var(--cblk);
                border-radius: 10px;
                border: 1px solid var(--border);
            }

            .scrollable {
                overflow-y: auto;
                flex-grow: 1;
            }

            .rounded-div {
                border: 1px solid var(--border);
                border-radius: 10px;
                padding: 10px;
                margin: 2px 0;
                background-color: var(--bg2);
                color: var(--cblk);
                font-size: xx-large;
            }

            .user-input-section {
                margin-top: 100px;
            }

            .message-input {
                width: 98%;
                padding: 5px;
                border-radius: 5px;
                border: none;
                background-color: var(--act1);
                color: var(--cblk);
                font-size: xxx-large;
            }

            .button-round {
                background: var(--bg4);
                position: absolute;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                border: none;
            }

            .button-round.left {
                left: 20px;
            }

            .button-round.right {
                right: 20px;
                font-size: 1.2em;
                color: var(--bg1);
                text-align: center;
                cursor: pointer;
            }

            .chat-box-bar {
                display: flex;
                justify-content: space-between;
                padding: 10px;
                height: 70px;
            }
        </style>

        <div class="chat-container">
            <div class="chat-box-bar">
                <button class="button-round right">X</button>
                <button class="button-round left" disabled></button>
            </div>
            <div class="scrollable rounded-div"></div>
            <div class="user-input-section">
                <label for="messageInput"></label>
                <input class="message-input" type="text" placeholder="Write a message group ${group}"/>
            </div>
        </div>
        `;
    }

    closeButtonClick$() {
        return rxjs.fromEvent(
            this.elements.closeButton,
            'click'
        );
    }

    connectedButton(boolean) {
        this.elements.connectedButton.style.backgroundColor = boolean ? "green" : "red";
    }

    SocketMessage$(url) {
        return new rxjs.Observable(subscriber => {
            this.webSocket = new WebSocket(url);

            this.webSocket.onopen = () => {
                console.log('WebSocket Client Connected to:', url);
                this.connectedButton(true);
            }

            this.webSocket.onmessage = event => {
                console.log('WebSocket message received:', event);
                subscriber.next(event);
            };

            this.webSocket.onerror = error => {
                console.error('WebSocket error:', error);
                subscriber.error(error);
            };

            this.webSocket.onclose = event => {
                console.log('WebSocket closed:', event);
                if (!event.wasClean) {
                    subscriber.error('WebSocket connection lost');
                } else {
                    subscriber.complete();
                }
            };

            return () => {
                console.log('Closing WebSocket connection');
                this.webSocket.close(1000, 'Closing connection normally');
            };
        }).pipe(
            rxjs.operators.retryWhen((errors) => errors.pipe(
                rxjs.operators.tap(val => console.error(`WebSocket connection failed with error:`, val)),
                rxjs.operators.switchMap((error, index) =>
                    (index < 10) ?
                        rxjs.of(error).pipe(rxjs.operators.delay(2000))
                        : rxjs.throwError(() => new Error(`Failed to connect after 10 attempts: ${error}`))
                )
            ))
        );
    }
}

window.customElements.define('chat-box', ChatBox);

export default ChatBox;