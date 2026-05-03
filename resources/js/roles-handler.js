class RolesHandler {
    constructor() {
        this.checkAlls = document.querySelectorAll('.permission-check-all');
        if (!this.checkAlls.length) return;

        this.bindCheckAll();
        this.bindItems();
        this.syncIndeterminate();
    }

    bindCheckAll() {
        this.checkAlls.forEach(checkAll => {
            checkAll.addEventListener('change', () => {
                const group = checkAll.dataset.group;
                document.querySelectorAll('.permission-item[data-group="' + group + '"]')
                    .forEach(cb => cb.checked = checkAll.checked);
            });
        });
    }

    bindItems() {
        document.querySelectorAll('.permission-item').forEach(item => {
            item.addEventListener('change', () => {
                const group = item.dataset.group;
                const items = document.querySelectorAll('.permission-item[data-group="' + group + '"]');
                const checkAll = document.querySelector('.permission-check-all[data-group="' + group + '"]');
                const checked = document.querySelectorAll('.permission-item[data-group="' + group + '"]:checked');
                checkAll.checked = checked.length === items.length;
                checkAll.indeterminate = checked.length > 0 && checked.length < items.length;
            });
        });
    }

    syncIndeterminate() {
        this.checkAlls.forEach(checkAll => {
            const group = checkAll.dataset.group;
            const items = document.querySelectorAll('.permission-item[data-group="' + group + '"]');
            const checked = document.querySelectorAll('.permission-item[data-group="' + group + '"]:checked');
            if (checked.length > 0 && checked.length < items.length) {
                checkAll.indeterminate = true;
            }
        });
    }
}

export default RolesHandler;
