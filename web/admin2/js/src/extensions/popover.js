import jQuery from 'jquery';
import 'px-bootstrap/popover';

// Extensions / Popover
// --------------------------------------------------

($ => {
  'use strict';

  if (!$.fn.popover) {
    throw new Error('popover.js required.');
  }

  const STATE_PARAM = 'data-state';
  const STYLE_PARAM = 'data-style';

  const bsPopoverGetOptions = $.fn.popover.Constructor.prototype.getOptions;
  const bsPopoverSetContent = $.fn.popover.Constructor.prototype.setContent;

  $.fn.popover.Constructor.prototype.getOptions = function(options) {
    const result = bsPopoverGetOptions.call(this, options);
    const _isRtl = $('html').attr('dir') === 'rtl';

    if (_isRtl && result.placement === 'left') {
      result.placement = 'right';
    } else if (_isRtl && result.placement === 'right') {
      result.placement = 'left';
    }

    return result;
  };

  $.fn.popover.Constructor.prototype.setContent = function() {
    var $element = this.$element;
    var $tip     = $(this.tip());
    var state    = $element.attr(STATE_PARAM);
    var style    = ($element.attr(STYLE_PARAM) || '').toLowerCase().split(' ');

    if (state) {
      $tip.addClass(`popover-${state.replace(/[^a-z0-9_-]/ig, '')}`);
    }

    if (style.indexOf('dark') !== -1) {
      $tip.addClass(`popover-dark`);
    }

    if (style.indexOf('colorful') !== -1) {
      $tip.addClass(`popover-colorful`);
    }

    bsPopoverSetContent.call(this);
  };
})(jQuery);
