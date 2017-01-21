import jQuery from 'jquery';
import 'px-libs/bootstrap-tagsinput';

// Extensions / Bootstrap-tagsinput
// --------------------------------------------------

($ => {
  'use strict';

  if (!$.fn.tagsinput) {
    throw new Error('bootstrap-tagsinput.js required.');
  }

  const tagsinputBuild   = $.fn.tagsinput.Constructor.prototype.build;
  const tagsinputDestroy = $.fn.tagsinput.Constructor.prototype.destroy;

  $.fn.tagsinput.Constructor.prototype.build = function(options) {
    const self   = this;
    const result = tagsinputBuild.call(this, options);
    const re     = /<|>/g;

    this.$inpWidth = $('<div class="bootstrap-tagsinput-input" style="position:absolute;z-index:-101;top:-9999px;opacity:0;white-space:nowrap;"></div>');
    $('<div style="position:absolute;width:0;height:0;z-index:-100;opacity:0;overflow:hidden;"></div>').append(this.$inpWidth).prependTo(this.$container);

    function getWidth(val) {
      return Math.ceil(self.$inpWidth.html((val || '').replace(re, '#')).outerWidth() + 12) + 'px';
    }

    this.$input[0].style.width = getWidth();
    this.$input.on('keydown keyup focusout', function() {
      this.style.width = getWidth(this.value);
    });
    this.$input.on('paste', function() {
      setTimeout($.proxy(function() { this.style.width = getWidth(this.value); }, this), 100);
    });

    return result;
  };

  $.fn.tagsinput.Constructor.prototype.destroy = function() {
    this.$input.off('keydown keyup focusout paste');

    return tagsinputDestroy.call(this);
  };

  // Re-initialize [data-role=tagsinput]
  $(function() {
    $('input[data-role=tagsinput], select[multiple][data-role=tagsinput]').tagsinput('destroy');
    $('input[data-role=tagsinput], select[multiple][data-role=tagsinput]').tagsinput();
  });
})(jQuery);
