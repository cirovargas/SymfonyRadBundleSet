import jQuery from 'jquery';
import 'px-libs/jquery.knob';

// Extensions / Knob
// --------------------------------------------------

($ => {
  'use strict';

  if (!$.fn.knob) {
    throw new Error('jquery.knob.js required.');
  }

  const fnKnob = $.fn.knob;

  $.fn.knob = function(options) {
    const knobs  = fnKnob.call(this, options);
    const _isRtl = $('html').attr('dir') === 'rtl';

    if (!_isRtl) { return knobs; }

    return knobs.each(function() {
      const $input = $(this).find('input');

      $input.css({
        'margin-left':  0,
        'margin-right': $input.css('margin-left'),
      });
    });
  };
})(jQuery);
