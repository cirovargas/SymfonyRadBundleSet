import jQuery from 'jquery';
import 'px-libs/bootstrap-datepicker';

// Extensions / Bootstrap-datepicker
// --------------------------------------------------

($ => {
  'use strict';

  if (!$.fn.datepicker) {
    throw new Error('bootstrap-datepicker.js required.');
  }

  const datepickerPlace = $.fn.datepicker.Constructor.prototype.place;

  $.fn.datepicker.Constructor.prototype.place = function() {
    datepickerPlace.call(this);

    if (!this.o.rtl) { return this; }

    const $container = $(this.o.container);
    let right = parseInt(this.picker.css('right'), 10);

    right += $container.outerWidth() - $container.width();

    if (!this.picker.hasClass('datepicker-orient-left')) {
      const cW = this.picker.outerWidth();
      const w  = this.component ? this.component.outerWidth(true) : this.element.outerWidth(false);

      right += (2 * w) - (2 * cW);
    }

    this.picker.css({ right });
    return this;
  };

  $.fn.datepicker.defaults.rtl = $('html').attr('dir') === 'rtl';
})(jQuery);
