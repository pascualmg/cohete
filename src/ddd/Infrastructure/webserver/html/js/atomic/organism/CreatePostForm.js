class CreatePostForm extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({mode: 'open'});
    }

    connectedCallback() {
        this.render();
        this.setupObservables();
    }

    render() {
        this.shadowRoot.innerHTML = `
      <style>
        :host {
          display: block;
          font-family: 'Fira Code', monospace;
          background-color: var(--bg1, #292b2e);
          color: var(--base, #b2b2b2);
          padding: 20px;
          border-radius: 8px;
        }
        form {
          display: flex;
          flex-direction: column;
          gap: 15px;
        }
        label {
          font-weight: bold;
          color: var(--keyword, #4f97d7);
        }
        input, textarea {
          width: 100%;
          padding: 10px;
          background-color: var(--bg2, #212026);
          border: 1px solid var(--border, #5d4d7a);
          border-radius: 4px;
          color: var(--base, #b2b2b2);
          font-family: inherit;
        }
        input:focus, textarea:focus {
          outline: none;
          border-color: var(--func, #bc6ec5);
          box-shadow: 0 0 0 2px var(--highlight-dim, #3b314d);
        }
        button {
          padding: 12px;
          background-color: var(--func, #bc6ec5);
          color: var(--bg1, #292b2e);
          border: none;
          border-radius: 4px;
          cursor: pointer;
          font-weight: bold;
          transition: background-color 0.3s ease;
        }
        button:hover {
          background-color: var(--comp, #c56ec3);
        }
        .error {
          color: var(--err, #e0211d);
          font-size: 0.9em;
          margin-top: 5px;
        }
      </style>
      <form id="postForm">
        <label for="headline">Headline:</label>
        <input type="text" id="headline" required>
        <div class="error" id="headlineError"></div>

        <label for="articleBody">Article Body:</label>
        <textarea id="articleBody" rows="10" required></textarea>
        <div class="error" id="articleBodyError"></div>

        <label for="image">Image URL:</label>
        <input type="url" id="image">
        <div class="error" id="imageError"></div>

        <label for="author">Author:</label>
        <input type="text" id="author" required>
        <div class="error" id="authorError"></div>

        <button type="submit">Submit Post</button>
      </form>
    `;
    }

    setupObservables() {
        const form = this.shadowRoot.getElementById('postForm');
        const submitButton = this.shadowRoot.querySelector('button[type="submit"]');

        const formInputs = ['headline', 'articleBody', 'image', 'author'];

        formInputs.forEach(fieldId => {
            const input = this.shadowRoot.getElementById(fieldId);
            rxjs.fromEvent(input, 'input')
                .pipe(
                    rxjs.operators.debounceTime(300),
                    rxjs.operators.map(event => ({field: fieldId, value: event.target.value}))
                )
                .subscribe(this.validateField.bind(this));
        });

        rxjs.fromEvent(form, 'submit')
            .pipe(
                rxjs.operators.map(event => {
                    event.preventDefault();
                    return this.createPostData();
                }),
                rxjs.operators.switchMap(postData => this.sendPostRequest(postData))
            )
            .subscribe(
                result => {
                    console.log('Success:', result);
                    this.dispatchEvent(new CustomEvent('postCreated', {detail: result}));
                },
                error => {
                    console.error('Error:', error);
                    this.dispatchEvent(new CustomEvent('postError', {detail: error.message}));
                }
            );

        rxjs.fromEvent(form, 'input')
            .pipe(
                rxjs.operators.debounceTime(300),
                rxjs.operators.map(() => this.isFormValid())
            )
            .subscribe(isValid => {
                submitButton.disabled = !isValid;
                submitButton.style.opacity = isValid ? '1' : '0.5';
            });
    }

    validateField({field, value}) {
        const errorElement = this.shadowRoot.getElementById(`${field}Error`);
        let errorMessage = '';

        switch (field) {
            case 'headline':
            case 'articleBody':
            case 'author':
                if (!value.trim()) {
                    errorMessage = `${field.charAt(0).toUpperCase() + field.slice(1)} is required.`;
                }
                break;
            case 'image':
                if (value && !this.isValidUrl(value)) {
                    errorMessage = 'Please enter a valid URL.';
                }
                break;
        }

        errorElement.textContent = errorMessage;
    }

    isValidUrl(string) {
        try {
            new URL(string);
            return true;
        } catch (_) {
            return false;
        }
    }

    isFormValid() {
        const requiredFields = ['headline', 'articleBody', 'author'];
        const isRequiredFieldsValid = requiredFields.every(field =>
            this.shadowRoot.getElementById(field).value.trim() !== ''
        );
        const isImageUrlValid = this.shadowRoot.getElementById('image').value === '' ||
            this.isValidUrl(this.shadowRoot.getElementById('image').value);

        return isRequiredFieldsValid && isImageUrlValid;
    }

    createPostData = () => ({
        id: crypto.randomUUID(),
        headline: this.shadowRoot.getElementById('headline').value,
        articleBody: this.escapeForJSON(this.shadowRoot.getElementById('articleBody').value),
        image: this.shadowRoot.getElementById('image').value,
        author: this.shadowRoot.getElementById('author').value,
        datePublished: new Date().toISOString()
    });

    escapeForJSON(str) {
        return str.replace(/\\/g, '\\\\')
            .replace(/"/g, '\\"')
            .replace(/\n/g, '\\n')
            .replace(/\r/g, '\\r')
            .replace(/\t/g, '\\t')
            .replace(/\f/g, '\\f');
    }

    sendPostRequest(postData) {
        return rxjs.ajax.ajax({
            url: 'http://localhost:8000/post',
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: postData
        }).pipe(
            rxjs.operators.map(response => response.response),
            rxjs.operators.catchError(error => {
                console.error('Error:', error);
                return rxjs.throwError(() => new Error(`Error sending post: ${error.message}`));
            })
        );
    }

}

customElements.define('create-post-form', CreatePostForm);
export default CreatePostForm;