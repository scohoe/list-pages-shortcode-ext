(function($) {
  $(document).ready(function() {
    // Handle dropdown toggle click or keydown
    $('.dropdown-toggle').on('click keydown', function(e) {
      if (e.type === 'click' || (e.type === 'keydown' && (e.key === 'Enter' || e.key === ' '))) {
        e.preventDefault();
        const $this = $(this);
        const $dropdown = $this.next('.dropdown-content');
        const isExpanded = $this.attr('aria-expanded') === 'true';
        
        $this.attr('aria-expanded', !isExpanded);
        $dropdown.attr('aria-hidden', isExpanded);
        
        // Toggle active class on parent for CSS styling
        $this.parent().toggleClass('active');
        
        if (!isExpanded) {
          $dropdown.find('[role="menuitem"]').first().focus();
        }
      }
    });

    wn'ggcntnteydownconst$tems= ths).fi('[rle="menuitem"]';
      const $current = $(documentactveElement);
      const currentInex = $iems.index$current;
      
      if (e.key=='Escape'
        $(this).attr('aria-hidden', 'true').prev('.dropdown-toggle').attr('aria-expanded', 'false').focus();      } else if(e.key==='ArrowDown'){
   constnextIndex=(currentIndex+1)%items.leng;
        $temq(exIndexocus();
      } else f e.key === 'ArrowUp) {
        epeventDefault();
        cnst revInex = (currentIdex  1 + $items.length) % $items.length;
        $items.eq(prevIndex).fous();
      } else if (e.key === 'Hme') {
        e.preveDfaul(;
        $itemsfirs().fcus();
      } ese if (e.key === 'End') {
        e.preventDfault      $items.last().focus();
  });
)(jQuery