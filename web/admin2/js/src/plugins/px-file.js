import jQuery from 'jquery';
import pxUtil from 'px/util';

// Plugins / PxFile
// --------------------------------------------------

const PxFile = (function($) {
  'use strict';

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  const NAME               = 'pxFile';
  const DATA_KEY           = 'px.file';
  const EVENT_KEY          = `.${DATA_KEY}`;
  const JQUERY_NO_CONFLICT = $.fn[NAME];

  const ClassName = {
    BROWSE:    'px-file-browse',
    CLEAR:     'px-file-clear',
    HAS_VALUE: 'px-file-has-value',
  };

  const Event = {
    CLICK:  `click${EVENT_KEY}`,
    CHANGE: `change${EVENT_KEY}`,
  };


  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  class FileCls {
    constructor(element) {
      this.element    = element;
      this.input      = $(element).find('.custom-file-input')[0];
      this.control    = $(element).find('.custom-file-control')[0];
      this.placeholder = this.control.innerHTML;

      this._checkElement();
      this._checkInput();
      this._checkControl();
      this._setListeners();

      this.update();
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

    browse() {
      $(this.input).trigger('click');
    }

    clear() {
      $(this.input)
        .wrap('<form>')
        .parent()
        .on('reset', function(e) { e.stopPropagation(); })
        .trigger('reset');

      $(this.input).unwrap();
      this.update();
    }

    update() {
      const value = (this.input.value || '')
        .replace(/\\/g, '/')
        .split('/')
        .pop();

      // Set control placeholder or value
      if (value) {
        $(this.control).text(value);
      } else {
        this.control.innerHTML = this.placeholder;
      }

      pxUtil[value ? 'addClass' : 'removeClass'](this.element, ClassName.HAS_VALUE);
    }

    destroy() {
      this._unsetListeners();

      $(this.element).removeData(DATA_KEY);
    }

    // private

    _checkElement() {
      if (!pxUtil.hasClass(this.element, 'custom-file')) {
        throw new Error(`${NAME} plugin must be called on a custom file input wrapper.`);
      }
    }

    _checkInput() {
      if (!this.input) {
        throw new Error('File input is not found.');
      }
    }

    _checkControl() {
      if (!this.control) {
        throw new Error('.custom-file-control element is not found.');
      }
    }

    _rejectEvent(e) {
      if (!e) { return; }
      e.stopPropagation();
      e.preventDefault();
    }

    _setListeners() {
      $(this.element)
        .find(`.${ClassName.BROWSE}`)
        .on(this.constructor.Event.CLICK, e => {
          this._rejectEvent(e);
          this.browse();
          $(this.input).trigger('focus');
        });

      $(this.element)
        .find(`.${ClassName.CLEAR}`)
        .on(this.constructor.Event.CLICK, e => {
          this._rejectEvent(e);
          this.clear();
          $(this.input).trigger('focus');
        });

      $(this.input)
        .on(this.constructor.Event.CHANGE, $.proxy(this.update, this));
    }

    _unsetListeners() {
      $(this.element).find(`.${ClassName.BROWSE}`).off(EVENT_KEY);
      $(this.element).find(`.${ClassName.CLEAR}`).off(EVENT_KEY);
      $(this.input).off(EVENT_KEY);
    }

    // static

    static _jQueryInterface(method) {
      return this.each(function() {
        let data = $(this).data(DATA_KEY);

        if (!data) {
          data = new FileCls(this);
          $(this).data(DATA_KEY, data);
        }

        if (typeof method === 'string') {
          if (!data[method]) {
            throw new Error(`No method named "${method}".`);
          }
          data[method]();
        }
      });
    }
  }


  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME]             = FileCls._jQueryInterface;
  $.fn[NAME].Constructor = FileCls;
  $.fn[NAME].noConflict  = function() {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return FileCls._jQueryInterface;
  };

  return FileCls;
})(jQuery);

export default PxFile;
