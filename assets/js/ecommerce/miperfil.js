document.addEventListener("DOMContentLoaded", function () {
    const dropdownToggle = document.querySelector('.dropdown-toggle, [data-bs-toggle="dropdown"]');
    if (dropdownToggle) {
        dropdownToggle.addEventListener('click', function (e) {
            e.preventDefault();
            const parent = this.closest('.dropdown');
            if (parent) {
                parent.classList.toggle('show');
                const menu = parent.querySelector('.dropdown-menu');
                if (menu) {
                    menu.classList.toggle('show');
                }
            }
        });

        document.addEventListener('click', function (e) {
            if (!dropdownToggle.contains(e.target)) {
                const parent = dropdownToggle.closest('.dropdown');
                if (parent) {
                    parent.classList.remove('show');
                    const menu = parent.querySelector('.dropdown-menu');
                    if (menu) {
                        menu.classList.remove('show');
                    }
                }
            }
        });
    }
});