class ChatBox extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({mode: 'open'});
        this.webSocket = null;
        this.minimized = true;
        this.dragging = false;
        this.dragOffset = {x: 0, y: 0};
    }

    connectedCallback() {
        const isSecure = window.location.protocol === 'https:';
        const host = this.getAttribute("host") || (isSecure ? 'ws.pascualmg.dev' : window.location.hostname);
        const port = this.getAttribute("port") || (isSecure ? '' : '8001');
        const protocol = isSecure ? 'wss' : 'ws';
        const uri = port ? `${protocol}://${host}:${port}` : `${protocol}://${host}`;
        const group = this.getAttribute("group") || 'general';

        this.render(group);
        this.elements = {
            'chatContainer': this.shadowRoot.querySelector('.chat-container'),
            'chatBox': this.shadowRoot.querySelector('.scrollable'),
            'userInputSection': this.shadowRoot.querySelector('.user-input-section'),
            'messageInput': this.shadowRoot.querySelector('.message-input'),
            'connectedButton': this.shadowRoot.querySelector('.button-round.left'),
            'closeButton': this.shadowRoot.querySelector('.button-round.right'),
            'minimizeButton': this.shadowRoot.querySelector('.button-minimize'),
            'chatBar': this.shadowRoot.querySelector('.chat-box-bar'),
            'minimizedBubble': this.shadowRoot.querySelector('.minimized-bubble'),
        };

        const removeElement = (function setHidden(elem) {
            return function () {
                elem.remove();
            }
        })(this);

        this.closeButtonClick$()
            .subscribe(removeElement);

        this.setupMinimize();
        this.setupDrag();

        this.SocketMessage$(uri)
            .subscribe(
                this.renderIncomingMessage(this.elements.chatBox),
                error => console.error('WebSocket error:', error),
                () => console.log('WebSocket connection closed')
            );

        this.userInput$()
            .subscribe(this.sendMessageToChat(this.webSocket));
    }

    setupMinimize() {
        this.elements.minimizeButton.addEventListener('click', () => this.toggleMinimize());
        this.elements.minimizedBubble.addEventListener('click', () => this.toggleMinimize());
    }

    toggleMinimize() {
        this.minimized = !this.minimized;
        this.elements.chatContainer.classList.toggle('hidden', this.minimized);
        this.elements.minimizedBubble.classList.toggle('hidden', !this.minimized);
        if (!this.minimized) {
            this.elements.messageInput.focus();
        }
    }

    setupDrag() {
        const bar = this.elements.chatBar;
        const container = this.elements.chatContainer;
        const bubble = this.elements.minimizedBubble;

        const onMouseDown = (e) => {
            if (e.target.tagName === 'BUTTON') return;
            this.dragging = true;
            const target = this.minimized ? bubble : container;
            const rect = target.getBoundingClientRect();
            this.dragOffset.x = e.clientX - rect.left;
            this.dragOffset.y = e.clientY - rect.top;
            bar.style.cursor = 'grabbing';
            e.preventDefault();
        };

        const onMouseMove = (e) => {
            if (!this.dragging) return;
            const target = this.minimized ? bubble : container;
            const x = e.clientX - this.dragOffset.x;
            const y = e.clientY - this.dragOffset.y;
            // Switch from bottom/right positioning to top/left
            target.style.bottom = 'auto';
            target.style.right = 'auto';
            target.style.left = Math.max(0, Math.min(x, window.innerWidth - target.offsetWidth)) + 'px';
            target.style.top = Math.max(0, Math.min(y, window.innerHeight - target.offsetHeight)) + 'px';
            // Sync position to the other element
            const other = this.minimized ? container : bubble;
            other.style.bottom = 'auto';
            other.style.right = 'auto';
            other.style.left = target.style.left;
            other.style.top = target.style.top;
        };

        const onMouseUp = () => {
            this.dragging = false;
            bar.style.cursor = 'grab';
        };

        bar.addEventListener('mousedown', onMouseDown);
        bubble.addEventListener('mousedown', onMouseDown);
        document.addEventListener('mousemove', onMouseMove);
        document.addEventListener('mouseup', onMouseUp);
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
                divWithMessage.classList.add('own');
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
            :host {
                font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            }

            .chat-container {
                position: fixed;
                bottom: 16px;
                right: 16px;
                height: 480px;
                width: 380px;
                display: flex;
                flex-direction: column;
                background: #1a1a2e;
                color: #e0e0e0;
                border-radius: 12px;
                border: 1px solid #2a2a4a;
                box-shadow: 0 8px 32px rgba(0,0,0,0.4);
                overflow: hidden;
                z-index: 9999;
                transition: opacity 0.2s, transform 0.2s;
            }

            .chat-container.hidden {
                display: none;
            }

            .minimized-bubble {
                position: fixed;
                bottom: 16px;
                right: 16px;
                width: 56px;
                height: 56px;
                border-radius: 50%;
                background: #00d4aa;
                border: none;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 4px 16px rgba(0,212,170,0.3);
                z-index: 9999;
                transition: transform 0.2s;
                font-size: 24px;
            }

            .minimized-bubble:hover {
                transform: scale(1.1);
            }

            .minimized-bubble.hidden {
                display: none;
            }

            .chat-box-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 12px 16px;
                background: #16213e;
                border-bottom: 1px solid #2a2a4a;
                cursor: grab;
                user-select: none;
            }

            .chat-title {
                font-size: 14px;
                font-weight: 600;
                color: #00d4aa;
            }

            .button-round {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                border: none;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .button-round.left {
                background: #e74c3c;
                transition: background 0.3s;
            }

            .button-round.left.connected {
                background: #00d4aa;
            }

            .button-minimize {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                border: none;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
                background: #2a2a4a;
                color: #888;
                font-size: 16px;
                transition: all 0.2s;
            }

            .button-minimize:hover {
                background: #00d4aa;
                color: #1a1a2e;
            }

            .button-round.right {
                background: #2a2a4a;
                color: #888;
                font-size: 14px;
                transition: all 0.2s;
            }

            .button-round.right:hover {
                background: #e74c3c;
                color: #fff;
            }

            .scrollable {
                flex: 1;
                overflow-y: auto;
                padding: 12px 16px;
                display: flex;
                flex-direction: column;
                gap: 6px;
            }

            .scrollable::-webkit-scrollbar {
                width: 4px;
            }

            .scrollable::-webkit-scrollbar-thumb {
                background: #2a2a4a;
                border-radius: 2px;
            }

            .scrollable div {
                padding: 8px 12px;
                background: #16213e;
                border-radius: 8px;
                font-size: 14px;
                line-height: 1.4;
                word-wrap: break-word;
                max-width: 85%;
            }

            .scrollable div.own {
                background: #0a3d62;
                align-self: flex-end;
            }

            .user-input-section {
                padding: 12px;
                border-top: 1px solid #2a2a4a;
                background: #16213e;
            }

            .message-input {
                width: 100%;
                padding: 10px 14px;
                border-radius: 8px;
                border: 1px solid #2a2a4a;
                background: #1a1a2e;
                color: #e0e0e0;
                font-size: 14px;
                outline: none;
                box-sizing: border-box;
                transition: border-color 0.2s;
            }

            .message-input:focus {
                border-color: #00d4aa;
            }

            .message-input::placeholder {
                color: #555;
            }
        </style>

        <div class="chat-container hidden">
            <div class="chat-box-bar">
                <div style="display:flex;align-items:center;gap:8px">
                    <button class="button-round left" disabled></button>
                    <span class="chat-title">Cohete Chat - ${group}</span>
                </div>
                <div style="display:flex;align-items:center;gap:4px">
                    <button class="button-minimize" title="Minimizar">&#8722;</button>
                    <button class="button-round right" title="Cerrar">X</button>
                </div>
            </div>
            <div class="scrollable"></div>
            <div class="user-input-section">
                <input class="message-input" type="text" placeholder="Escribe un mensaje..."/>
            </div>
        </div>
        <div class="minimized-bubble" title="Abrir chat">&#128172;</div>
        `;
    }

    closeButtonClick$() {
        return rxjs.fromEvent(
            this.elements.closeButton,
            'click'
        );
    }

    connectedButton(boolean) {
        if (boolean) {
            this.elements.connectedButton.classList.add('connected');
        } else {
            this.elements.connectedButton.classList.remove('connected');
        }
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