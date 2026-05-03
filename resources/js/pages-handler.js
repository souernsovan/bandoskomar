class PagesHandler {
    constructor() {
        this.svgX = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
        this.svgPlus = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="14" height="14"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>';
        this.svgImg = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="drop-zone-icon"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25z" /></svg>';
        this.svgRemoveBtn = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>';
        this.svgChevron = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="16" height="16"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>';

        this.initSlugGeneration();
        this.initLanguageTabs();
        this.initSharedImageRemoval();
        this.initHomepageStyles();
        this.initPlatformFeatures();
    }

    toSlug(str) {
        return str.toLowerCase().trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    initSlugGeneration() {
        const titleInput = document.getElementById('translations_en_title');
        const slugInput = document.getElementById('slug');
        if (!titleInput || !slugInput) return;
        if (slugInput.disabled) return; // Slug is locked for special pages (home, platform, etc.)

        if (slugInput.value.trim()) {
            slugInput.dataset.manual = '1';
        }

        titleInput.addEventListener('input', () => {
            if (!slugInput.dataset.manual) {
                slugInput.value = this.toSlug(titleInput.value);
            }
        });

        slugInput.addEventListener('input', () => {
            if (slugInput.value) slugInput.dataset.manual = '1';
        });
    }

    switchLang(lang) {
        document.querySelectorAll('.lang-tab').forEach(t => {
            t.classList.toggle('active', t.dataset.lang === lang);
            t.setAttribute('aria-selected', t.dataset.lang === lang ? 'true' : 'false');
        });

        document.querySelectorAll('.lang-panel').forEach(panel => {
            const pid = panel.id || '';
            const isMatch = pid === 'lang-panel-' + lang
                || pid === 'lang-panel-create-title-' + lang
                || pid === 'lang-panel-create-' + lang
                || pid.endsWith('-' + lang);
            panel.classList.toggle('active', isMatch);
            panel.style.display = isMatch ? 'block' : 'none';
        });
    }

    initLanguageTabs() {
        const tabs = document.querySelectorAll('.lang-tab');
        if (!tabs.length) return;

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                this.switchLang(tab.dataset.lang);
            });
        });

        this.switchLang('en');
    }

    initSharedImageRemoval() {
        if (!document.querySelector('[data-shared-image]')) return;

        document.addEventListener('click', e => {
            const btn = e.target.closest('[data-remove-btn][data-shared-image]');
            if (!btn) return;

            const key = btn.getAttribute('data-shared-image');

            document.querySelectorAll('[data-shared-image="' + key + '"]').forEach(el => {
                if (el.tagName === 'INPUT') el.value = '';
            });

            document.querySelectorAll('.form-group[data-shared-image="' + key + '"]').forEach(fg => {
                const p = fg.querySelector('[data-preview]');
                const dz = fg.querySelector('.image-drop-zone');
                const dc = dz ? dz.querySelector('.drop-zone-content') : null;
                const rb = fg.querySelector('[data-remove-btn]');
                if (!p || !dz) return;
                p.innerHTML = '';
                p.classList.remove('has-image');
                dz.classList.remove('has-file');
                if (dc) dc.style.display = 'flex';
                if (rb) rb.style.display = 'none';
            });
        }, true);
    }

    // --- Homepage Style Showcase ---

    initHomepageStyles() {
        if (!document.querySelector('[data-add-style]')) return;

        this.initStyleShowcaseLangLabel();

        document.querySelectorAll('[data-add-style]').forEach(btn => {
            btn.addEventListener('click', () => {
                const locale = btn.getAttribute('data-locale');
                const list = document.getElementById('styleItemsList_' + locale);
                const empty = document.getElementById('styleItemsEmpty_' + locale);
                if (empty) empty.style.display = 'none';
                const si = list.querySelectorAll('.style-item-card').length;
                const card = document.createElement('div');
                card.className = 'style-item-card';
                card.setAttribute('data-style-index', si);
                card.innerHTML = this.buildStyleCardHtml(locale, si);
                list.appendChild(card);
                this.initStyleCard(card);
                if (typeof initImageUploadForContainer === 'function') {
                    initImageUploadForContainer(card);
                }
            });
        });

        document.querySelectorAll('.style-item-card').forEach(card => this.initStyleCard(card));
    }

    initStyleShowcaseLangLabel() {
        const styleLabel = document.getElementById('styleShowcaseLangLabel');
        if (!styleLabel) return;

        document.querySelectorAll('.lang-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                const name = tab.querySelector('.lang-tab-name');
                if (name) styleLabel.textContent = '(' + name.textContent + ')';
            });
        });
    }

    buildColorEntryHtml(locale, si, ci) {
        return '<div class="style-color-entry" data-color-index="' + ci + '">' +
            '<div class="style-color-entry-header">' +
            '<span class="style-color-entry-num">' + (ci + 1) + '</span>' +
            '<button type="button" class="style-color-entry-delete" data-delete-color title="Remove color">' +
            this.svgX + '</button>' +
            '</div>' +
            '<div class="style-color-entry-body style-color-entry-body--horizontal">' +
            '<div class="style-color-entry-left">' +
            '<div class="style-color-field">' +
            '<label class="form-label form-label-sm">Color Name</label>' +
            '<input type="text" class="form-input form-input-sm" name="homepage_sections[' + locale +
            '][styles][' + si + '][colors][' + ci +
            '][name]" value="" placeholder="e.g. Dark Emerald Green">' +
            '</div>' +
            '<div class="style-color-field">' +
            '<label class="form-label form-label-sm">Hex Color</label>' +
            '<div class="style-color-picker-wrap">' +
            '<input type="color" class="style-color-picker-input" name="homepage_sections[' + locale +
            '][styles][' + si + '][colors][' + ci + '][hex]" value="#6366f1">' +
            '<input type="text" class="form-input form-input-sm style-color-hex-text" value="#6366f1" placeholder="#000000" data-hex-text>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="style-color-entry-right">' +
            '<div class="form-group">' +
            '<label class="form-label">Preview Image</label>' +
            '<div class="image-upload-wrapper homepage-image-upload">' +
            '<div class="image-drop-zone" data-drop-zone>' +
            '<input type="file" name="homepage_sections[' + locale + '][style_' + si + '_color_' + ci +
            '_image_file]" class="image-file-input" data-file-input accept="image/jpeg,image/png,image/gif,image/webp">' +
            '<input type="hidden" name="homepage_sections[' + locale + '][styles][' + si + '][colors][' +
            ci + '][image]" value="">' +
            '<div class="drop-zone-content">' + this.svgImg +
            '<span class="drop-zone-text">Drag & drop image</span>' +
            '<span class="drop-zone-subtext">or click to browse</span>' +
            '<span class="drop-zone-hint">JPEG, PNG, GIF, WebP</span>' +
            '</div>' +
            '<div class="image-preview" data-preview></div>' +
            '<button type="button" class="remove-image-btn" data-remove-btn style="display:none;" title="Remove image">' +
            this.svgRemoveBtn + '</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>';
    }

    buildStyleCardHtml(locale, si) {
        const styleNum = String(si + 1).padStart(2, '0');
        return '<div class="style-item-header" data-toggle-style>' +
            '<span class="style-item-number">STYLE ' + styleNum + '</span>' +
            '<div class="style-item-header-actions">' +
            '<button type="button" class="style-item-toggle-btn" data-toggle-style-btn title="Expand / Collapse">' + this.svgChevron + '</button>' +
            '<button type="button" class="style-item-delete-btn" data-delete-style title="Remove this style">' +
            this.svgX.replace(/14/g, '16') + '</button>' +
            '</div>' +
            '</div>' +
            '<div class="style-item-body">' +
            '<div class="form-group">' +
            '<label class="form-label">Style Background Image</label>' +
            '<div class="image-upload-wrapper homepage-image-upload">' +
            '<div class="image-drop-zone" data-drop-zone>' +
            '<input type="file" name="homepage_sections[' + locale + '][style_' + si +
            '_image_file]" class="image-file-input" data-file-input accept="image/jpeg,image/png,image/gif,image/webp">' +
            '<input type="hidden" name="homepage_sections[' + locale + '][styles][' + si +
            '][image]" value="">' +
            '<div class="drop-zone-content">' + this.svgImg +
            '<span class="drop-zone-text">Drag & drop an image here</span>' +
            '<span class="drop-zone-subtext">or click to browse</span>' +
            '<span class="drop-zone-hint">JPEG, PNG, GIF, WebP • Max 10MB</span>' +
            '</div>' +
            '<div class="image-preview" data-preview></div>' +
            '<button type="button" class="remove-image-btn" data-remove-btn style="display:none;" title="Remove image">' +
            this.svgRemoveBtn + '</button>' +
            '</div>' +
            '</div>' +
            '</div>' +
            '<div class="form-group full-width">' +
            '<label class="form-label">Color Choices</label>' +
            '<div class="style-color-items" data-color-list data-style-index="' + si + '" data-locale="' +
            locale + '"></div>' +
            '<button type="button" class="btn btn-outline style-add-color-btn" data-add-color data-style-index="' +
            si + '" data-locale="' + locale + '">' + this.svgPlus + ' Add Color</button>' +
            '</div>' +
            '</div>';
    }

    initColorEntry(entry) {
        const deleteBtn = entry.querySelector('[data-delete-color]');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', () => {
                const list = deleteBtn.closest('[data-color-list]');
                deleteBtn.closest('.style-color-entry').remove();
                this.renumberColors(list);
            });
        }

        const colorInput = entry.querySelector('.style-color-picker-input');
        const hexText = entry.querySelector('[data-hex-text]');
        if (colorInput && hexText) {
            colorInput.addEventListener('input', () => {
                hexText.value = colorInput.value;
            });
            hexText.addEventListener('input', () => {
                if (/^#[0-9a-fA-F]{6}$/.test(hexText.value)) {
                    colorInput.value = hexText.value;
                }
            });
        }
    }

    initStyleCard(card) {
        const deleteBtn = card.querySelector('[data-delete-style]');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', e => {
                e.stopPropagation();
                const list = card.parentElement;
                card.remove();
                this.renumberStyles(list);
            });
        }

        const header = card.querySelector('[data-toggle-style]');
        if (header) {
            header.addEventListener('click', e => {
                if (e.target.closest('[data-delete-style]')) return;
                card.classList.toggle('is-collapsed');
            });
        }

        card.querySelectorAll('[data-add-color]').forEach(btn => this.bindAddColor(btn));
        card.querySelectorAll('.style-color-entry').forEach(entry => this.initColorEntry(entry));
    }

    bindAddColor(btn) {
        btn.addEventListener('click', () => {
            const si = parseInt(btn.getAttribute('data-style-index'));
            const locale = btn.getAttribute('data-locale');
            let list = btn.previousElementSibling;
            if (!list || !list.hasAttribute('data-color-list')) {
                list = btn.closest('.form-group').querySelector('[data-color-list]');
            }
            const ci = list.querySelectorAll('.style-color-entry').length;
            const wrapper = document.createElement('div');
            wrapper.innerHTML = this.buildColorEntryHtml(locale, si, ci);
            const el = wrapper.firstElementChild;
            list.appendChild(el);
            this.initColorEntry(el);
            if (typeof initImageUploadForContainer === 'function') {
                initImageUploadForContainer(el);
            }
        });
    }

    renumberColors(list) {
        list.querySelectorAll('.style-color-entry').forEach((entry, ci) => {
            entry.setAttribute('data-color-index', ci);
            entry.querySelector('.style-color-entry-num').textContent = ci + 1;
            entry.querySelectorAll('input[name]').forEach(inp => {
                inp.name = inp.name
                    .replace(/\[colors\]\[\d+\]/, '[colors][' + ci + ']')
                    .replace(/_color_\d+_/, '_color_' + ci + '_');
            });
        });
    }

    renumberStyles(list) {
        const cards = list.querySelectorAll('.style-item-card');
        if (cards.length === 0) {
            const empty = list.querySelector('.style-items-empty');
            if (empty) empty.style.display = '';
        }
        cards.forEach((card, i) => {
            card.setAttribute('data-style-index', i);
            card.querySelector('.style-item-number').textContent = 'STYLE ' + String(i + 1).padStart(2, '0');
            card.querySelectorAll('[data-color-list]').forEach(cl => {
                cl.setAttribute('data-style-index', i);
            });
            card.querySelectorAll('[data-add-color]').forEach(ab => {
                ab.setAttribute('data-style-index', i);
            });
            card.querySelectorAll('input[name]').forEach(inp => {
                inp.name = inp.name
                    .replace(/\[styles\]\[\d+\]/, '[styles][' + i + ']')
                    .replace(/\[style_\d+_/, '[style_' + i + '_');
            });
        });
    }

    // --- Platform page: Our Features (dynamic rows per locale) ---

    initPlatformFeatures() {
        if (!document.querySelector('[data-platform-features-list]')) {
            return;
        }

        document.querySelectorAll('[data-add-platform-feature]').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                const locale = btn.getAttribute('data-locale');
                const list = document.getElementById('platformFeaturesList_' + locale);
                if (!list) {
                    return;
                }
                this.appendPlatformFeatureFromTemplate(list);
            });
        });

        document.addEventListener('click', e => {
            const rm = e.target.closest('[data-remove-platform-feature]');
            if (!rm) {
                return;
            }
            const row = rm.closest('[data-platform-feature-row]');
            const list = row?.closest('[data-platform-features-list]');
            if (!row || !list) {
                return;
            }
            e.preventDefault();
            row.remove();
            this.renumberPlatformFeatureRows(list);
            if (!list.querySelectorAll('[data-platform-feature-row]').length) {
                this.appendPlatformFeatureFromTemplate(list);
            }
        });
    }

    appendPlatformFeatureFromTemplate(list) {
        const locale = list.getAttribute('data-locale');
        const tpl = document.getElementById('platformFeatureRowTpl_' + locale);
        if (!tpl || !tpl.content) {
            return;
        }
        const frag = tpl.content.cloneNode(true);
        const row = frag.querySelector('[data-platform-feature-row]');
        if (!row) {
            return;
        }
        list.appendChild(row);
        this.renumberPlatformFeatureRows(list);
    }

    renumberPlatformFeatureRows(list) {
        list.querySelectorAll('[data-platform-feature-row]').forEach((row, i) => {
            const numEl = row.querySelector('.platform-feature-num');
            if (numEl) {
                numEl.textContent = String(i + 1);
            }
            row.querySelectorAll('[name]').forEach(inp => {
                const m = inp.name.match(
                    /^platform_sections\[([^\]]+)\]\[features\]\[[^\]]+\]\[(title|color|icon)\]$/
                );
                if (m) {
                    inp.name = 'platform_sections[' + m[1] + '][features][' + i + '][' + m[2] + ']';
                }
            });
        });
    }
}

export default PagesHandler;
