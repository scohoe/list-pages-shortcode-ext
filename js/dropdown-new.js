document.addEventListener('DOMContentLoaded', function() {
    // Initialize all dropdowns
    function initializeDropdowns() {
        document.querySelectorAll('.dropdown-toggle').forEach(function(item) {
            const hasChildren = item.getAttribute('data-has-children') === 'true';
            if (hasChildren) {
                const toggleIcon = item.querySelector('.dropdown-toggle-icon');
                const childrenList = item.querySelector('.children');

                // Set initial state
                if (childrenList) {
                    childrenList.style.display = 'none';
                    toggleIcon.textContent = '+';
                }

                // Add click event
                if (toggleIcon) {
                    toggleIcon.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        if (childrenList) {
                            const isVisible = childrenList.style.display === 'block';
                            childrenList.style.display = isVisible ? 'none' : 'block';
                            toggleIcon.textContent = isVisible ? '+' : '-';
                        }
                    });
                }
            }
        });
    }

    // Initialize on page load
    initializeDropdowns();

    // Re-initialize when content changes (for dynamic loading)
    if (typeof MutationObserver !== 'undefined') {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    initializeDropdowns();
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }
}));}