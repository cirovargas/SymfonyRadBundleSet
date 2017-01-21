import jQuery from 'jquery';

// Plugins / PxCharLimit
// --------------------------------------------------

const PxCharLimit = (function($) {
  'use strict';

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  const NAME               = 'pxCharLimit';
  const DATA_KEY           = 'px.charLimit';
  const EVENT_KEY          = `.${DATA_KEY}`;
  const JQUERY_NO_CONFLICT = $.fn[NAME];


  const Default = {
    maxlength: null,
    counter:   '',
  };

  const Event = {
    KEYUP: `keyup${EVENT_KEY}`,
    FOCUS: `focus${EVENT_KEY}`,
  };


  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  class CharLimit {
    constructor(element, config) {
      this.element    = element;
      this.isTextarea = $(element).is('textarea');
      this.config     = this._getConfig(config);
      this.counter      = this._getLabel();

      this._setMaxLength();
      this._setListeners();

      this.update();
    }


    // getters

    static get Default() {
      return Default;
    }

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

    update() {
      const maxlength = this.config.maxlength;
      let value       = this.element.value;
      let charCount;

      if (this.isTextarea) {
        value = value.replace(/\r?\n/g, '\n');
      }

      charCount = value.length;

      if (charCount > maxlength) {
        this.element.value = value.substr(0, maxlength);
        charCount = maxlength;
      }

      if (this.counter) {
        this.counter.innerHTML = maxlength - charCount;
      }
    }

    destroy() {
      this._unsetListeners();

      $(this.element).removeData(DATA_KEY);
    }


    // private

    _getLabel() {
      if (!this.config.counter) { return null; }

      return typeof this.config.counter === 'string' ?
        ($(this.config.counter)[0] || null) :
        this.config.counter;
    }

    _setMaxLength() {
      if (!this.isTextarea) {
        this.element.setAttribute('maxlength', this.config.maxlength);
      } else {
        this.element.removeAttribute('maxlength');
      }
    }

    _setListeners() {
      $(this.element)
        .on(this.constructor.Event.KEYUP, $.proxy(this.update, this))
        .on(this.constructor.Event.FOCUS, $.proxy(this.update, this));
    }

    _unsetListeners() {
      $(this.element).off(EVENT_KEY);
    }

    _getConfig(config) {
      const result = $.extend({},
        this.constructor.Default,
        { maxlength: this.element.getAttribute('maxlength') },
        $(this.element).data(),
        config
      );

      if (!result.maxlength) {
        throw new Error('maxlength is not specified.');
      }

      // Remove maxlength attribute if the element is a textarea
      if (this.isTextarea && this.element.getAttribute('maxlength')) {
        this.element.removeAttribute('maxlength');
      }

      return result;
    }


    // static

    static _jQueryInterface(config) {
      return this.each(function() {
        let data    = $(this).data(DATA_KEY);
        let _config = typeof config === 'object' ? config : null;

        if (!data) {
          data = new CharLimit(this, _config);
          $(this).data(DATA_KEY, data);
        }

        if (typeof config === 'string') {
          if (!data[config]) {
            throw new Error(`No method named "${config}"`);
          }
          data[config]();
        }
      });
    }

  }


  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME]             = CharLimit._jQueryInterface;
  $.fn[NAME].Constructor = CharLimit;
  $.fn[NAME].noConflict  = function() {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return CharLimit._jQueryInterface;
  };

  return CharLimit;
})(jQuery);

export default PxCharLimit;
