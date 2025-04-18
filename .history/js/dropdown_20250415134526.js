document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.dropdown-toggle-icon').forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const pageId = this.getAttribute('data-page-id');
            const listItem = this.closest('.page_item');
            const childrenList = listItem.querySelector('.children');
            
            if (childrenList) {
                childrenList.classList.toggle('visible');
                this.textContent = childrenList.classList.contains('visible') ? '-' : '+';
            }
        });
    });
});