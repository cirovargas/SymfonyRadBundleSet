import jQuery from 'jquery';
import 'px-libs/perfect-scrollbar.jquery';

// Extensions / Bootstrap-datepicker
// --------------------------------------------------

($ => {
  'use strict';

  if (!$.fn.perfectScrollbar) {
    throw new Error('perfect-scrollbar.jquery.js required.');
  }

  const _isRtl             = $('html').attr('dir') === 'rtl';
  const fnPerfectScrollbar = $.fn.perfectScrollbar;

  $.fn.perfectScrollbar = function(settingOrCommand) {
    return this.each(function() {
      let psId = $(this).attr('data-ps-id');

      fnPerfectScrollbar.call($(this), settingOrCommand);

      if (_isRtl && !psId) {
        psId = $(this).attr('data-ps-id');

        if (psId) {
          $(window).on(`resize.ps.${psId}`, () => $(this).perfectScrollbar('update'));
        }
      } else if (_isRtl && psId && settingOrCommand === 'destroy') {
        $(window).off(`resize.ps.${psId}`);
      }
    });
  };
})(jQuery);
