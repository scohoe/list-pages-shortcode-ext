function toggleDropdown(button) {
    // Toggle the 'rotated' class on the button
    button.classList.toggle('rotated');
    
    // Find the dropdown menu (next sibling after the button)
    var dropdownMenu = button.nextElementSibling;
    
    // Toggle the 'visible' class on the dropdown menu
    if (dropdownMenu && dropdownMenu.classList.contains('children')) {
        dropdownMenu.classList.toggle('visible');
        
        // Update aria-expanded attribute
        var isExpanded = dropdownMenu.classList.contains('visible');
        button.setAttribute('aria-expanded', isExpanded);
    }
    
    // Prevent the click event from bubbling up
    event.stopPropagation();
}

// Add this to ensure links in dropdown menus are clickable
document.addEventListener('DOMContentLoaded', function() {
    // Make sure dropdown menu items are clickable
    var dropdownMenus = document.querySelectorAll('.children.dropdown-menu');
    dropdownMenus.forEach(function(menu) {
        menu.addEventListener('click', function(event) {
            // Only stop propagation if we're clicking the menu itself, not its children
            if (event.target === menu) {
                event.stopPropagation();
            }
        });
    });
});