import jQuery from 'jquery';
import pxUtil from 'px/util';
import 'px/pixeladmin';
import 'px-bootstrap/transition';
import 'px-libs/perfect-scrollbar.jquery';

// Plugins / PxNav
// --------------------------------------------------

const PxNav = (function($) {
  'use strict';

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  const NAME               = 'pxNav';
  const DATA_KEY           = 'px.nav';
  const EVENT_KEY          = `.${DATA_KEY}`;
  const DATA_API_KEY       = '.data-api';
  const JQUERY_NO_CONFLICT = $.fn[NAME];

  const Default = {
    accordion:          true,
    transitionDuration: 300,
    dropdownCloseDelay: 400,
    enableTooltips:     true,
    animate:            true,
    storeState:         true,
    storagePrefix:      'px-nav.',

    modes: {
      phone:   ['xs'],
      tablet:  [ 'sm', 'md' ],
      desktop: [ 'lg', 'xl' ],
    },
  };

  const ClassName = {
    NAV:               'px-nav',
    NAV_LEFT:          'px-nav-left',
    CONTENT:           'px-nav-content',
    EXPAND:            'px-nav-expand',
    STATIC:            'px-nav-static',
    COLLAPSE:          'px-nav-collapse',
    ANIMATE:           'px-nav-animate',
    NAV_TRANSITIONING: 'px-nav-transitioning',
    DIMMER:            'px-nav-dimmer',
    FIXED:             'px-nav-fixed',
    OFF_CANVAS:        'px-nav-off-canvas',
    SCROLLABLE_AREA:   'px-nav-scrollable-area',

    ITEM:                  'px-nav-item',
    TOOLTIP:               'px-nav-tooltip',
    DROPDOWN:              'px-nav-dropdown',
    DROPDOWN_MENU:         'px-nav-dropdown-menu',
    DROPDOWN_MENU_TITLE:   'px-nav-dropdown-menu-title',
    DROPDOWN_MENU_SHOW:    'px-nav-dropdown-menu-show',
    DROPDOWN_MENU_WRAPPER: 'px-nav-dropdown-menu-wrapper',
    DROPDOWN_MENU_TOP:     'px-nav-dropdown-menu-top',

    OPEN:          'px-open',
    SHOW:          'px-show',
    FREEZE:        'freeze',
    ACTIVE:        'active',
    TRANSITIONING: 'transitioning',

    PERFECT_SCROLLBAR_CONTAINER: 'ps-container',
    NAVBAR_FIXED:                'px-navbar-fixed',
  };

  const Selector = {
    DATA_TOGGLE:         '[data-toggle="px-nav"]',
    CONTENT:             '.px-nav-content',
    ITEM:                '> .px-nav-item',
    ITEM_LABEL:          '> a > .px-nav-label',
    ROOT_LINK:           '> .px-nav-item:not(.px-nav-dropdown) > a',
    DROPDOWN_LINK:       '.px-nav-dropdown > a',
    DROPDOWN_MENU:       '> .px-nav-dropdown-menu',
    DROPDOWN_MENU_TITLE: '> .px-nav-dropdown-menu-title',
    OPENED_DROPDOWNS:    '> .px-nav-dropdown.px-open',
    SHOWN_DROPDOWNS:     '> .px-nav-dropdown.px-show',
    FROZEN_DROPDOWNS:    '.px-nav-dropdown.freeze',
    SCROLLABLE_AREA:     '.px-nav-scrollable-area',
    NEAR_NAVBAR:         '~ .px-navbar',
  };

  const Event = {
    CLICK_DATA_API: `click${EVENT_KEY}${DATA_API_KEY}`,
    RESIZE:         `resize${EVENT_KEY}`,
    CLICK:          `click${EVENT_KEY}`,
    MOUSEENTER:     `mouseenter${EVENT_KEY}`,
    MOUSELEAVE:     `mouseleave${EVENT_KEY}`,
    SCROLL:         `scroll${EVENT_KEY}`,

    INITIALIZED:       `initialized`,
    EXPAND:            `expand${EVENT_KEY}`,
    EXPANDED:          `expanded${EVENT_KEY}`,
    COLLAPSE:          `collapse${EVENT_KEY}`,
    COLLAPSED:         `collapsed${EVENT_KEY}`,
    DESTROY:           `destroy${EVENT_KEY}`,
    DROPDOWN_OPEN:     `dropdown-open${EVENT_KEY}`,
    DROPDOWN_OPENED:   `dropdown-opened${EVENT_KEY}`,
    DROPDOWN_CLOSE:    `dropdown-close${EVENT_KEY}`,
    DROPDOWN_CLOSED:   `dropdown-closed${EVENT_KEY}`,
    DROPDOWN_FROZEN:   `dropdown-frozen${EVENT_KEY}`,
    DROPDOWN_UNFROZEN: `dropdown-unfrozen${EVENT_KEY}`,
  };

  const PERFECT_SCROLLBAR_OPTIONS = {
    suppressScrollX:  true,
    wheelPropagation: false,
    swipePropagation: false,
  };


  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  class Nav {
    constructor(element, config) {
      this.uniqueId = pxUtil.generateUniqueId();

      this.element = element;
      this.content = $(element).find(Selector.CONTENT)[0];
      this.config  = this._getConfig(config);

      // Internal variables
      this._curMode       = this._getMode();
      this._isCollapsed   = this._getNavState();
      this._stateChanging = 0;

      this._setupMarkup();

      this.dimmer = $(element).parent().find(`> .${ClassName.DIMMER}`)[0];

      this._setListeners();

      this._restoreNavState();

      this._detectActiveItem();

      this._enableAnimation();

      this._checkNavbarPosition();

      this._triggerEvent('INITIALIZED', element);
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
      this[
        this._curMode !== 'desktop' && pxUtil.hasClass(this.element, ClassName.EXPAND) ||
        this._curMode === 'desktop' && !pxUtil.hasClass(this.element, ClassName.COLLAPSE) ?
          'collapse' :
          'expand'
      ]();
    }

    expand() {
      if (this._curMode !== 'phone' && !this.isCollapsed()) { return; }
      if (this._curMode === 'phone' && pxUtil.hasClass(this.element, ClassName.EXPAND)) { return; }
      if (!this._triggerPreventableEvent('EXPAND', this.element)) { return; }

      if (this._curMode !== 'phone') {
        this.closeAllDropdowns();
      }

      if (this.config.enableTooltips) {
        this._clearTooltips();
      }

      this._changeNavState(function() {
        if (this._curMode !== 'desktop') {
          const self = this;

          // Collapse other navs
          $(this.element)
            .parent()
            .find(`> .${ClassName.EXPAND}`)
            .each(function() {
              if (this === self.element) { return; }

              $(this)[NAME]('collapse');
            });

          $(this.dimmer).on(this.constructor.Event.CLICK, () => this.collapse());
          pxUtil.addClass(this.element, ClassName.EXPAND);
        } else {
          pxUtil.removeClass(this.element, ClassName.COLLAPSE);
        }

        this._triggerEvent('EXPANDED', this.element);
      });
    }

    collapse() {
      if (this.isCollapsed()) { return; }
      if (!this._triggerPreventableEvent('COLLAPSE', this.element)) { return; }

      this._changeNavState(function() {
        if (this._curMode !== 'desktop') {
          $(this.dimmer).off('click');
          pxUtil.removeClass(this.element, ClassName.EXPAND);
        } else {
          pxUtil.addClass(this.element, ClassName.COLLAPSE);
        }

        $(window).trigger('scroll');
        this._triggerEvent('COLLAPSED', this.element);
      });
    }

    isFixed() {
      return pxUtil.hasClass(this.element, ClassName.FIXED);
    }

    isStatic() {
      return pxUtil.hasClass(this.element, ClassName.STATIC);
    }

    isCollapsed() {
      return this._isCollapsed;
    }

    activateItem(_el) {
      const el = this._getNode(_el, ClassName.ITEM);

      if (pxUtil.hasClass(el, ClassName.DROPDOWN)) { return; }

      // Deactivate all items
      $(this.element)
        .find(`.${ClassName.ITEM}.${ClassName.ACTIVE}`)
        .removeClass(ClassName.ACTIVE);

      pxUtil.addClass(el, ClassName.ACTIVE);

      // If item is in the root
      if (pxUtil.hasClass(el.parentNode, ClassName.CONTENT)) {
        return;

      // Else if item is in the floating opened dropdown
      } else if (pxUtil.hasClass(el.parentNode, ClassName.DROPDOWN_MENU_WRAPPER)) {
        const targetDropdown = $(el).parents(`.${ClassName.DROPDOWN_MENU}`).data('dropdown');

        if (!targetDropdown) { return; }

        targetDropdown.addClass(ClassName.ACTIVE);

      // Else
      } else {
        let curDropdown = $(el).parents(`.${ClassName.DROPDOWN}`)[0];
        let rootDropdown;

        this.openDropdown(curDropdown, false);

        while (curDropdown) {
          pxUtil.addClass(curDropdown, ClassName.ACTIVE);

          if (pxUtil.hasClass(curDropdown.parentNode, ClassName.DROPDOWN_MENU_WRAPPER)) {
            rootDropdown = $(curDropdown).parents(`.${ClassName.DROPDOWN_MENU}`).data('dropdown');
            curDropdown  = null;

            if (!rootDropdown) { return; }

            pxUtil.addClass(rootDropdown, ClassName.ACTIVE);
          } else {
            rootDropdown = curDropdown;
            curDropdown  = $(curDropdown).parents(`.${ClassName.DROPDOWN}`)[0];
          }
        }

        if (this.isCollapsed()) {
          $(this.content).find(Selector.OPENED_DROPDOWNS).removeClass(ClassName.OPEN);
          pxUtil.addClass(rootDropdown, ClassName.OPEN);
        }
      }
    }

    openDropdown(_el, showDropdown = true) {
      const el = this._getNode(_el);

      if (this.isStatic() && !this._isFloatingDropdown(el)) { return; }
      if ((!this._isFloatingDropdown(el) || showDropdown) && !this.isDropdownOpened(el) && !this._triggerPreventableEvent('DROPDOWN_OPEN', el)) {
        return;
      }

      // Open dropdown tree
      //

      const dropdowns = this.isDropdownOpened(el) ? [] : [el];
      let dropdown = el;

      // Collect unopened parent dropdowns
      while ((dropdown = $(dropdown).parents(`.${ClassName.DROPDOWN}`)[0])) {
        if (!this.isDropdownOpened(dropdown)) {
          dropdowns.push(dropdown);
        }
      }

      const parent = dropdowns.pop();

      if (!parent) {
        return;
      }

      // Expand child dropdowns without animation
      for (let i = 0, l = dropdowns.length; i < l; i++) {
        this._expandDropdown(dropdowns[i], false);
      }

      if (this._isFloatingDropdown(parent)) {
        if (!showDropdown) { return; }

        this._showDropdown(parent);
      } else {
        this._expandDropdown(parent, true);
      }
    }

    closeDropdown(_el) {
      const el = this._getNode(_el);

      if (!this.isDropdownOpened(el)) { return; }
      if (this.isStatic() && !this._isFloatingDropdown(el)) { return; }
      if (!this._triggerPreventableEvent('DROPDOWN_CLOSE', el)) { return; }

      if (this._isFloatingDropdown(el)) {
        this._hideDropdown(el);
      } else {
        this._collapseDropdown(el, true);
      }
    }

    toggleDropdown(_el) {
      const el = this._getNode(_el);

      this[
        this.isDropdownOpened(el) ?
          'closeDropdown' :
          'openDropdown'
      ](el);
    }

    closeAllDropdowns(_parent = $(this.element).find(`.${ClassName.CONTENT}`)) {
      this._closeAllDropdowns(this._getNode(_parent, null));
    }

    freezeDropdown(_el) {
      const el = this._getNode(_el);

      if (!this._isFloatingDropdown(el) || !this.isDropdownOpened(el)) { return; }
      if (pxUtil.hasClass(el, ClassName.FREEZE)) { return; }

      pxUtil.addClass(el, ClassName.FREEZE);

      this._clearDropdownTimer(el);

      this._triggerEvent('DROPDOWN_FROZEN', el);
    }

    unfreezeDropdown(_el) {
      const el = this._getNode(_el);

      if (!this._isFloatingDropdown(el) || !this.isDropdownOpened(el)) { return; }
      if (!pxUtil.hasClass(el, ClassName.FREEZE)) { return; }

      pxUtil.removeClass(el, ClassName.FREEZE);

      this._triggerEvent('DROPDOWN_UNFROZEN', el);
    }

    getDropdownContainer(_el) {
      const el = this._getNode(_el);

      return (this._isFloatingDropdown(el) && this.isDropdownOpened(el)) ?
        $($(el).data('dropdown')).find(`.${ClassName.DROPDOWN_MENU_WRAPPER}`) :
        $(el).find(Selector.DROPDOWN_MENU);
    }

    isFloatingDropdown(el) {
      return this._isFloatingDropdown(this._getNode(el));
    }

    isDropdownOpened(_el) {
      const el          = this._getNode(_el);
      const isRoot      = this._isRootDropdown(el);
      const isCollapsed = this.isCollapsed();

      return (isCollapsed && isRoot && pxUtil.hasClass(el, ClassName.SHOW)) ||
             (isCollapsed && !isRoot && pxUtil.hasClass(el, ClassName.OPEN)) ||
             (!isCollapsed && pxUtil.hasClass(el, ClassName.OPEN));
    }

    isDropdownFrozen(_el) {
      return pxUtil.hasClass(this._getNode(_el), ClassName.FREEZE);
    }

    append(item, parentDropdown = null) {
      return this.insert(item, null, parentDropdown);
    }

    prepend(item, parentDropdown = null) {
      return this.insert(item, 0, parentDropdown);
    }

    insert(_item, position, _parent = null) {
      // Get item

      const $items = this._getNodeOrCreate(_item, ClassName.ITEM, false);

      if ($items.hasClass(ClassName.DROPDOWN) && !$items.find(Selector.DROPDOWN_MENU).length) {
        throw new Error(`The .${ClassName.DROPDOWN} item(s) must contain the child .${ClassName.DROPDOWN_MENU} element.`);
      }

      // Get target

      const $parent = _parent === null ?
        $(this.content) :
        this._getNode(_parent, ClassName.DROPDOWN, false);

      let $target;

      if ($parent.hasClass(ClassName.CONTENT)) {
        $target = $parent;
      } else {
        // If floating dropdown
        if (this._isFloatingDropdown($parent[0]) && this.isDropdownOpened($parent[0])) {
          $target = $($parent.data('dropdown')).find(`.${ClassName.DROPDOWN_MENU_WRAPPER}`);
        } else {
          $target = $parent.find(Selector.DROPDOWN_MENU);
        }

        if (!$target.length) {
          throw new Error('Targeted element is not found.');
        }
      }

      // Insert items

      const $dropdownItems = $target.find(Selector.ITEM);

      if (!$dropdownItems.length) {
        $target.append($items);
      } else if (position === null) {
        $items.insertAfter($dropdownItems.last());
      } else {
        const $found = $dropdownItems.eq(position);

        if ($found.length) {
          $items.insertBefore($found);
        } else {
          $items.insertAfter($dropdownItems.last());
        }
      }

      // Update scrollbar

      if (!this.isCollapsed() || $parent.hasClass(ClassName.CONTENT)) {
        this._updateScrollbar(this.content);
      } else if ($target.hasClass(ClassName.DROPDOWN_MENU_WRAPPER)) {
        this._updateScrollbar($target[0]);
      } else {
        this._updateScrollbar($target.parents(`.${ClassName.DROPDOWN_MENU_WRAPPER}`)[0]);
      }

      return $items;
    }

    remove(_item) {
      const $item   = this._getNode(_item, ClassName.ITEM, false);
      const $parent = $item.parent();

      if ($item.hasClass(ClassName.DROPDOWN)) {
        $($item.data('dropdown')).remove();
      }

      $item.remove();

      if (!this.isCollapsed() || $parent.hasClass(ClassName.CONTENT)) {
        this._updateScrollbar(this.content);
      } else if ($parent.hasClass(ClassName.DROPDOWN_MENU_WRAPPER)) {
        this._updateScrollbar($parent[0]);
      } else {
        this._updateScrollbar($parent.parents(`.${ClassName.DROPDOWN_MENU_WRAPPER}`)[0]);
      }
    }

    destroy() {
      // Trigger before destroy event
      if (!this._triggerPreventableEvent('DESTROY', this.element)) {
        return;
      }

      this._unsetListeners();
      $(this.element).removeData(DATA_KEY);

      // Disable animations
      pxUtil.removeClass(this.element, ClassName.ANIMATE);

      // Disable transitions
      pxUtil.removeClass(this.element, ClassName.TRANSITIONING);

      // Reset nav state
      pxUtil.removeClass(this.element, ClassName.EXPAND);

      // Close dropdowns
      if (this.isCollapsed()) {
        this.closeAllDropdowns();
      }

      // Remove dimmer if no initialized navs exists

      let initializedNavs = 0;

      $(this.element.parentNode).find(`> .${ClassName.NAV}`).each(function() {
        if ($(this).data(DATA_KEY)) { initializedNavs++; }
      });

      if (!initializedNavs) {
        $(this.dimmer).remove();
      }

      // Destroy scrollbar
      $(this.element).find(`.${ClassName.CONTENT}`).perfectScrollbar('destroy');
      $(this.content).unwrap(Selector.SCROLLABLE_AREA);
    }

    // private

    _getNode(_el, requiredClass = ClassName.DROPDOWN, returnPlainNode = true) {
      const $el = typeof _el === 'string' ? $(this.element).find(_el) : $(_el);

      if (!$el.length) {
        throw new Error(`Element is not found.`);
      }

      if (requiredClass && !$el.hasClass(requiredClass)) {
        throw new Error(`Element(s) must have the .${requiredClass} class.`);
      }

      return returnPlainNode ? $el[0] : $el;
    }

    _getNodeOrCreate(_el, requiredClass = ClassName.DROPDOWN, returnPlainNode = true) {
      // Add simple check to detect CSS selector
      return this._getNode(
        (typeof _el === 'string' && (_el[0] === '#' || _el[0] === '.')) ? _el : $(_el),
        requiredClass,
        returnPlainNode
      );
    }

    _detectActiveItem() {
      const $activeItem = $(this.content).find(`.${ClassName.ITEM}.${ClassName.ACTIVE}:not(.${ClassName.DROPDOWN})`);

      if (!$activeItem.length) { return; }

      this.activateItem($activeItem.first());
    }

    _expandDropdown(el, animate = true) {
      if (pxUtil.hasClass(el, ClassName.OPEN)) { return; }

      const $dropdown = $(el).find(Selector.DROPDOWN_MENU);

      function complete() {
        $dropdown
          .removeClass(ClassName.TRANSITIONING)
          .height('');

        this._updateScrollbar(
          this.isCollapsed() ?
            $(el).parents(`.${ClassName.DROPDOWN_MENU_WRAPPER}`)[0] :
            this.content
        );

        // Trigger "opened" event
        this._triggerEvent('DROPDOWN_OPENED', el);
      }

      if (this.config.accordion) {
        this._closeAllDropdowns(el.parentNode, animate, $(el));
      }

      pxUtil.addClass(el, ClassName.OPEN);

      if (!$.support.transition || !animate) { return complete.call(this); }

      $dropdown
        .height(0)
        .addClass(ClassName.TRANSITIONING)
        .one('bsTransitionEnd', $.proxy(complete, this))
        .emulateTransitionEnd(this.config.transitionDuration)
        .height($dropdown[0].scrollHeight);
    }

    _collapseDropdown(el, animate = true) {
      if (!pxUtil.hasClass(el, ClassName.OPEN)) { return; }

      const $dropdown = $(el).find(Selector.DROPDOWN_MENU);

      function complete() {
        pxUtil.removeClass(el, ClassName.OPEN);
        $dropdown
          .removeClass(ClassName.TRANSITIONING)
          .height('');

        // Collapse all sub-dropdowns
        $(el).find(`.${ClassName.OPEN}`).removeClass(ClassName.OPEN);

        this._updateScrollbar(
          this.isCollapsed() ?
            $(el).parents(`.${ClassName.DROPDOWN_MENU_WRAPPER}`)[0] :
            this.content
        );

        // Trigger "closed" event
        this._triggerEvent('DROPDOWN_CLOSED', el);
      }

      if (!$.support.transition || !animate) { return complete.call(this); }

      $dropdown.height($dropdown.height())[0].offsetHeight;

      $dropdown
        .addClass(ClassName.TRANSITIONING)
        .height(0)
        .one('bsTransitionEnd', $.proxy(complete, this))
        .emulateTransitionEnd(this.config.transitionDuration);
    }

    _showDropdown(el) {
      if (pxUtil.hasClass(el, ClassName.SHOW) || !this._isRootDropdown(el)) { return; }

      const container = el.parentNode.parentNode;
      const dropdown  = $(el).find(Selector.DROPDOWN_MENU)[0];

      if (!dropdown) { return; }

      // Close all dropdowns
      this.closeAllDropdowns();

      const offsetTop = el.parentNode.offsetTop;
      const elTop     = el.offsetTop - el.parentNode.scrollTop;

      const $dropdownTitle =
        $(`<div class="${ClassName.DROPDOWN_MENU_TITLE}"></div>`)
          .html($(el).find(Selector.ITEM_LABEL).html())
          .prependTo(dropdown);

      pxUtil.addClass(el, ClassName.SHOW);
      pxUtil.addClass(dropdown, ClassName.SHOW);
      container.appendChild(dropdown);

      const elHeight    = $(el).outerHeight();
      const $items      = $(dropdown).find(Selector.ITEM);
      const itemHeight  = $items.first().find('> a').outerHeight();
      const visibleArea = $(this.element).outerHeight() - offsetTop;
      const titleHeight = $dropdownTitle.outerHeight();
      const minHeight   = titleHeight + itemHeight * 3;

      const dropdownWrapper =
        $(`<div class="${ClassName.DROPDOWN_MENU_WRAPPER}"></div>`)
          .append($items)
          .appendTo(dropdown)[0];

      let maxHeight;

      // Top dropdown
      if ((elTop + minHeight) > visibleArea) {
        maxHeight = elTop;

        if (this.isFixed() || this._curMode === 'tablet') {
          dropdown.style.bottom = `${visibleArea - elTop - elHeight}px`;
        } else {
          dropdown.style.bottom = '0px';
        }

        pxUtil.addClass(dropdown, ClassName.DROPDOWN_MENU_TOP);
        dropdown.appendChild($dropdownTitle[0]);

      // Bottom dropdown
      } else {
        maxHeight = visibleArea - elTop - titleHeight;

        dropdown.style.top = `${offsetTop + elTop}px`;
        dropdown.insertBefore($dropdownTitle[0], dropdown.firstChild);
      }

      dropdownWrapper.style.maxHeight = `${maxHeight - 10}px`;
      $(dropdownWrapper).perfectScrollbar(PERFECT_SCROLLBAR_OPTIONS);

      // Add event handlers
      $(dropdown)
        .on(this.constructor.Event.MOUSEENTER, () => this._clearDropdownTimer(el))
        .on(this.constructor.Event.MOUSELEAVE, () => this._setDropdownTimer(el));

      // Link elements
      $(el).data('dropdown', dropdown);
      $(dropdown).data('element', el);

      this._updateScrollbar(el.parentNode);

      // Trigger "opened" event
      this._triggerEvent('DROPDOWN_OPENED', el);
    }

    _hideDropdown(el) {
      if (!pxUtil.hasClass(el, ClassName.SHOW)) { return; }

      const dropdown = $(el).data('dropdown');

      if (!dropdown) { return; }

      // Remove classes
      pxUtil.removeClass(el, [ ClassName.SHOW, ClassName.FREEZE ]);
      pxUtil.removeClass(dropdown, ClassName.SHOW);
      pxUtil.removeClass(dropdown, ClassName.DROPDOWN_MENU_TOP);
      this.unfreezeDropdown(el);

      const $wrapper = $(dropdown).find(`.${ClassName.DROPDOWN_MENU_WRAPPER}`);

      // Remove title
      $(dropdown).find(`.${ClassName.DROPDOWN_MENU_TITLE}`).remove();

      // Remove wrapper
      $(dropdown).append($wrapper.find(Selector.ITEM));
      $wrapper.perfectScrollbar('destroy').remove();

      dropdown.setAttribute('style', '');
      el.appendChild(dropdown);

      $(el).data('dropdown', null);
      $(dropdown).data('element', null);

      this._clearDropdownTimer(el);

      // Remove event handlers
      $(dropdown).off('mouseenter').off('mouseleave');

      this._updateScrollbar(el.parentNode);

      // Trigger "closed" event
      this._triggerEvent('DROPDOWN_CLOSED', el);
    }

    _showTooltip(dropdown) {
      this._clearTooltips();

      const text = $(dropdown).find('.px-nav-label').contents().filter(function() {
        return this.nodeType === 3;
      }).text();

      const tooptip  = $(`<div class="${ClassName.TOOLTIP}"></div>`).text(text)[0];

      const offsetTop = dropdown.parentNode.offsetTop;
      const elTop     = dropdown.offsetTop - dropdown.parentNode.scrollTop;

      tooptip.style.top = `${offsetTop + elTop}px`;

      // Link dropdown
      $(tooptip).data('dropdown', dropdown);

      dropdown.parentNode.parentNode.appendChild(tooptip);
    }

    _updateTooltipPosition() {
      const tooltip = $(this.element).find(`.${ClassName.TOOLTIP}`)[0];

      if (!tooltip) { return; }

      const dropdown = $(tooltip).data('dropdown');

      if (!dropdown) {
        $(tooltip).remove();
        return;
      }

      const offsetTop = dropdown.parentNode.offsetTop;
      const elTop     = dropdown.offsetTop - dropdown.parentNode.scrollTop;

      tooltip.style.top = `${offsetTop + elTop}px`;
    }

    _clearTooltips() {
      $(this.element).find(`.${ClassName.TOOLTIP}`).remove();
    }

    _closeAllDropdowns(_parent, animate, $except = null) {
      const self  = this;

      let _selector;
      let method;
      let parent = _parent;

      if (this.isCollapsed() && pxUtil.hasClass(parent, ClassName.CONTENT)) {
        _selector = Selector.SHOWN_DROPDOWNS;
        method    = '_hideDropdown';
      } else {
        if (this._isFloatingDropdown(parent) && this.isDropdownOpened(parent)) {
          parent = $($(parent).data('dropdown')).find(`.${ClassName.DROPDOWN_MENU_WRAPPER}`)[0];
        } else if (pxUtil.hasClass(parent, ClassName.DROPDOWN)) {
          parent = $(parent).find(Selector.DROPDOWN_MENU)[0];
        }

        _selector = Selector.OPENED_DROPDOWNS;
        method    = '_collapseDropdown';
      }

      $(parent).find(_selector).each(function() {
        if ($except && $except === $(this)) { return; }

        self[method](this, animate);
      });
    }

    _isRootDropdown(el) {
      return pxUtil.hasClass(el.parentNode, ClassName.CONTENT);
    }

    _isFloatingDropdown(el) {
      return this.isCollapsed() && this._isRootDropdown(el);
    }

    _getNavState() {
      return ((this._curMode === 'phone' || this._curMode === 'tablet') && !pxUtil.hasClass(this.element, ClassName.EXPAND)) ||
             (this._curMode === 'desktop' && pxUtil.hasClass(this.element, ClassName.COLLAPSE));
    }

    _setDropdownTimer(el) {
      if (this.isDropdownFrozen(el)) { return; }

      this._clearDropdownTimer(el);

      const timer = setTimeout(() => {
        if (this.isDropdownFrozen(el)) { return; }

        this._hideDropdown(el);
      }, this.config.dropdownCloseDelay);

      $(el).data('timer', timer);
    }

    _clearDropdownTimer(el) {
      const timer = $(el).data('timer');

      if (!timer) { return; }

      clearTimeout(timer);
    }

    _updateScrollbar(el) {
      if (el && pxUtil.hasClass(el, ClassName.PERFECT_SCROLLBAR_CONTAINER)) {
        $(el).perfectScrollbar('update');
      }
    }

    _changeNavState(fn) {
      this._stateChanging++;

      if (this.config.animate && $.support.transition) {
        pxUtil.addClass(this.element, ClassName.NAV_TRANSITIONING);
      }

      function transitionComplete() {
        this._stateChanging = this._stateChanging < 2 ? 0 : this._stateChanging - 1;

        if (!this._stateChanging) {
          pxUtil.removeClass(this.element, ClassName.NAV_TRANSITIONING);
        }

        this._updateScrollbar(this.content);
        pxUtil.triggerResizeEvent();
      }

      fn.call(this);

      this._isCollapsed = this._getNavState();
      this._storeNavState();

      if (!this.config.animate || !$.support.transition) { return transitionComplete.call(this); }

      $(this.element)
        .one('bsTransitionEnd', $.proxy(transitionComplete, this))
        .emulateTransitionEnd(this.config.transitionDuration);
    }

    _getMode() {
      const screenSize = window.PixelAdmin.getScreenSize();

      let mode;

      if (this.config.modes.phone.indexOf(screenSize) !== -1) {
        mode = 'phone';
      } else if (this.config.modes.tablet.indexOf(screenSize) !== -1) {
        mode = 'tablet';
      } else if (this.config.modes.desktop.indexOf(screenSize) !== -1) {
        mode = 'desktop';
      } else {
        throw new Error('Cannot determine PxNav mode.');
      }

      return mode;
    }

    _prefixStorageKey(key) {
      return this.config.storagePrefix + (pxUtil.hasClass(this.element, ClassName.NAV_LEFT) ? 'left.' : 'right.') + key;
    }

    _storeNavState() {
      if (!this.config.storeState) { return; }

      const key   = this._prefixStorageKey('state');
      const state = pxUtil.hasClass(this.element, ClassName.COLLAPSE) ? 'collapsed' : 'expanded';

      window.PixelAdmin.storage.set(key, state);
    }

    _restoreNavState() {
      if (!this.config.storeState) { return; }

      const key   = this._prefixStorageKey('state');
      const state = window.PixelAdmin.storage.get(key) || 'expanded';


      pxUtil[state === 'collapsed' ? 'addClass' : 'removeClass'](this.element, ClassName.COLLAPSE);

      this._isCollapsed = this._getNavState();
      pxUtil.triggerResizeEvent();
    }

    _checkNavbarPosition() {
      if (!this.isFixed()) { return; }

      const navbar = $(this.element).find(Selector.NEAR_NAVBAR)[0];

      if (!navbar) { return; }

      if (!pxUtil.hasClass(navbar.parentNode, ClassName.NAVBAR_FIXED)) {
        console.warn(`The ${pxUtil.hasClass(this.element, ClassName.NAV_LEFT) ? 'left' : 'right'} .px-nav is fixed, but the coterminous .px-navbar isn't. You need to explicitly add the .${ClassName.NAVBAR_FIXED} class to the parent element to fix the navbar.`);

        pxUtil.addClass(navbar.parentNode, ClassName.NAVBAR_FIXED);
      }
    }

    _setupMarkup() {
      const $parent = $(this.element).parent();

      // Append dimmer
      if (!$parent.find(`> .${ClassName.DIMMER}`).length) {
        $parent.append(`<div class="${ClassName.DIMMER}"></div>`);
      }

      // Set scrollbar

      if (!$.fn.perfectScrollbar) {
        throw new Error('Scrolling feature requires the "perfect-scrollbar" plugin included.');
      }

      const $content = $(this.content);

      if ($content.length) {
        $content
          .wrap(`<div class="${ClassName.SCROLLABLE_AREA}"></div>`)
          .perfectScrollbar(PERFECT_SCROLLBAR_OPTIONS);
      }
    }

    _setListeners() {
      const self = this;

      $(window).on(`${this.constructor.Event.RESIZE}.${this.uniqueId}`, function() {
        self._curMode     = self._getMode();
        self._isCollapsed = self._getNavState();

        if (self.isCollapsed()) {
          self.closeAllDropdowns();
        }

        if (self.config.enableTooltips) {
          self._clearTooltips();
        }

        self._updateScrollbar(self.content);
      });

      $(this.element)
        .on(this.constructor.Event.CLICK, Selector.DROPDOWN_LINK, function(e) {
          e.preventDefault();

          const el = this.parentNode;

          if (self._isFloatingDropdown(el)) {
            if (self.isDropdownOpened(el)) {
              self[self.isDropdownFrozen(el) ? 'closeDropdown' : 'freezeDropdown'](el);
            } else {
              self.openDropdown(el);
              self.freezeDropdown(el);
            }
          } else {
            self.toggleDropdown(el);
          }
        });

      $(this.content)
        .on(this.constructor.Event.MOUSEENTER, Selector.DROPDOWN_LINK, function() {
          if (window.PixelAdmin.isMobile) { return; }

          const el = this.parentNode;

          if (!self._isFloatingDropdown(el) || pxUtil.hasClass(self.element, ClassName.OFF_CANVAS)) { return; }

          if (!self.isDropdownOpened(el)) {
            if ($(self.element).find(Selector.FROZEN_DROPDOWNS).length) { return; }
            self.openDropdown(el);
          } else {
            self._clearDropdownTimer(el);
          }
        })
        .on(this.constructor.Event.MOUSELEAVE, Selector.DROPDOWN_LINK, function() {
          if (window.PixelAdmin.isMobile) { return; }

          const el = this.parentNode;

          if (!self._isFloatingDropdown(el) || !self.isDropdownOpened(el)) { return; }

          self._setDropdownTimer(el);
        })
        .on(this.constructor.Event.MOUSEENTER, Selector.ROOT_LINK, function() {
          if (window.PixelAdmin.isMobile) { return; }

          if (!self.config.enableTooltips || !self.isCollapsed() || pxUtil.hasClass(self.element, ClassName.OFF_CANVAS)) { return; }

          self._showTooltip(this.parentNode);
        })
        .on(this.constructor.Event.MOUSELEAVE, Selector.ROOT_LINK, function() {
          if (window.PixelAdmin.isMobile) { return; }

          if (!self.config.enableTooltips) { return; }

          self._clearTooltips();
        })
        .on(this.constructor.Event.SCROLL, () => {
          if (!this.isCollapsed()) { return; }

          if (this.config.enableTooltips) {
            this._updateTooltipPosition();
          }

          this.closeAllDropdowns();
        });
    }

    _unsetListeners() {
      $(window).off(`${this.constructor.Event.RESIZE}.${this.uniqueId}`);
      $(this.element).off(EVENT_KEY);
      $(this.content)
        .off(EVENT_KEY)
        .find(`.${ClassName.DROPDOWN_MENU}`).off(EVENT_KEY);

      // Reset dimmer
      if (this._curMode !== 'desktop' && pxUtil.hasClass(this.element, ClassName.EXPAND)) {
        $(this.dimmer).off(EVENT_KEY);
      }
    }

    _enableAnimation() {
      if (!this.config.animate) { return; }

      // Prevent animation "blink"
      pxUtil.addClass(this.element, [ 'off', ClassName.ANIMATE ]);

      setTimeout(() => {
        pxUtil.removeClass(this.element, 'off');
      }, this.config.transitionDuration);
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

    _getConfig(config) {
      return $.extend({},
        this.constructor.Default,
        $(this.element).data(),
        config
      );
    }

    // static

    static _jQueryInterface(config, ...args) {
      let result;

      const $el = this.each(function() {
        let data    = $(this).data(DATA_KEY);
        let _config = typeof config === 'object' ? config : null;

        if (!data) {
          data = new Nav(this, _config);
          $(this).data(DATA_KEY, data);
        }

        if (typeof config === 'string') {
          if (!data[config]) {
            throw new Error(`No method named "${config}"`);
          }
          result = data[config].apply(data, args);
        }
      });

      return typeof result !== 'undefined' ? result : $el;
    }
  }


  /**
   * ------------------------------------------------------------------------
   * Data Api implementation
   * ------------------------------------------------------------------------
   */

  $(document).on(Event.CLICK_DATA_API, Selector.DATA_TOGGLE, function(e) {
    e.preventDefault();

    let $target = $($(this).data('target'));

    if (!$target.length) {
      $target = $(this).parents(`.${ClassName.NAV}`);
    }

    if (!$target.length) { return; }

    if (!$target.data(DATA_KEY)) {
      Nav._jQueryInterface.call($target, $(this).data());
    }

    Nav._jQueryInterface.call($target, 'toggle');
  });


  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME]             = Nav._jQueryInterface;
  $.fn[NAME].Constructor = Nav;
  $.fn[NAME].noConflict  = function() {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return Nav._jQueryInterface;
  };

  return Nav;
})(jQuery);

export default PxNav;
