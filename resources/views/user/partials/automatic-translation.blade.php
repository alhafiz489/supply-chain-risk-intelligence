@if (
    session('translation_mode') === 'automatic'
    && ! in_array(session('translation_target_locale'), ['en', 'id'], true)
)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const endpoint = @json(route('user.translate'));
            const csrfToken = @json(csrf_token());
            const translatedValues = new WeakMap();
            const queue = new Set();
            let timer = null;

            function canTranslate(node) {
                const parent = node.parentElement;
                const value = String(node.nodeValue || '').trim();

                return parent
                    && value.length >= 2
                    && /\p{L}/u.test(value)
                    && !parent.closest(
                        'script,style,code,pre,svg,[translate="no"],[contenteditable="true"]'
                    )
                    && translatedValues.get(node) !== value;
            }

            function enqueue(root) {
                const nodes = [];

                if (root.nodeType === Node.TEXT_NODE) {
                    nodes.push(root);
                } else {
                    const walker = document.createTreeWalker(
                        root,
                        NodeFilter.SHOW_TEXT
                    );

                    while (walker.nextNode()) {
                        nodes.push(walker.currentNode);
                    }
                }

                nodes.forEach(function (node) {
                    if (canTranslate(node)) {
                        queue.add(node);
                    }
                });

                window.clearTimeout(timer);
                timer = window.setTimeout(flush, 120);
            }

            async function flush() {
                const nodes = Array.from(queue).filter(canTranslate).slice(0, 50);
                nodes.forEach(node => queue.delete(node));

                if (nodes.length === 0) {
                    return;
                }

                const texts = [...new Set(nodes.map(
                    node => String(node.nodeValue || '').trim()
                ))];

                try {
                    const response = await fetch(endpoint, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ texts })
                    });
                    const result = await response.json();

                    if (!response.ok || !result.data) {
                        return;
                    }

                    nodes.forEach(function (node) {
                        const original = String(node.nodeValue || '').trim();
                        const translated = result.data[original];

                        if (translated && translated !== original) {
                            const leading = node.nodeValue.match(/^\s*/u)?.[0] || '';
                            const trailing = node.nodeValue.match(/\s*$/u)?.[0] || '';
                            translatedValues.set(node, translated);
                            node.nodeValue = leading + translated + trailing;
                        }
                    });
                } catch (error) {
                    console.warn('Dynamic translation unavailable.', error);
                } finally {
                    if (queue.size > 0) {
                        timer = window.setTimeout(flush, 120);
                    }
                }
            }

            new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    mutation.addedNodes.forEach(enqueue);

                    if (mutation.type === 'characterData') {
                        enqueue(mutation.target);
                    }
                });
            }).observe(document.body, {
                childList: true,
                characterData: true,
                subtree: true
            });
        });
    </script>
@endif
