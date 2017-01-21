import jQuery from 'jquery';
import pxUtil from 'px/util';
import 'px-bootstrap/transition';
import 'px-bootstrap/collapse';
import 'px-bootstrap/dropdown';
import 'px-libs/perfect-scrollbar.jquery';

// Plugins / PxNavbar
// --------------------------------------------------

const PxNavbar = (function($) {
  'use strict';

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  const NAME               = 'pxNavbar';
  const DATA_KEY           = 'px.navbar';
  const EVENT_KEY          = `.${DATA_KEY}`;
  const DATA_API_KEY       = '.data-api';
  const JQUERY_NO_CONFLICT = $.fn[NAME];

  const ClassName = {
    NAVBAR:    'px-navbar',
    INNER:     'px-navbar-collapse-inner',
    IN:        'in',
    COLLAPSED: 'collapsed',
  };

  const Selector = {
    DATA_TOGGLE:     '.navbar-toggle[data-toggle="collapse"]',
    DROPDOWN_TOGGLE: '.dropdown-toggle[data-toggle="dropdown"]',
    COLLAPSE:        '.navbar-collapse',
    DROPDOWN:        '.dropdown',
  };

  const Event = {
    CLICK_DATA_API:  `click${EVENT_KEY}${DATA_API_KEY}`,
    RESIZE:          `resize${EVENT_KEY}`,
    CLICK:           `click${EVENT_KEY}`,
    MOUSEDOWN:       `mousedown${EVENT_KEY}`,
    COLLAPSE_SHOW:   `show.bs.collapse${EVENT_KEY}`,
    COLLAPSE_SHOWN:  `shown.bs.collapse${EVENT_KEY}`,
    COLLAPSE_HIDDEN: `hidden.bs.collapse${EVENT_KEY}`,
    DROPDOWN_SHOWN:  `shown.bs.dropdown${EVENT_KEY}`,
    DROPDOWN_HIDDEN: `hidden.bs.dropdown${EVENT_KEY}`,
  };

  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  class Navbar {
    constructor(element) {
      if (!$.fn.perfectScrollbar) {
        throw new Error('Scrolling feature requires the "perfect-scrollbar" plugin included.');
      }

      this.uniqueId = pxUtil.generateUniqueId();

      this.element   = element;
      this.$collapse = $(element).find(Selector.COLLAPSE);
      this.$toggle   = $(element).find(Selector.DATA_TOGGLE);

      this._scrollbarEnabled = 0;
      this._curScrollTop     = 0;

      if (!this.$collapse.length || !this.$toggle.length) { return; }

      this.$inner = this._setupInnerContainer();

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

    updateScrollbar() {
      if (!this._scrollbarEnabled) { return; }

      this._updateHeight();

      this.$inner
        .scrollTop(this._curScrollTop)
        .perfectScrollbar('update');
    }

    destroy() {
      this._unsetListeners();

      this._disableScrollbar();

      this.$collapse.append(this.$inner.find('> *'));

      this.$inner.remove();

      $(this.element).removeData(DATA_KEY);
    }

    // private

    _updateHeight() {
      const maxHeight = $(window).height() - this.$collapse[0].offsetTop;

      this.$collapse.height('');

      const curHeight = this.$collapse.height();

      if (curHeight > maxHeight) {
        this.$collapse.height(`${maxHeight}px`);
      }
    }

    _enableScrollbar() {
      if (this._scrollbarEnabled) { return; }

      this._updateHeight();
      this.$inner.perfectScrollbar({ suppressScrollX: true });

      this._scrollbarEnabled = 1;
    }

    _disableScrollbar() {
      if (!this._scrollbarEnabled) { return; }

      this.$collapse.height('');
      this.$inner.perfectScrollbar('destroy');

      this._scrollbarEnabled = 0;
    }

    _setupInnerContainer() {
      const $inner = $(`<div class="${ClassName.INNER}"></div>`);

      $inner.append(this.$collapse.find('> *'));

      this.$collapse.append($inner);

      return $inner;
    }

    _setListeners() {
      const self = this;

      $(window)
        .on(`${this.constructor.Event.RESIZE}.${this.uniqueId}`, () => {
          if (!this._scrollbarEnabled) { return; }

          // TODO: Remove dependency on toggle button
          if (this.$toggle.is(':visible')) {
            this._curScrollTop = this.$inner[0].scrollTop;
            this.updateScrollbar();
          } else {
            this._disableScrollbar();
            this.$collapse.removeClass(ClassName.IN);
            this.$toggle.addClass(ClassName.COLLAPSED);
            this.$collapse.attr('aria-expanded', 'false');
            this.$toggle.attr('aria-expanded', 'false');
          }
        });

      $(this.element)
        .on(this.constructor.Event.COLLAPSE_SHOW, Selector.COLLAPSE, () => {
          this.$collapse.find('.dropdown.open').removeClass('open');
        })
        .on(this.constructor.Event.COLLAPSE_SHOWN, Selector.COLLAPSE, () => {
          this._enableScrollbar();
        })
        .on(this.constructor.Event.COLLAPSE_HIDDEN, Selector.COLLAPSE, () => {
          this._disableScrollbar();
        })
        .on(`${this.constructor.Event.DROPDOWN_SHOWN} ${this.constructor.Event.DROPDOWN_HIDDEN}`, Selector.DROPDOWN, () => {
          this.updateScrollbar();
        })
        .on(this.constructor.Event.MOUSEDOWN, Selector.DROPDOWN_TOGGLE, () => {
          if (!this._scrollbarEnabled) { return true; }

          this._curScrollTop = this.$inner[0].scrollTop;
        })
        .on(this.constructor.Event.CLICK, Selector.DROPDOWN_TOGGLE, function(e) {
          if (!self._scrollbarEnabled) { return true; }
          if (this.getAttribute('href') === '#') { return true; }

          // Prevent dropdown open
          e.preventDefault();
          e.stopPropagation();

          // Simulate link click and prevent dropdown toggling
          this.removeAttribute('data-toggle');
          this.click();
          this.setAttribute('data-toggle', 'dropdown');
        });
    }

    _unsetListeners() {
      $(window).off(`${this.constructor.Event.RESIZE}.${this.uniqueId}`);
      $(this.element).off(EVENT_KEY);
    }

    // static

    static _jQueryInterface(method, ...args) {
      return this.each(function() {
        let data = $(this).data(DATA_KEY);

        if (!data) {
          data = new Navbar(this);
          $(this).data(DATA_KEY, data);

          if (!$.support.transition && $(this).find(Selector.DATA_TOGGLE).attr('aria-expanded') === 'true') {
            data._enableScrollbar();
          }
        }

        if (typeof method === 'string') {
          if (!data[method]) {
            throw new Error(`No method named "${method}"`);
          }
          data[method].apply(data, args);
        }
      });
    }
  }


  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */

  $(document).on(Event.CLICK_DATA_API, `.${ClassName.NAVBAR} ${Selector.DATA_TOGGLE}`, function(e) {
    e.preventDefault();

    const $target = $(this).parents(`.${ClassName.NAVBAR}`);

    if (!$target.length) { return; }

    if (!$target.data(DATA_KEY)) {
      Navbar._jQueryInterface.call($target);
    }
  });


  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME]             = Navbar._jQueryInterface;
  $.fn[NAME].Constructor = Navbar;
  $.fn[NAME].noConflict  = function() {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return Navbar._jQueryInterface;
  };

  return Navbar;
})(jQuery);

export default PxNavbar;
