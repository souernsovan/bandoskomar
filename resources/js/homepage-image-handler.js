/**
 * Handles multiple image drop zones on the homepage edit form.
 * Uses the same UI template as product image upload (image-drop-zone).
 */
class HomepageImageHandler {
    constructor(options = {}) {
        this.maxSize = options.maxSize || 10 * 1024 * 1024; // 10MB
        this.maxSizeMB = this.maxSize / (1024 * 1024);
        this.acceptedTypes = options.acceptedTypes || ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        this.zones = [];
    }

    init() {
        const form = document.querySelector('form[data-staged-upload-url]');
        if (form) {
            this.initStagedSubmit(form);
        }
        this.initSingleUploads();
        this.initPartnersUpload();
        this.initPartnersCurrentRemove();

        window.initImageUploadForContainer = (container) => {
            this.initSingleUploadsIn(container);
        };
    }

    initSingleUploadsIn(container) {
        container.querySelectorAll('.homepage-image-upload').forEach(wrapper => {
            const dropZone = wrapper.closest('.form-group')?.querySelector('[data-drop-zone]') || wrapper.querySelector('[data-drop-zone]');
            const fileInput = wrapper.querySelector('[data-file-input]');
            const preview = wrapper.querySelector('[data-preview]');
            const removeBtn = wrapper.querySelector('[data-remove-btn]');
            const dropZoneContent = dropZone?.querySelector('.drop-zone-content');
            const errorEl = wrapper.querySelector('[data-error]');
            const pathInput = wrapper.closest('.form-group')?.querySelector('[data-path-input]');

            if (dropZone && fileInput && preview && !dropZone._hpBound) {
                dropZone._hpBound = true;
                this.zones.push({ dropZone, fileInput, preview, removeBtn, dropZoneContent, errorEl, pathInput });
                this.bindZone({ dropZone, fileInput, preview, removeBtn, dropZoneContent, errorEl, pathInput });
            }
        });

        container.querySelectorAll('[data-drop-zone]').forEach(dropZone => {
            if (dropZone._hpBound) return;
            const wrapper = dropZone.closest('.image-upload-wrapper');
            if (!wrapper) return;
            const fileInput = wrapper.querySelector('.image-file-input') || wrapper.querySelector('[data-file-input]');
            const preview = wrapper.querySelector('[data-preview]');
            const removeBtn = wrapper.querySelector('[data-remove-btn]');
            const dropZoneContent = dropZone.querySelector('.drop-zone-content');
            const errorEl = wrapper.querySelector('[data-error]');
            const pathInput = wrapper.closest('.form-group')?.querySelector('[data-path-input]');

            if (fileInput && preview) {
                dropZone._hpBound = true;
                this.zones.push({ dropZone, fileInput, preview, removeBtn, dropZoneContent, errorEl, pathInput });
                this.bindZone({ dropZone, fileInput, preview, removeBtn, dropZoneContent, errorEl, pathInput });
            }
        });
    }

