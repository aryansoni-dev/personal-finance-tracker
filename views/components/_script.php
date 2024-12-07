<script>
    AOS.init({
        duration: 1000,
        once: true,
    });

    // Mobile menu functionality
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        let isMenuOpen = false;

        mobileMenuButton.addEventListener('click', function(e) {
            e.stopPropagation();
            isMenuOpen = !isMenuOpen;
            if (isMenuOpen) {
                mobileMenu.classList.add('active');
                this.innerHTML = '<span class="material-icons">close</span>';
            } else {
                mobileMenu.classList.remove('active');
                this.innerHTML = '<span class="material-icons">menu</span>';
            }
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!mobileMenu.contains(e.target) && !mobileMenuButton.contains(e.target) && isMenuOpen) {
                isMenuOpen = false;
                mobileMenu.classList.remove('active');
                mobileMenuButton.innerHTML = '<span class="material-icons">menu</span>';
            }
        });

        // Prevent menu from closing when clicking inside it
        mobileMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });

        const editBtn = document.getElementById('edit-btn');
        const saveBtn = document.getElementById('update-profile-btn');
        const cancelBtn = document.getElementById('cancel-update-profile-btn');
        const formFields = document.querySelectorAll('#profile-form input');

        if (editBtn) {
            editBtn.addEventListener('click', () => {
                formFields.forEach(field => {
                    if (field.id != 'joined') {
                        field.disabled = false;
                        field.classList.remove('bg-gray-100');
                        field.style.cursor = 'text';
                    }
                    // console.log(field.id);
                    editBtn.classList.add('hidden');
                    saveBtn.classList.remove('hidden');
                    cancelBtn.classList.remove('hidden');
                });
            });
        }

        if (cancelBtn) {
            cancelBtn.addEventListener('click', () => {
                formFields.forEach(field => {
                    field.disabled = true;
                    field.classList.add('bg-gray-200');
                    field.style.cursor = 'not-allowed';
                });
                saveBtn.classList.add('hidden');
                editBtn.classList.remove('hidden');
                cancelBtn.classList.add('hidden');
            });
        }
        // const aside_links = document.getElementsByClassName('aside-a');
        // if (aside_links) {
        //     aside_links.forEach(link => {
        //         link.addEventListener('click', () => {
        //             link.classList.add('viewing');
        //         });
        //     });
        // }
    });
</script>