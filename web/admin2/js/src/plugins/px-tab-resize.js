import jQuery from 'jquery';
import pxUtil from 'px/util';
import 'px-bootstrap/tab';

// Plugins / PxTabResize
// --------------------------------------------------

const PxTabResize = (function($) {
  'use strict';

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  const NAME               = 'pxTabResize';
  const DATA_KEY           = 'px.tab-resize';
  const EVENT_KEY          = `.${DATA_KEY}`;
  const JQUERY_NO_CONFLICT = $.fn[NAME];

  const Default = {
    template: `
<li class="dropdown">
  <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"></a>
  <ul class="dropdown-menu"></ul>
</li>`,
    content: '<span class="tab-resize-icon"></span>',
  };

  const ClassName = {
    TAB_RESIZE:     'tab-resize',
    TAB_RESIZE_NAV: 'tab-resize-nav',
    SHOW:           'show',
    ACTIVE:         'active',
  };

  const Selector = {
    NAV_ITEMS:       '> li:not(.tab-resize)',
    NAV_LINK:        '> a',
    DROPDOWN_TOGGLE: '> .dropdown-toggle',
    DROPDOWN_MENU:   '> .dropdown-menu',
    DROPDOWN_ITEMS:  '> li',
  };

  const Event = {
    RESIZE: `resize${EVENT_KEY}`,
    SHOWN:  `shown.bs.tab${EVENT_KEY}`,
  };


  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  class TabResize {
    constructor(element, config) {
      this.uniqueId = pxUtil.generateUniqueId();
      this.config   = this._getConfig(config);

      this.element = element;
      pxUtil.addClass(element, ClassName.TAB_RESIZE_NAV);

      this.navItem  = this._createNavItemElement();
      this.navLink  = this._getNavLinkElement();
      this.dropdown = this._getDropdownElement();

      this._setListeners();

      this.placeTabs();
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

    placeTabs() {
      this._resetDropdown();

      const $navItems = $(this.element).find(Selector.NAV_ITEMS);
      let curIndex    = $navItems.length - 1;
      let curNavItem  = $navItems[curIndex];
      const offsetTop = curNavItem ? $navItems[0].offsetTop : 0;

      if (!curNavItem || curNavItem.offsetTop <= offsetTop) {
        pxUtil.removeClass(this.navItem, ClassName.SHOW);
        return;
      }

      // Show dropdown menu
      pxUtil.addClass(this.navItem, ClassName.SHOW);

      while (curNavItem) {
        if (curNavItem.offsetTop <= offsetTop) { break; }

        this._moveItemToDropdown(curNavItem);

        curNavItem = $navItems[--curIndex];
      }
    }

    destroy() {
      this._unsetListeners();
      this._resetDropdown();
      this.navItem.remove();
      pxUtil.removeClass(this.element, ClassName.TAB_RESIZE_NAV);
      $(this.element).removeData(DATA_KEY);
    }

    // private

    _createNavItemElement() {
      const navItem = $(this.config.template).addClass(ClassName.TAB_RESIZE)[0];

      this.element
        .insertBefore(navItem, this.element.firstChild);

      return navItem;
    }

    _getNavLinkElement() {
      return $(this.navItem)
        .find(Selector.DROPDOWN_TOGGLE)
        .html(this.config.content)[0];
    }

    _getDropdownElement() {
      return $(this.navItem)
        .find(Selector.DROPDOWN_MENU)[0];
    }

    _moveItemToDropdown(_navItem) {
      $(this.dropdown).prepend(_navItem);

      // Check if nav item is active
      if (pxUtil.hasClass(_navItem, ClassName.ACTIVE)) {
        pxUtil.addClass(this.navItem, ClassName.ACTIVE);
        this.navLink.innerHTML = $(_navItem).find(Selector.NAV_LINK)[0].innerHTML;
      }
    }

    _resetDropdown() {
      pxUtil.removeClass(this.navItem, ClassName.ACTIVE);
      this.navLink.innerHTML = this.config.content;

      $(this.element)
        .append($(this.dropdown).find(Selector.DROPDOWN_ITEMS));
    }

    _setListeners() {
      $(window).on(
        `${this.constructor.Event.RESIZE}.${this.uniqueId}`,
        $.proxy(this.placeTabs, this)
      );

      $(this.element).on(
        this.constructor.Event.SHOWN,
        $.proxy(this.placeTabs, this)
      );
    }

    _unsetListeners() {
      $(window).off(`${this.constructor.Event.RESIZE}.${this.uniqueId}`);
      $(this.element).off(EVENT_KEY);
    }

    _getConfig(config) {
      return $.extend({},
        this.constructor.Default,
        config
      );
    }

    // static

    static _jQueryInterface(config) {
      return this.each(function() {
        let data    = $(this).data(DATA_KEY);
        let _config = typeof config === 'object' ? config : null;

        if (!data) {
          data = new TabResize(this, _config);
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

  $.fn[NAME]             = TabResize._jQueryInterface;
  $.fn[NAME].Constructor = TabResize;
  $.fn[NAME].noConflict  = function() {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return TabResize._jQueryInterface;
  };

  return TabResize;
})(jQuery);

export default PxTabResize;
