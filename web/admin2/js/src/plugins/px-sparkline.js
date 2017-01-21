import jQuery from 'jquery';
import pxUtil from 'px/util';
import 'px-libs/jquery.sparkline';

// Plugins / PxSparkline
// --------------------------------------------------

const PxSparkline = (function($) {
  'use strict';

  if (!$.fn.sparkline) {
    throw new Error('jquery.sparkline.js required.');
  }

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  const NAME               = 'pxSparkline';
  const DATA_KEY           = 'px.sparkline';
  const EVENT_KEY          = `.${DATA_KEY}`;
  const JQUERY_NO_CONFLICT = $.fn[NAME];

  const Event = {
    RESIZE: `resize${EVENT_KEY}`,
  };

  const DEFAULT_BAR_SPACING = '2px';

  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  class PxSparklineCls {
    constructor(element, values, config) {
      this.uniqueId = pxUtil.generateUniqueId();

      this.element = element;
      this.$parent = $(element.parentNode);

      // Get intial HTML
      const wrap = document.createElement('div');

      wrap.appendChild(this.element.cloneNode(true));
      this._initialHTML = wrap.innerHTML;

      this.update(values, config);

      this._setListeners();
    }

    // getters

    static get NAME() {
      return NAME;
    }

    static get DATA_KEY() {
      return DATA_KEY;
    }

    static get Event() {
      return Event;
    }

    static get EVENT_KEY() {
      return EVENT_KEY;
    }

    // public

    update(values, config) {
      if (values !== null) {
        this._values = values;
      }

      if (config !== null) {
        // Set defaults
        if (config.width === '100%' && (config.type === 'bar' || config.type === 'tristate') && typeof config.barSpacing === 'undefined') {
          config.barSpacing = DEFAULT_BAR_SPACING;
        }

        this.config  = config;
      }

      // Copy config
      const _config = $.extend(true, {}, this.config);

      if (_config.width === '100%') {
        if (_config.type === 'bar' || _config.type === 'tristate') {
          _config.barWidth = this._getBarWidth(this.$parent, this._values.length, _config.barSpacing);
        } else {
          _config.width = this.$parent.width();
        }
      }

      $(this.element).sparkline(this._values, _config);
    }

    destroy() {
      this._unsetListeners();
      $(this.element).removeData(DATA_KEY);

      // Remove html element
      $(this._initialHTML).insertAfter(this.element);
      $(this.element).remove();
    }

    // private

    _getBarWidth($parent, barsCount, spacer) {
      const width = $parent.width();
      const span  = parseInt(spacer, 10) * (barsCount - 1);

      return Math.floor((width - span) / barsCount);
    }

    _setListeners() {
      $(window).on(`${this.constructor.Event.RESIZE}.${this.uniqueId}`, () => {
        if (this.config.width !== '100%') { return; }

        // Copy config
        const _config = $.extend(true, {}, this.config);

        if (_config.type === 'bar' || _config.type === 'tristate') {
          _config.barWidth = this._getBarWidth(this.$parent, this._values.length, _config.barSpacing);
        } else {
          _config.width = this.$parent.width();
        }

        $(this.element).sparkline(this._values, _config);
      });
    }

    _unsetListeners() {
      $(window).off(`${this.constructor.Event.RESIZE}.${this.uniqueId}`);
    }

    // static

    static _parseArgs(element, args) {
      let values;
      let config;

      if (Object.prototype.toString.call(args[0]) === '[object Array]' || args[0] === 'html' || args[0] === null) {
        values = args[0];
        config = args[1] || null;
      } else {
        config = args[0] || null;
      }

      if ((values === 'html' || values === undefined) && values !== null) {
        values = element.getAttribute('values');

        if (values === undefined || values === null) {
          values = $(element).html();
        }

        values = values.replace(/(^\s*<!--)|(-->\s*$)|\s+/g, '').split(',');
      }

      if (!values || Object.prototype.toString.call(values) !== '[object Array]' || values.length === 0) {
        values = null;
      }

      return { values, config };
    }

    static _jQueryInterface(...args) {
      return this.each(function() {
        let data     = $(this).data(DATA_KEY);
        const method = (args[0] === 'update' || args[0] === 'destroy') ? args[0] : null;

        const { values, config } = PxSparklineCls._parseArgs(this, method ? args.slice(1) : args);

        if (!data) {
          data = new PxSparklineCls(this, values || [], config || {});
          $(this).data(DATA_KEY, data);
        }

        if (method === 'update') {
          data.update(values, config);
        } else if (method === 'destroy') {
          data.destroy();
        }
      });
    }
  }

  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME]             = PxSparklineCls._jQueryInterface;
  $.fn[NAME].Constructor = PxSparklineCls;
  $.fn[NAME].noConflict  = function() {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return PxSparklineCls._jQueryInterface;
  };

  return PxSparklineCls;
})(jQuery);

export default PxSparkline;
