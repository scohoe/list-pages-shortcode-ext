(function($) {
  $(document).ready(function() {
    // Handle mouse hover
    $('.page_item_has_children').hover(function() {
      const $dropdownContent = $(this).find('> .children');
      $(this).addClass('active');
      $(this).find('> a').attr('aria-expanded', 'true');
      $dropdownContent.attr('aria-hidden', 'false');
    }, function() {
      const $dropdownContent = $(this).find('> .children');
      $(this).removeClass('active');
      $(this).find('> a').attr('aria-expanded', 'false');
      $dropdownContent.attr('aria-hidden', 'true');
    });

    // Handle click for touch devices
    $('.page_item_has_children > a').on('click', function(e) {
      if (window.matchMedia('(hover: none)').matches) {
        e.preventDefault();
        const $parent = $(this).parent();
        const $dropdownContent = $parent.find('> .children');
        const isExpanded = $parent.hasClass('active');

        $('.page_item_has_children').not($parent).removeClass('active')
          .find('> a').attr('aria-expanded', 'false')
          .end().find('> .children').attr('aria-hidden', 'true');

        $parent.toggleClass('active');
        $(this).attr('aria-expanded', !isExpanded);
        $dropdownContent.attr('aria-hidden', isExpanded);
      }
    });

    // Handle keyboard navigation
    $('.children').on('keydown', function(e) {
      const $items = $(this).find('a');
      const $current = $(document.activeElement);
      const currentIndex = $items.index($current);
      
      if (e.key === 'Escape') {
        const $parentLink = $(this).parent().find('> a');
        $(this).parent().removeClass('active');
        $parentLink.attr('aria-expanded', 'false');
        $(this).attr('aria-hidden', 'true');
        $parentLink.focus();
      } else if (e.key === 'ArrowDown') {
        e.preventDefault();
        const nextIndex = (currentIndex + 1) % $items.length;
        $items.eq(nextIndex).focus();
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        const prevIndex = (currentIndex - 1 + $items.length) % $items.length;
        $items.eq(prevIndex).focus();
      } else if (e.key === 'Home') {
        e.preventDefault();
        $items.first().focus();
      } else if (e.key === 'End') {
        e.preventDefault();
        $items.last().focus();
      }
    });

    // Initialize ARIA attributes
    $('.page_item_has_children > a').attr('aria-expanded', 'false');
    $('.children').attr('aria-hidden', 'true');
  });
})(jQuery);