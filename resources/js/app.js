import './dashboard.js';

window.richTextEditor = ({ value }) => ({
    value,

    init() {
        this.waitForEditor(() => {
            this.syncEditor(this.value ?? '');

            this.$refs.editor.addEventListener('trix-change', () => {
                const html = this.$refs.editor.editor.getHTML();
                const normalized = this.normalize(html);
                this.value = normalized;
                this.$refs.input.value = normalized;
            });

            this.$watch('value', (newValue) => {
                const normalized = this.normalize(newValue ?? '');

                if (normalized === this.normalize(this.$refs.editor.editor.getHTML())) {
                    return;
                }

                this.syncEditor(normalized);
            });

            this.$refs.editor.addEventListener('trix-attachment-add', (event) => {
                this.handleAttachmentAdd(event);
            });
        });
    },

    waitForEditor(callback) {
        if (this.$refs.editor?.editor) {
            if (!this.$refs.editor._richTextInitialized) {
                this.$refs.editor._richTextInitialized = true;
                callback();
            }

            return;
        }

        requestAnimationFrame(() => this.waitForEditor(callback));
    },

    syncEditor(content) {
        const normalized = this.normalize(content);

        this.$refs.input.value = normalized;
        this.$refs.editor.editor.loadHTML(normalized);
    },

    normalize(content) {
        if (!content || content === '<div><br></div>') {
            return '';
        }

        return content;
    },

    handleAttachmentAdd(event) {
        const { attachment } = event;

        if (!attachment?.file) {
            return;
        }

        this.uploadAttachment(attachment);
    },

    uploadAttachment(attachment) {
        if (!this.$wire?.upload) {
            console.warn('Livewire upload helper unavailable for rich text attachments.');
            return;
        }

        attachment.setUploadProgress(0);

        this.$wire.upload(
            'pendingAttachments',
            attachment.file,
            () => {
                this.$wire.call('storePendingAttachment')
                    .then((payload) => {
                        if (!payload || !payload.url) {
                            attachment.remove();
                            return;
                        }

                        const url = payload.url;

                        attachment.setAttributes({
                            url,
                            href: url,
                        });

                        attachment.setUploadProgress(100);
                    })
                    .catch(() => {
                        attachment.remove();
                    });
            },
            () => {
                attachment.remove();
            },
            (progress) => {
                attachment.setUploadProgress(progress ?? 0);
            },
        );
    },
});

document.addEventListener('livewire:navigated', () => {
    document.querySelectorAll('[data-rich-text] trix-editor').forEach((editor) => {
        editor._richTextInitialized = false;
    });
});

