class PostEditor {
    constructor(textareaId) {
        this.textarea = document.getElementById(textareaId);
        this.initEditor();
    }

    initEditor() {
        const toolbar = [
            'heading', 'bold', 'italic', 'code', 'quote',
            'unordered-list', 'ordered-list', 'link', 'image'
        ];

        this.editor = new EasyMDE({
            element: this.textarea,
            toolbar,
            spellChecker: false,
            previewRender: (text) => {
                return fetch('/api/markdown/preview', {
                    method: 'POST',
                    body: JSON.stringify({ text })
                }).then(res => res.text());
            },
            sideBySideFullscreen: false
        });
    }
}

new PostEditor('post-content');