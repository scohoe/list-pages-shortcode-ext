(function($) {
  $(document).ready(function() {
    $('.dropdown-toggle').on('click keydown', function(e) {
      if (e.type === 'click' || (e.type === 'keydown' && (e.key === 'Enter' || e.key === ' '))) {
        e.preventDefault();
        const $this = $(this);
        const $dropdown = $this.next('.dropdown-content');
        const isExpanded = $this.attr('aria-expanded') === 'true';
        
        $this.attr('aria-expanded', !isExpanded);
        $dropdown.attr('aria-hidden', isExpanded);
        
        if (!isExpanded) {
          $dropdown.find('[role="menuitem"]').first().focus();
        }
      }
    });

    $('.dropdown-content').on('keydown', function(e) {
      const $items = $(this).find('[role="menuitem"]');
      const $current = $(document.activeElement);
      const currentIndex = $items.index($current);
      
      if (e.key === 'Escape') {
        $(this).attr('aria-hidden', 'true').prev('.dropdown-toggle').attr('aria-expanded', 'false').focus();
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
  });
})(jQuery);