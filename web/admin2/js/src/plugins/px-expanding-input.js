import jQuery from 'jquery';
import pxUtil from 'px/util';

// Plugins / PxExpandingInput
// --------------------------------------------------

const PxExpandingInput = (function($) {
  'use strict';

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  const NAME               = 'pxExpandingInput';
  const DATA_KEY           = 'px.expanding-input';
  const EVENT_KEY          = `.${DATA_KEY}`;
  const JQUERY_NO_CONFLICT = $.fn[NAME];

  const ClassName = {
    EXPANDED: 'expanded',
    CONTROL:  'expanding-input-control',
    OVERLAY:  'expanding-input-overlay',
    CONTENT:  'expanding-input-content',
  };

  const Event = {
    FOCUS:     `focus${EVENT_KEY}`,
    CLICK:     `click${EVENT_KEY}`,
    EXPAND:    `expand${EVENT_KEY}`,
    EXPANDED:  `expanded${EVENT_KEY}`,
    COLLAPSE:  `collapse${EVENT_KEY}`,
    COLLAPSED: `collapsed${EVENT_KEY}`,
  };


  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  class ExpandingInput {
    constructor(element) {
      this.element = element;
      this.control = $(element).find(`.${ClassName.CONTROL}`)[0];
      this.overlay = $(element).find(`.${ClassName.OVERLAY}`)[0];

      this._checkElements();
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

    expand() {
      if (pxUtil.hasClass(this.element, ClassName.EXPANDED)) { return; }

      let expandEvent = $.Event(this.constructor.Event.EXPAND, {
        target: this.element,
      });

      // Trigger before expand event
      $(this.element).trigger(expandEvent);

      if (expandEvent.isDefaultPrevented()) { return; }

      pxUtil.addClass(this.element, ClassName.EXPANDED);

      // Trigger after expand event
      $(this.element).trigger(
        $.Event(this.constructor.Event.EXPANDED, { target: this.element })
      );

      // Set focus on input
      $(this.control).trigger('focus');
    }

    collapse() {
      if (!pxUtil.hasClass(this.element, ClassName.EXPANDED)) { return; }

      let collapseEvent = $.Event(this.constructor.Event.COLLAPSE, {
        target: this.element,
      });

      // Trigger before collapse event
      $(this.element).trigger(collapseEvent);

      if (collapseEvent.isDefaultPrevented()) { return; }

      pxUtil.removeClass(this.element, ClassName.EXPANDED);

      // Trigger after collapse event
      $(this.element).trigger(
        $.Event(this.constructor.Event.COLLAPSED, { target: this.element })
      );
    }

    destroy() {
      this._unsetListeners();

      $(this.element).removeData(DATA_KEY);
    }

    // private

    _checkElements() {
      if (!pxUtil.hasClass(this.element, 'expanding-input')) {
        throw new Error(`${NAME} plugin must be called on an element with 'expanding-input' class.`);
      }

      if (!this.control) {
        throw new Error('Input is not found.');
      }

      if (!this.overlay) {
        this.overlay = $('<div class="expanding-input-overlay"></div>')
            .insertAfter(this.control)[0];
      }

      if (!$(this.element).find(`.${ClassName.CONTENT}`)[0]) {
        throw new Error('Content element is not found.');
      }
    }

    _setListeners() {
      $(this.control)
        .on(this.constructor.Event.FOCUS, $.proxy(this.expand, this));

      $(this.overlay)
        .on(this.constructor.Event.CLICK, $.proxy(this.expand, this));

      // Set listeners on cancel buttons
      $(this.element).find('[data-collapse="true"]')
        .on(this.constructor.Event.CLICK, $.proxy(this.collapse, this));
    }

    _unsetListeners() {
      $(this.control).off(EVENT_KEY);
      $(this.overlay).off(EVENT_KEY);
      $(this.element).find('[data-collapse="true"]').off(EVENT_KEY);
    }

    // static

    static _jQueryInterface() {
      return this.each(function(method) {
        let data = $(this).data(DATA_KEY);

        if (!data) {
          data = new ExpandingInput(this);
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

  $.fn[NAME]             = ExpandingInput._jQueryInterface;
  $.fn[NAME].Constructor = ExpandingInput;
  $.fn[NAME].noConflict  = function() {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return ExpandingInput._jQueryInterface;
  };

  return ExpandingInput;
})(jQuery);

export default PxExpandingInput;
