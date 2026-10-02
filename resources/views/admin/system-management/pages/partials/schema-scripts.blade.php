<script>
(() => {
    const editor = document.querySelector('[data-sc-editor]');
    if (!editor) return;

    let counter = 0;

    const renumber = list => {
        const items = list.querySelectorAll(':scope > [data-sc-items] > [data-sc-item]');
        items.forEach((item, i) => {
            const num = item.querySelector('[data-sc-num]');
            if (num) num.textContent = i + 1;
        });
        const count = list.querySelector('[data-sc-count]');
        if (count) count.textContent = items.length;
    };

    const addItem = list => {
        const template = list.querySelector(':scope > template[data-sc-template]');
        const index = 'n' + Date.now() + '_' + (counter++);
        const wrapper = document.createElement('div');
        wrapper.innerHTML = template.innerHTML.replaceAll('__INDEX__', index);
        const item = wrapper.firstElementChild;
        list.querySelector(':scope > [data-sc-items]').appendChild(item);
        renumber(list);
        return item;
    };

    const showPreview = (media, file) => {
        const preview = media.querySelector('[data-sc-media-preview]');
        if (!file) return;
        if (media.dataset.kind === 'image') {
            preview.innerHTML = '';
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            preview.appendChild(img);
        } else {
            preview.textContent = file.name;
        }
    };

    editor.addEventListener('click', e => {
        const button = e.target.closest('[data-sc-add], [data-sc-remove], [data-sc-up], [data-sc-down], [data-sc-media-clear]');
        if (!button) return;
        e.preventDefault();

        if (button.matches('[data-sc-add]')) {
            addItem(button.closest('[data-sc-list]'));
            return;
        }

        if (button.matches('[data-sc-media-clear]')) {
            const media = button.closest('[data-sc-media]');
            media.querySelector('[data-sc-media-path]').value = '';
            media.querySelector('[data-sc-media-file]').value = '';
            media.querySelector('[data-sc-media-preview]').innerHTML = '<span>Removed</span>';
            return;
        }

        const item = button.closest('[data-sc-item]');
        const list = item.closest('[data-sc-list]');
        if (button.matches('[data-sc-remove]')) {
            if (!confirm('Remove this item? It is deleted when you click Update.')) return;
            item.remove();
        } else if (button.matches('[data-sc-up]') && item.previousElementSibling) {
            item.parentNode.insertBefore(item, item.previousElementSibling);
        } else if (button.matches('[data-sc-down]') && item.nextElementSibling) {
            item.parentNode.insertBefore(item.nextElementSibling, item);
        }
        renumber(list);
    });

    editor.addEventListener('change', e => {
        const input = e.target;

        if (input.matches('[data-sc-media-file]') && input.files.length) {
            showPreview(input.closest('[data-sc-media]'), input.files[0]);
            return;
        }

        // Add one list item per selected image.
        if (input.matches('[data-sc-bulk]') && input.files.length) {
            const list = input.closest('[data-sc-list]');
            const key = input.dataset.scBulk;
            Array.from(input.files).forEach(file => {
                const item = addItem(list);
                const fileInput = item.querySelector(`[data-sc-media-file][name$="[${key}]"]`);
                if (!fileInput) return;
                const transfer = new DataTransfer();
                transfer.items.add(file);
                fileInput.files = transfer.files;
                showPreview(fileInput.closest('[data-sc-media]'), file);
                item.open = false;
            });
            input.value = '';
        }
    });

    // Empty file inputs are not sent, so many unused upload boxes never hit PHP's upload limit.
    editor.closest('form').addEventListener('submit', () => {
        editor.querySelectorAll('input[type=file]').forEach(input => {
            if (!input.files.length) input.disabled = true;
        });
    });
})();
</script>
