import jQuery from 'jquery';
import pxUtil from 'px/util';
import 'px/pixeladmin';

// Plugins / PxFooter
// --------------------------------------------------

const PxFooter = (function($) {
  'use strict';

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  const NAME               = 'pxFooter';
  const DATA_KEY           = 'px.footer';
  const EVENT_KEY          = `.${DATA_KEY}`;
  const JQUERY_NO_CONFLICT = $.fn[NAME];

  const ClassName = {
    CONTENT: 'px-content',
    BOTTOM:  'px-footer-bottom',
    FIXED:   'px-footer-fixed',
  };

  const Event = {
    RESIZE:          `resize${EVENT_KEY}`,
    SCROLL:          `scroll${EVENT_KEY}`,
    NAV_EXPANDED:    `expanded.px.nav`,
    NAV_COLLAPSED:   `collapsed.px.nav`,
    DROPDOWN_OPENED: `dropdown-opened.px.nav`,
    DROPDOWN_CLOSED: `dropdown-closed.px.nav`,
  };

  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  class Footer {
    constructor(element) {
      this.uniqueId = pxUtil.generateUniqueId();
      this.element  = element;
      this.parent   = element.parentNode;

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

    update() {
      if (this.parent === document.body) {
        this._curScreenSize = window.PixelAdmin.getScreenSize();
        this._updateBodyMinHeight();
      }

      const content = $(this.parent).find(`> .${ClassName.CONTENT}`)[0];

      if (!pxUtil.hasClass(this.element, ClassName.BOTTOM) && !pxUtil.hasClass(this.element, ClassName.FIXED)) {
        content.style.paddingBottom = content.setAttribute('style', (content.getAttribute('style') || '').replace(/\s*padding-bottom:\s*\d+px\s*;?/i));
        return;
      }

      $(this.parent).find(`> .${ClassName.CONTENT}`)[0].style.paddingBottom =
        `${$(this.element).outerHeight() + 20}px`;
    }

    destroy() {
      this._unsetListeners();

      $(this.element).removeData(DATA_KEY);
    }

    // private

    _updateBodyMinHeight() {
      if (document.body.style.minHeight) {
        document.body.style.minHeight = null;
      }

      if ((this._curScreenSize !== 'lg' && this._curScreenSize !== 'xl') || !pxUtil.hasClass(this.element, ClassName.BOTTOM) || $(document.body).height() >= document.body.scrollHeight) {
        return;
      }

      document.body.style.minHeight = `${document.body.scrollHeight}px`;
    }

    _setListeners() {
      $(window)
        .on(`${this.constructor.Event.RESIZE}.${this.uniqueId}`, $.proxy(this.update, this))
        .on(`${this.constructor.Event.SCROLL}.${this.uniqueId}`, $.proxy(this._updateBodyMinHeight, this))
        .on(`${this.constructor.Event.NAV_EXPANDED}.${this.uniqueId} ${this.constructor.Event.NAV_COLLAPSED}.${this.uniqueId}`, '.px-nav', $.proxy(this._updateBodyMinHeight, this));

      if (this.parent === document.body) {
        $('.px-nav')
          .on(`${this.constructor.Event.DROPDOWN_OPENED}.${this.uniqueId} ${this.constructor.Event.DROPDOWN_CLOSED}.${this.uniqueId}`, '.px-nav-dropdown', $.proxy(this._updateBodyMinHeight, this));
      }
    }

    _unsetListeners() {
      $(window)
        .off(`${this.constructor.Event.RESIZE}.${this.uniqueId} ${this.constructor.Event.SCROLL}.${this.uniqueId}`)
        .off(`${this.constructor.Event.NAV_EXPANDED}.${this.uniqueId} ${this.constructor.Event.NAV_COLLAPSED}.${this.uniqueId}`);

      $('.px-nav')
        .off(`${this.constructor.Event.DROPDOWN_OPENED}.${this.uniqueId} ${this.constructor.Event.DROPDOWN_CLOSED}.${this.uniqueId}`);
    }

    // static

    static _jQueryInterface(method) {
      return this.each(function() {
        let data = $(this).data(DATA_KEY);

        if (!data) {
          data = new Footer(this);
          $(this).data(DATA_KEY, data);
        }

        if (typeof method === 'string') {
          if (!data[method]) {
            throw new Error(`No method named "${method}"`);
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

  $.fn[NAME]             = Footer._jQueryInterface;
  $.fn[NAME].Constructor = Footer;
  $.fn[NAME].noConflict  = function() {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return Footer._jQueryInterface;
  };

  return Footer;
})(jQuery);

export default PxFooter;
