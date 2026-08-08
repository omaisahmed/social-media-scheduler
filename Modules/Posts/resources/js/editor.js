import Quill from 'quill';

import 'quill/dist/quill.snow.css';

import 'quill-mention/autoregister';
import 'quill-mention/dist/quill.mention.css';

import '../css/editor.css';

window.richEditor = function richEditor(options) {
    let quill = null;
    let mentionDebounce = null;

    const name = options.name;
    const value = options.value ?? '';
    const placeholder = options.placeholder ?? 'Write your post content here...';

    const csrfToken = () => document.querySelector('meta[name="csrf-token"]')?.content ?? '';

    const fetchMentions = (searchTerm, renderList) => {
        clearTimeout(mentionDebounce);
        mentionDebounce = setTimeout(() => {
            fetch(`/contacts/search?q=${encodeURIComponent(searchTerm)}`)
                .then((response) => (response.ok ? response.json() : []))
                .then((items) => renderList(items, searchTerm))
                .catch(() => renderList([], searchTerm));
        }, 200);
    };

    const insertMention = (item, insertItem) => {
        if (!item.remote) {
            insertItem(item);
            return;
        }

        fetch('/contacts/from-remote', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            body: JSON.stringify({
                platform: item.platform,
                name: item.name,
                handle: item.handle ? item.handle.replace(/^@/, '') : null,
                platform_uid: item.uid,
                avatar_url: item.avatar ?? null,
            }),
        })
            .then((response) => (response.ok ? response.json() : null))
            .then((contact) => insertItem({ ...item, id: contact?.id ?? null, value: contact?.name ?? item.value }))
            .catch(() => insertItem({ ...item, id: null }));
    };

    const mentionItem = (item) => {
        const node = document.createElement('div');
        node.className = 'ql-mention-item-row';

        const avatar = document.createElement('span');
        avatar.className = 'ql-mention-item-avatar';

        if (item.avatar) {
            const img = document.createElement('img');
            img.src = item.avatar;
            img.alt = '';
            avatar.appendChild(img);
        } else {
            avatar.textContent = (item.name || item.value || '@').charAt(0).toUpperCase();
        }

        const meta = document.createElement('span');
        meta.className = 'ql-mention-item-meta';

        const nameEl = document.createElement('span');
        nameEl.className = 'ql-mention-item-name';
        nameEl.textContent = item.value || item.name || '';

        const handle = document.createElement('span');
        handle.className = 'ql-mention-item-handle';
        handle.textContent = item.handle ?? '';

        meta.append(nameEl, handle);
        node.append(avatar, meta);

        return node;
    };

    return {
        name,
        value,
        placeholder,

        init() {
            this.$nextTick(() => {
                const editor = this.$refs.editor;
                const toolbar = this.$refs.toolbar;

                quill = new Quill(editor, {
                    theme: 'snow',
                    placeholder,
                    modules: {
                        toolbar,
                        mention: {
                            mentionDenotationChars: ['@'],
                            allowedChars: /^[A-Za-z0-9_ .-]*$/,
                            minChars: 1,
                            positioningStrategy: 'fixed',
                            source: fetchMentions,
                            renderItem: (item) => mentionItem(item),
                            onSelect: insertMention,
                        },
                        keyboard: {
                            bindings: {
                                tab: {
                                    key: 9,
                                    handler: () => {},
                                },
                            },
                        },
                    },
                });

                quill.clipboard.addMatcher('SPAN', (node, delta) => {
                    if (node.classList && node.classList.contains('ql-mention')) {
                        const data = { denotationChar: node.dataset.denotationChar ?? '@' };

                        ['id', 'value', 'denotationChar'].forEach((key) => {
                            if (node.dataset[key]) {
                                data[key] = node.dataset[key];
                            }
                        });

                        if (data.value && data.id) {
                            return { ops: [{ insert: { mention: data } }] };
                        }
                    }

                    return delta;
                });

                if (value) {
                    quill.clipboard.dangerouslyPasteHTML(value);
                }

                quill.on('text-change', () => this.sync());
                this.sync();
            });
        },

        sync() {
            this.$refs.input.value = quill.getSemanticHTML();
        },
    };
};
