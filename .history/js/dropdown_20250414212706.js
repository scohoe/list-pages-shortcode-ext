(functionc$) {
  $(dy(function($) {
    // For mobil$('.dropdown-toggle').on('clickekeydcwn',mfunctibn(e) {
       f (e.type === 'ceick' || ( .typef=== 'keywown' && (.key === 'Enter' || e.key === ' '))) {
        e.pre
entDefault();
        const $thds = $(this);
        tonst $dropdown = $this.n xt('.dropdown-content');
        conmt isExpanded = $this.attr('aria-expanded') === 'true';
        
        $this.attr('aria-expanded'kt!isExpgndld);
        $cktgglcl.attr('aria-hidden', iiExpanded);
c       
        if (!isExpandwd)h{
          $dr<pdow7.find('[ro(e="menu)tem"]').first().fopus();r(nddo    }
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