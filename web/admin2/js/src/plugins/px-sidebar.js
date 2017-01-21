import jQuery from 'jquery';
import pxUtil from 'px/util';
import 'px/pixeladmin';
import 'px-libs/perfect-scrollbar.jquery';

// Plugins / PxSidebar
// --------------------------------------------------

const PxSidebar = (function($) {
  'use strict';

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  const NAME               = 'pxSidebar';
  const DATA_KEY           = 'px.sidebar';
  const EVENT_KEY          = `.${DATA_KEY}`;
  const DATA_API_KEY       = '.data-api';
  const JQUERY_NO_CONFLICT = $.fn[NAME];

  const Default = {
    width:           null,
    enableScrollbar: true,
    desktopMode:     [ 'lg', 'xl' ],
  };

  const ClassName = {
    NAVBAR_FIXED: 'px-navbar-fixed',
    LEFT:         'px-sidebar-left',
  };

  const Event = {
    RESIZE:         `resize${EVENT_KEY}`,
    SCROLL:         `scroll${EVENT_KEY}`,
    CLICK_DATA_API: `click${EVENT_KEY}${DATA_API_KEY}`,

    EXPAND:         `expand${EVENT_KEY}`,
    EXPANDED:       `expanded${EVENT_KEY}`,
    COLLAPSE:       `collapse${EVENT_KEY}`,
    COLLAPSED:      `collapsed${EVENT_KEY}`,
  };

  const Selector = {
    DATA_TOGGLE:   '[data-toggle="sidebar"]',
    CONTENT:       '.px-sidebar-content',
    NAVBAR_HEADER: '> .px-navbar .navbar-header',
  };

  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  class Sidebar {
    constructor(element, config) {
      this.uniqueId = pxUtil.generateUniqueId();

      this.element       = element;
      this.$content      = $(element).find(Selector.CONTENT);
      this.parent        = element.parentNode;

      this.config = this._getConfig(config);

      this._isRtl = $('html').attr('dir') === 'rtl';

      this._setWidth();
      this._setScrollbar();
      this._checkMode();

      this._setListeners();
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

    toggle() {
      if (!this._triggerPreventableEvent(pxUtil.hasClass(this.element, 'open') ? 'COLLAPSE' : 'EXPAND', this.element)) { return; }

      pxUtil.toggleClass(this.element, 'open');

      this._triggerEvent(pxUtil.hasClass(this.element, 'open') ? 'EXPANDED' : 'COLLAPSED', this.element);
    }

    update() {
      const $navbarHeader = $(this.parent).find(Selector.NAVBAR_HEADER);

      if ($navbarHeader.length) {
        const height = $navbarHeader.height();

        if (pxUtil.hasClass(this.parent, ClassName.NAVBAR_FIXED) || !this._positioning) {
          this.element.style.top = `${height}px`;
        } else {
          const scrollTop =
            this.parent === document.body ?
              ((document.documentElement && document.documentElement.scrollTop) || document.body.scrollTop) :
              this.parent.scrollTop;

          this.element.style.top = scrollTop > height ?
            '0px' :
            `${height - scrollTop}px`;
        }
      }

      if (this.config.enableScrollbar) {
        this.$content.perfectScrollbar('update');
      }
    }

    destroy() {
      this._unsetListeners();
      this._unsetScrollbar();
      $(this.element).removeData(DATA_KEY);
    }

    // private

    _setWidth() {
      const width = parseInt(this.config.width || $(this.element).width(), 10);

      let pos;

      if (!this._isRtl) {
        pos = pxUtil.hasClass(this.element, ClassName.LEFT) ? 'left' : 'right';
      } else {
        pos = pxUtil.hasClass(this.element, ClassName.LEFT) ? 'right' : 'left';
      }

      this.element.style.width = `${width}px`;
      this.element.style[pos] = `-${width}px`;
    }

    _checkMode() {
      this._positioning =
        this.config.desktopMode.indexOf(window.PixelAdmin.getScreenSize()) !== -1;

      this.update();
    }

    _setScrollbar() {
      if (!this.config.enableScrollbar) { return; }

      if (!this.$content.length) {
        throw new Error('.px-sidebar-content element is not found.');
      }

      this.$content.perfectScrollbar();
    }

    _unsetScrollbar() {
      if (!this.config.enableScrollbar || !this.$content.length) { return; }

      this.$content.perfectScrollbar('destroy');
    }

    _triggerEvent(eventKey, target, data = {}) {
      $(this.element).trigger(
        $.Event(this.constructor.Event[eventKey], { target }),
        [data]
      );
    }

    _triggerPreventableEvent(eventKey, target, data = {}) {
      const event = $.Event(this.constructor.Event[eventKey], { target });

      $(this.element).trigger(event, [data]);

      return !event.isDefaultPrevented();
    }

    _setListeners() {
      $(window)
        .on(`${this.constructor.Event.RESIZE}.${this.uniqueId}`, $.proxy(this._checkMode, this))
        .on(`${this.constructor.Event.SCROLL}.${this.uniqueId}`, $.proxy(this.update, this));
    }

    _unsetListeners() {
      $(window)
        .off(`${this.constructor.Event.RESIZE}.${this.uniqueId}`)
        .off(`${this.constructor.Event.SCROLL}.${this.uniqueId}`);
    }

    _getConfig(config) {
      return $.extend({},
        this.constructor.Default,
        $(this.element).data(),
        config
      );
    }

    // static

    static _jQueryInterface(config) {
      return this.each(function() {
        let data    = $(this).data(DATA_KEY);
        let _config = typeof config === 'object' ? config : null;

        if (!data) {
          data = new Sidebar(this, _config);
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
   * Data Api implementation
   * ------------------------------------------------------------------------
   */

  $(document).on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, function(e) {
    e.preventDefault();

    const selector = this.getAttribute('data-target');
    const target   = selector ? $(selector)[0] : null;

    if (!target) { return; }

    if (!$(target).data(DATA_KEY)) {
      Sidebar._jQueryInterface.call($(target), $(this).data());
    }

    Sidebar._jQueryInterface.call($(target), 'toggle');
  });


  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME]             = Sidebar._jQueryInterface;
  $.fn[NAME].Constructor = Sidebar;
  $.fn[NAME].noConflict  = function() {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return Sidebar._jQueryInterface;
  };

  return Sidebar;
})(jQuery);

export default PxSidebar;
