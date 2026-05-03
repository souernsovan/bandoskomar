class CategoriesHandler {
    constructor() {
        this.nameInput = document.getElementById('name');
        this.slugInput = document.getElementById('slug');
        this.isEdit = !!(this.slugInput && this.slugInput.value.trim());

        if (this.nameInput && this.slugInput) {
            this.bindEvents();
        }
    }

    toSlug(str) {
        return str
            .toLowerCase()
            .trim()
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    }

    bindEvents() {
        if (!this.isEdit) {
            this.nameInput.addEventListener('input', () => {
                this.slugInput.value = this.toSlug(this.nameInput.value);
            });
        }
    }
}

export default CategoriesHandler;
