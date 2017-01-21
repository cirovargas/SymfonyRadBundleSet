import jQuery from 'jquery';
import pxUtil from 'px/util';

// Plugins / PxWizard
// --------------------------------------------------

const PxWizard = (function($) {
  'use strict';

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  const NAME               = 'pxWizard';
  const DATA_KEY           = 'px.wizard';
  const EVENT_KEY          = `.${DATA_KEY}`;
  const JQUERY_NO_CONFLICT = $.fn[NAME];


  const Default = {
    minStepWidth: 200,
  };

  const ClassName = {
    WRAPPER:   'wizard-wrapper',
    STEPS:     'wizard-steps',
    PANE:      'wizard-pane',
    FROZEN:    'frozen',
    FINISHED:  'finished',
    ACTIVE:    'active',
    COMPLETED: 'completed',
  };

  const Event = {
    RESIZE:   `resize${EVENT_KEY}`,
    CLICK:    `click${EVENT_KEY}`,
    CHANGE:   `stepchange${EVENT_KEY}`,
    CHANGED:  `stepchanged${EVENT_KEY}`,
    FINISH:   `finish${EVENT_KEY}`,
    FINISHED: `finished${EVENT_KEY}`,
    FROZEN:   `frozen${EVENT_KEY}`,
    UNFROZEN: `unfrozen${EVENT_KEY}`,
    RESETED:  `reseted${EVENT_KEY}`,
    DESTROY:  `destroy${EVENT_KEY}`,
  };


  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  class Wizard {
    constructor(element, config) {
      this.uniqueId = pxUtil.generateUniqueId();

      this.element   = element;
      this.steps     = $(element).find(`.${ClassName.STEPS}`)[0];
      this.stepItems = $(this.steps).find('li');
      this.wrapper   = $(element).find(`.${ClassName.WRAPPER}`)[0];
      this.config    = this._getConfig(config);

      this.activeStep = null;

      this._isRtl = $('html').attr('dir') === 'rtl';

      this._resetStepsWidth();
      this.resizeStepItems();
      this.goTo(this.getActiveStepIndex());

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

    resizeStepItems() {
      const stepCount       = this.stepItems.length;
      const curWrapperWidth = $(this.wrapper).width();
      const minWrapperWidth = this.config.minStepWidth * stepCount;

      const newWidth = curWrapperWidth > minWrapperWidth ?
        Math.floor(curWrapperWidth / stepCount) :
        this.config.minStepWidth;

      for (let i = 0; i < stepCount; i++) {
        this._setStrictWidth(this.stepItems[i], newWidth);
      }

      if (this.activeStep !== null) {
        this._placeStepsContainer();
      }
    }

    getActiveStepIndex() {
      let curStep = this.activeStep ||
        $(this.steps).find(`li.${ClassName.ACTIVE}`)[0];

      if (!curStep) { return 0; }

      return this._getStepIndex(curStep);
    }

    getStepCount() {
      return this.stepItems.length;
    }

    goTo(step) {
      if (this.isFrozen() || this.isFinished()) { return; }

      let stepItem;
      let stepIndex;
      let stepTarget;

      [ stepIndex, stepItem, stepTarget ] = this._getStepItemAndTarget(step);

      // Get active step index
      let activeStepIndex = this.activeStep ?
                           this._getStepIndex(this.activeStep) :
                           null;

      if (activeStepIndex !== null && stepIndex === activeStepIndex) {
        return;
      }

      if (activeStepIndex !== null) {
        // Trigger before change event
        const eventResult = this._triggerPreventableEvent('CHANGE', this.element, {
          activeStepIndex: activeStepIndex,
          nextStepIndex:   stepIndex,
        });

        if (!eventResult) {
          return;
        }
      }

      this.activeStep = stepItem;

      this._activateStepItem(stepItem, stepIndex);
      this._activateStepPane(stepTarget);

      if (activeStepIndex !== null) {
        // Trigger after change event
        this._triggerEvent('CHANGED', this.element, {
          prevStepIndex:   activeStepIndex,
          activeStepIndex: stepIndex,
        });
      }
    }

    getPaneByIndex(stepIndex) {
      let _stepItem;
      let _stepIndex;
      let stepTarget;

      [ _stepIndex, _stepItem, stepTarget ] = this._getStepItemAndTarget(stepIndex);

      return $(stepTarget);
    }

    getActivePane() {
      return this.getPaneByIndex(this.getActiveStepIndex());
    }

    goNext() {
      if (this.isFrozen() || this.isFinished()) { return; }

      const nextIndex = this._getStepIndex(this.activeStep) + 1;

      if (nextIndex >= this.stepItems.length) { return this.finish(); }

      this.goTo(nextIndex);
    }

    goPrev() {
      if (this.isFrozen() || this.isFinished()) { return; }

      const prevIndex = this._getStepIndex(this.activeStep) - 1;

      if (prevIndex < 0) { return; }

      this.goTo(prevIndex);
    }

    finish() {
      if (this.isFrozen() || this.isFinished()) { return; }

      // Trigger before finish event
      if (!this._triggerPreventableEvent('FINISH', this.element)) {
        return;
      }

      const curIndex  = this._getStepIndex(this.activeStep);
      const lastIndex = this.stepItems.length - 1;

      if (curIndex !== lastIndex) {
        this.goTo(lastIndex);
      }

      pxUtil.addClass(this.element, ClassName.FINISHED);
      this.freeze();

      // Trigger after finish event
      this._triggerEvent('FINISHED', this.element);
    }

    isFinished() {
      return pxUtil.hasClass(this.element, ClassName.FINISHED);
    }

    freeze() {
      pxUtil.addClass(this.element, ClassName.FROZEN);

      // Trigger after freeze event
      this._triggerEvent('FROZEN', this.element);
    }

    unfreeze() {
      if (this.isFinished()) { return; }

      pxUtil.removeClass(this.element, ClassName.FROZEN);

      // Trigger after unfreeze event
      this._triggerEvent('UNFROZEN', this.element);
    }

    isFrozen() {
      return pxUtil.hasClass(this.element, ClassName.FROZEN);
    }

    reset(resetSteps = true) {
      pxUtil.removeClass(this.element, ClassName.FROZEN);
      pxUtil.removeClass(this.element, ClassName.FINISHED);

      if (resetSteps) {
        this.goTo(0);
      }

      // Trigger after reset event
      this._triggerEvent('RESETED', this.element);
    }

    destroy() {
      // Trigger before destroy event
      if (!this._triggerPreventableEvent('DESTROY', this.element)) {
        return;
      }

      this._unsetListeners();
      $(this.element).removeData(DATA_KEY);
    }

    // private

    _resetStepsWidth() {
      this.steps.style.width = 'auto';
    }

    _setStrictWidth(element, width) {
      element.style.minWidth = `${width}px`;
      element.style.maxWidth = `${width}px`;
      element.style.width    = `${width}px`;
    }

    _getStepItemAndTarget(step) {
      let stepItem;
      let stepIndex;

      if (typeof step === 'number') {
        stepItem = this.stepItems[step];
        stepIndex = step;

        if (!stepItem) {
          throw new Error(`Step item with index "${step}" is not found.`);
        }
      } else {
        stepItem = step[0] || step;
        stepIndex = this._getStepIndex(stepItem);
      }

      const stepTarget = stepItem.getAttribute('data-target');

      if (!stepTarget) {
        throw new Error(`The step item has invalid "data-target" attribute.`);
      }

      return [ stepIndex, stepItem, stepTarget ];
    }

    _activateStepItem(stepItem, index) {
      pxUtil.addClass(stepItem, ClassName.ACTIVE);
      pxUtil.removeClass(stepItem, ClassName.COMPLETED);

      // Add completed and remove active classes for the previous items
      for (let i = 0; i < index; i++) {
        pxUtil.addClass(this.stepItems[i], ClassName.COMPLETED);
        pxUtil.removeClass(this.stepItems[i], ClassName.ACTIVE);
      }

      // Remove completed and active classes for the next items
      for (let j = index + 1, len = this.stepItems.length; j < len; j++) {
        pxUtil.removeClass(this.stepItems[j], ClassName.ACTIVE);
        pxUtil.removeClass(this.stepItems[j], ClassName.COMPLETED);
      }

      this._placeStepsContainer();
    }

    _activateStepPane(selector) {
      const panes = $(this.element).find(
        `.${ClassName.PANE}.${ClassName.ACTIVE}`
      );

      // Remove active state
      for (let i = 0, len = panes.length; i < len; i++) {
        pxUtil.removeClass(panes[i], ClassName.ACTIVE);
      }

      // Add active class to target
      pxUtil.addClass($(this.element).find(selector)[0], ClassName.ACTIVE);
    }

    _placeStepsContainer() {
      const wrapperWidth = $(this.wrapper).width();
      const stepsWidth   = $(this.steps).width();
      const curStepWidth = $(this.activeStep).outerWidth();
      const delta        = Math.floor((wrapperWidth - curStepWidth) / 2);
      let curStepX     = $(this.activeStep).position().left;
      let offset;

      if (this._isRtl) {
        curStepX = stepsWidth - curStepX - curStepWidth;
      }

      if (stepsWidth > wrapperWidth && curStepX > delta) {
        offset = -1 * curStepX + delta;

        if ((stepsWidth + offset) < wrapperWidth) {
          offset = -1 * stepsWidth + wrapperWidth;
        }
      } else {
        offset = 0;
      }

      this.steps.style[this._isRtl ? 'right' : 'left'] = `${offset}px`;
    }

    _getStepIndex(stepItem) {
      let stepIndex;

      for (let i = 0, len = this.stepItems.length; i < len; i++) {
        if (stepItem === this.stepItems[i]) {
          stepIndex = i;
          break;
        }
      }

      if (typeof stepIndex === 'undefined') {
        throw new Error('Cannot find step item index.');
      }

      return stepIndex;
    }

    _setListeners() {
      const self = this;

      $(window).on(
        `${this.constructor.Event.RESIZE}.${this.uniqueId}`,
        $.proxy(this.resizeStepItems, this)
      );

      $(this.steps).on(
        this.constructor.Event.CLICK,
        '> li',
        function() {
          if (!pxUtil.hasClass(this, ClassName.COMPLETED)) { return; }
          self.goTo(this);
        }
      );

      $(this.element).on(
        this.constructor.Event.CLICK,
        '[data-wizard-action]',
        function() {
          const action = this.getAttribute('data-wizard-action');

          if (action === 'next') {
            return self.goNext();
          }

          if (action === 'prev') {
            return self.goPrev();
          }

          if (action === 'finish') {
            return self.finish();
          }

          throw new Error(`Action "${action}" is not found.`);
        }
      );
    }

    _unsetListeners() {
      $(window).off(`${this.constructor.Event.RESIZE}.${this.uniqueId}`);
      $(this.element).off(EVENT_KEY);
      $(this.steps).off(EVENT_KEY);
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
        let data      = $(this).data(DATA_KEY);
        const _config = typeof config === 'object' ? config : null;

        if (!data) {
          data = new Wizard(this, _config);
          $(this).data(DATA_KEY, data);
        }

        if (typeof config === 'string') {
          if (!data[config]) {
            throw new Error(`No method named "${config}".`);
          }
          result = data[config].apply(data, args);
        }
      });

      return typeof result !== 'undefined' ? result : $el;
    }

  }

  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME]             = Wizard._jQueryInterface;
  $.fn[NAME].Constructor = Wizard;
  $.fn[NAME].noConflict  = function() {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return Wizard._jQueryInterface;
  };

  return Wizard;
})(jQuery);

export default PxWizard;