    initPartnersCurrentRemove() {
        document.querySelectorAll('[data-partner-remove]').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const item = btn.closest('.partners-current-item');
                const checkbox = item?.querySelector('.partner-keep-checkbox');
                const value = checkbox?.value;
                if (value === undefined || value === '') {
                    return;
                }
                // Same image can appear in every language tab; drop all rows for this path (like removing one pending file).
                document.querySelectorAll('.partners-current-item').forEach((row) => {
                    const cb = row.querySelector('.partner-keep-checkbox');
                    if (cb && cb.value === value) {
                        row.remove();
                    }
                });
                this.syncEmptyPartnersCurrentSections();
            });
        });
    }

    /**
     * Hide "Current …" blocks when their list has no items left (all languages).
     */
    syncEmptyPartnersCurrentSections() {
        document.querySelectorAll('.partners-current-list').forEach((list) => {
            const group = list.closest('.form-group');
            if (!group) {
                return;
            }
            group.style.display = list.children.length === 0 ? 'none' : '';
        });
    }

    initSingleUploads() {
        this.initSingleUploadsIn(document);
    }

    initPartnersUpload() {
        const store = (window._partnersPendingStore = window._partnersPendingStore || {});
        const wrappers = document.querySelectorAll('.homepage-partners-upload');
        const self = this;

        wrappers.forEach((wrapper) => {
            const inputName = wrapper.dataset.partnersInputName || 'homepage_partner_image_files[]';
            if (!store[inputName]) store[inputName] = [];
            const stagedPurpose = wrapper.dataset.stagedPurpose || '';
            if (stagedPurpose) {
                window._partnersStagedPurposeByInput = window._partnersStagedPurposeByInput || {};
                window._partnersStagedPurposeByInput[inputName] = stagedPurpose;
            }

            const dropZone = wrapper.querySelector('[data-partners-drop-zone]');
            const fileInput = wrapper.querySelector('[data-partners-file-input]');
            const preview = wrapper.querySelector('[data-partners-preview]');
            const emptyState = wrapper.querySelector('[data-partners-empty-state]');
            const errorEl = wrapper.querySelector('[data-partners-error]');
            if (!dropZone || !fileInput || !preview) return;

            const pendingFiles = store[inputName];

            const syncAllZones = () => {
                const selector = '.homepage-partners-upload[data-partners-input-name="' + this.cssEscape(inputName) + '"]';
                const allWrappers = document.querySelectorAll(selector);
                const dt = new DataTransfer();
                pendingFiles.forEach(f => dt.items.add(f));
                allWrappers.forEach((w, idx) => {
                    const dz = w.querySelector('[data-partners-drop-zone]');
                    const inp = w.querySelector('[data-partners-file-input]');
                    const prev = w.querySelector('[data-partners-preview]');
                    const empty = w.querySelector('[data-partners-empty-state]');
                    if (dz && inp && prev) {
                        // Only the first zone's input gets the files to avoid 6x duplicate form submission
                        inp.files = idx === 0 ? dt.files : new DataTransfer().files;
                        self.renderPartnersPreview(pendingFiles, inp, prev, empty, dz, syncAllZones);
                    }
                });
            };
            wrapper._syncPartners = syncAllZones;

            const prevent = (e) => { e.preventDefault(); e.stopPropagation(); };
            const highlight = () => dropZone.classList.add('drag-over');
            const unhighlight = () => dropZone.classList.remove('drag-over');

            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(ev => dropZone.addEventListener(ev, prevent, false));
            ['dragenter', 'dragover'].forEach(ev => dropZone.addEventListener(ev, highlight, false));
            ['dragleave', 'drop'].forEach(ev => dropZone.addEventListener(ev, unhighlight, false));

            dropZone.addEventListener('drop', (e) => {
                const files = e.dataTransfer?.files ? Array.from(e.dataTransfer.files) : [];
                this.addPartnersFiles(files, pendingFiles, fileInput, preview, emptyState, errorEl, dropZone);
                syncAllZones();
            }, false);

            dropZone.addEventListener('click', (e) => {
                if (e.target.closest('.remove-image-btn') || e.target.closest('input[type="file"]')) return;
                e.preventDefault();
                fileInput.click();
            });

            fileInput.addEventListener('change', (e) => {
                const files = e.target.files ? Array.from(e.target.files) : [];
                this.addPartnersFiles(files, pendingFiles, fileInput, preview, emptyState, errorEl, dropZone);
                syncAllZones();
            });

            syncAllZones();
        });
    }

    cssEscape(str) {
        return String(str).replace(/["\\]/g, '\\$&');
    }

    /**
     * Upload pending multi-image files one request each before the main page save (PUT).
     */
    initStagedSubmit(form) {
        if (form._stagedSubmitBound) {
            return;
        }
        form._stagedSubmitBound = true;

        const purposeToHidden = {
            homepage_partner: 'homepage_staged_partner_paths[]',
            platform_slider: 'platform_staged_slider_paths[]',
            product_partner: 'product_staged_partner_paths[]',
        };

        form.addEventListener('submit', async (e) => {
            const uploadUrl = form.dataset.stagedUploadUrl;
            if (!uploadUrl) {
                return;
            }

            const tasks = this.collectStagedUploadTasks(purposeToHidden);
            if (tasks.length === 0) {
                return;
            }

            e.preventDefault();

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!token) {
                this.showStagedError('Missing CSRF token. Refresh the page and try again.');
                return;
            }

            form.querySelectorAll('input[data-staged-hidden]').forEach((el) => el.remove());

            if (typeof window.SwalAlert !== 'undefined') {
                window.SwalAlert.loading('Uploading images…');
            }

            try {
                for (const task of tasks) {
                    for (const file of task.files) {
                        const body = new FormData();
                        body.append('file', file);
                        body.append('purpose', task.purpose);

                        const res = await fetch(uploadUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'X-Requested-With': 'XMLHttpRequest',
                                Accept: 'application/json',
                            },
                            body,
                            credentials: 'same-origin',
                        });

                        const data = await res.json().catch(() => ({}));
                        if (!res.ok) {
                            const msg = data.message || (data.errors && Object.values(data.errors).flat().join(' ')) || res.statusText || 'Upload failed';
                            throw new Error(typeof msg === 'string' ? msg : 'Upload failed');
                        }
                        if (!data.path) {
                            throw new Error('Invalid server response');
                        }

                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = task.fieldName;
                        hidden.value = data.path;
                        hidden.setAttribute('data-staged-hidden', '1');
                        form.appendChild(hidden);
                    }
                }

                const pendingStore = window._partnersPendingStore || {};
                Object.keys(pendingStore).forEach((k) => {
                    pendingStore[k].length = 0;
                });
                document.querySelectorAll('.homepage-partners-upload').forEach((w) => {
                    if (typeof w._syncPartners === 'function') {
                        w._syncPartners();
                    }
                });

                if (typeof window.SwalAlert !== 'undefined') {
                    window.SwalAlert.dismiss();
                }
                form.submit();
            } catch (err) {
                if (typeof window.SwalAlert !== 'undefined') {
                    window.SwalAlert.dismiss();
                }
                this.showStagedError(err.message || 'Upload failed');
            }
        });
    }

    collectStagedUploadTasks(purposeToHidden) {
        const purposeMap = window._partnersStagedPurposeByInput || {};
        const store = window._partnersPendingStore || {};
        const tasks = [];

        for (const inputName of Object.keys(store)) {
            const files = store[inputName];
            if (!files || files.length === 0) {
                continue;
            }
            const purpose = purposeMap[inputName];
            const fieldName = purpose && purposeToHidden[purpose];
            if (!purpose || !fieldName) {
                continue;
            }
            tasks.push({
                purpose,
                fieldName,
                inputName,
                files: Array.from(files),
            });
        }

        return tasks;
    }

    showStagedError(msg) {
        if (typeof window.SwalAlert !== 'undefined') {
            window.SwalAlert.error('Upload Error', msg);
        } else {
            alert(msg);
        }
    }

    addPartnersFiles(newFiles, pendingFiles, fileInput, preview, emptyState, errorEl, dropZone) {
        if (errorEl) { errorEl.textContent = ''; errorEl.style.display = 'none'; }
        const maxSize = this.maxSize;
        const accepted = this.acceptedTypes;
        const isDuplicateInPending = (file) =>
            pendingFiles.some(
                (f) => f.name === file.name && f.size === file.size
            );
        for (const file of newFiles) {
            if (!accepted.includes(file.type)) {
                this.showError(`Invalid file type: ${file.name}. Use JPEG, PNG, GIF, or WebP.`, errorEl);
                continue;
            }
            if (file.size > maxSize) {
                this.showError(`File ${file.name} exceeds 10MB.`, errorEl);
                continue;
            }
            if (isDuplicateInPending(file)) {
                continue;
            }
            pendingFiles.push(file);
        }
        const wrapper = dropZone.closest('.homepage-partners-upload');
        const syncCb = wrapper?._syncPartners;
        this.renderPartnersPreview(pendingFiles, fileInput, preview, emptyState, dropZone, syncCb);
    }

    renderPartnersPreview(pendingFiles, fileInput, preview, emptyState, dropZone, syncAllZones) {
        const dt = new DataTransfer();
        pendingFiles.forEach(f => dt.items.add(f));
        fileInput.files = dt.files;
        if (pendingFiles.length === 0) {
            preview.style.display = 'none';
            preview.innerHTML = '';
            emptyState.style.display = 'flex';
            dropZone.classList.remove('has-file');
            preview.classList.remove('has-image');
            return;
        }
        emptyState.style.display = 'none';
        dropZone.classList.add('has-file');
        preview.style.display = 'grid';
        preview.classList.add('has-image');
        const items = pendingFiles.map((file, i) => {
            const name = this.truncate(file.name, 18);
            const size = this.formatSize(file.size);
            return `<div class="image-preview-item" data-partners-index="${i}">
                <button type="button" class="remove-image-btn" data-partners-remove title="Remove"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></button>
                <img src="#" alt="" data-partners-thumb>
                <div class="image-preview-info">
                    <span class="image-name">${name}</span>
                    <span class="image-size">${size}</span>
                </div>
            </div>`;
        }).join('');
        preview.innerHTML = items;
        pendingFiles.forEach((file, i) => {
            const reader = new FileReader();
            reader.onload = (ev) => {
                const item = preview.querySelector(`[data-partners-index="${i}"]`);
                if (item) item.querySelector('[data-partners-thumb]').src = ev.target.result;
            };
            reader.readAsDataURL(file);
        });
        const self = this;
        preview.querySelectorAll('[data-partners-remove]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const i = parseInt(btn.closest('.image-preview-item').dataset.partnersIndex, 10);
                pendingFiles.splice(i, 1);
                if (typeof syncAllZones === 'function') {
                    syncAllZones();
                } else {
                    self.renderPartnersPreview(pendingFiles, fileInput, preview, emptyState, dropZone);
                }
            });
        });
    }

    bindZone({ dropZone, fileInput, preview, removeBtn, dropZoneContent, errorEl, pathInput }) {
        const prevent = (e) => { e.preventDefault(); e.stopPropagation(); };
        const highlight = () => dropZone.classList.add('drag-over');
        const unhighlight = () => dropZone.classList.remove('drag-over');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(ev => dropZone.addEventListener(ev, prevent, false));
        ['dragenter', 'dragover'].forEach(ev => dropZone.addEventListener(ev, highlight, false));
        ['dragleave', 'drop'].forEach(ev => dropZone.addEventListener(ev, unhighlight, false));
        dropZone.addEventListener('drop', (e) => {
            const file = e.dataTransfer?.files?.[0];
            if (file) this.handleFile(file, { dropZone, fileInput, preview, removeBtn, dropZoneContent, errorEl, pathInput });
        }, false);

        dropZone.addEventListener('click', (e) => {
            if (e.target.closest('input[type="file"]') || e.target.closest('.remove-image-btn') ||
                dropZone.classList.contains('has-file')) return;
            e.preventDefault();
            fileInput.click();
        });

        fileInput.addEventListener('change', (e) => {
            const file = e.target.files?.[0];
            if (file) this.handleFile(file, { dropZone, fileInput, preview, removeBtn, dropZoneContent, errorEl, pathInput });
        });

        if (removeBtn) {
            removeBtn.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                fileInput.value = '';
                preview.innerHTML = '';
                preview.classList.remove('has-image');
                dropZone.classList.remove('has-file');
                if (dropZoneContent) dropZoneContent.style.display = 'flex';
                removeBtn.style.display = 'none';
                if (errorEl) { errorEl.textContent = ''; errorEl.style.display = 'none'; }
                if (pathInput) {
                    pathInput.value = '';
                    pathInput.style.display = 'none';
                }
                const sharedKey = removeBtn.dataset.sharedImage || (pathInput && pathInput.dataset.sharedImage);
                const currentFormGroup = dropZone.closest('.form-group');
                if (sharedKey) {
                    document.querySelectorAll(`[data-shared-image="${sharedKey}"]`).forEach(el => {
                        if (el.tagName === 'INPUT') el.value = '';
                    });
                    document.querySelectorAll(`.form-group[data-shared-image="${sharedKey}"]`).forEach(fg => {
                        if (fg === currentFormGroup) return;
                        const p = fg.querySelector('[data-preview]');
                        const dz = fg.querySelector('.image-drop-zone');
                        const dc = dz?.querySelector('.drop-zone-content');
                        const rb = fg.querySelector('[data-remove-btn]');
                        if (p && dz) {
                            p.innerHTML = '';
                            p.classList.remove('has-image');
                            dz.classList.remove('has-file');
                            if (dc) dc.style.display = 'flex';
                            if (rb) rb.style.display = 'none';
                        }
                    });
                }
            });
        }
    }

    handleFile(file, { dropZone, fileInput, preview, removeBtn, dropZoneContent, errorEl, pathInput }) {
        if (errorEl) { errorEl.textContent = ''; errorEl.style.display = 'none'; }

        if (!this.acceptedTypes.includes(file.type)) {
            this.showError(`Invalid file type. Please upload JPEG, PNG, GIF, or WebP.`, errorEl);
            return;
        }
        if (file.size > this.maxSize) {
            const mb = (file.size / (1024 * 1024)).toFixed(2);
            this.showError(`File (${mb}MB) exceeds max ${this.maxSizeMB}MB.`, errorEl);
            return;
        }

        const previewHtml = (dataUrl) => `
                <div class="image-preview-item">
                    <img src="${dataUrl}" alt="Preview">
                    <div class="image-preview-info">
                        <span class="image-name">${this.truncate(file.name, 30)}</span>
                        <span class="image-size">${this.formatSize(file.size)}</span>
                    </div>
                </div>`;
        const reader = new FileReader();
        reader.onload = (e) => {
            const dataUrl = e.target.result;
            preview.innerHTML = previewHtml(dataUrl);
            preview.classList.add('has-image');
            dropZone.classList.add('has-file');
            if (dropZoneContent) dropZoneContent.style.display = 'none';
            if (removeBtn) removeBtn.style.display = 'flex';
            if (pathInput) pathInput.style.display = 'none';
            const dt = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;

            const sharedKey = removeBtn?.dataset?.sharedImage || pathInput?.dataset?.sharedImage;
            const currentFormGroup = dropZone.closest('.form-group');
            if (sharedKey) {
                document.querySelectorAll(`.form-group[data-shared-image="${sharedKey}"]`).forEach(fg => {
                    if (fg === currentFormGroup) return;
                    const p = fg.querySelector('[data-preview]');
                    const dz = fg.querySelector('.image-drop-zone');
                    const dc = dz?.querySelector('.drop-zone-content');
                    const rb = fg.querySelector('[data-remove-btn]');
                    if (p && dz) {
                        p.innerHTML = previewHtml(dataUrl);
                        p.classList.add('has-image');
                        dz.classList.add('has-file');
                        if (dc) dc.style.display = 'none';
                        if (rb) rb.style.display = 'flex';
                    }
                });
            }
        };
        reader.readAsDataURL(file);
    }

    showError(msg, errorEl) {
        if (typeof window.SwalAlert !== 'undefined') {
            window.SwalAlert.error('Upload Error', msg);
        } else if (errorEl) {
            errorEl.textContent = msg;
            errorEl.style.display = 'block';
        } else {
            alert('Upload Error: ' + msg);
        }
    }

    truncate(str, n) {
        if (str.length <= n) return str;
        const ext = str.split('.').pop();
        return str.substring(0, n - ext.length - 4) + '...' + ext;
    }

    formatSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024, sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}

export default HomepageImageHandler;
