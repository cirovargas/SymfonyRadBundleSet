import jQuery from 'jquery';
import 'px-bootstrap/tooltip';

// Extensions / Tooltip
// --------------------------------------------------

($ => {
  'use strict';

  if (!$.fn.tooltip) {
    throw new Error('tooltip.js required.');
  }

  const STATE_PARAM = 'data-state';

  const bsTooltipGetOptions = $.fn.tooltip.Constructor.prototype.getOptions;
  const bsTooltipSetContent = $.fn.tooltip.Constructor.prototype.setContent;

  $.fn.tooltip.Constructor.prototype.getOptions = function(options) {
    const result = bsTooltipGetOptions.call(this, options);
    const _isRtl = $('html').attr('dir') === 'rtl';

    if (_isRtl && result.placement === 'left') {
      result.placement = 'right';
    } else if (_isRtl && result.placement === 'right') {
      result.placement = 'left';
    }

    return result;
  };

  $.fn.tooltip.Constructor.prototype.setContent = function() {
    const state = this.$element.attr(STATE_PARAM);

    if (state) {
      $(this.tip()).addClass(`tooltip-${state.replace(/[^a-z0-9_-]/ig, '')}`);
    }

    bsTooltipSetContent.call(this);
  };
})(jQuery);
