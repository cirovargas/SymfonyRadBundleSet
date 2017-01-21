import jQuery from 'jquery';
import pxUtil from 'px/util';
import 'px-libs/additional-methods';

// Plugins / PxValidate
// --------------------------------------------------

const PxValidate = (function($) {
  'use strict';

  if (!$.fn.validate) {
    throw new Error('jquery.validate.js required.');
  }

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  const NAME               = 'pxValidate';
  const DATA_KEY           = 'px.validate';
  const JQUERY_NO_CONFLICT = $.fn[NAME];

  const ClassName = {
    FORM_HELP: 'form-help-text',
    HAS_ERROR: 'has-validation-error',
    ERROR:     'validation-error',
    CONTAINER: 'validation-container',
    NO_ARROW:  'validation-error-no-arrow',
  };

  const Default = {
    errorElement: 'div',
    errorClass:   `form-message ${ClassName.ERROR}`,
  };

  const CONTAINER_REGEX = new RegExp(`(^|\\s)(?:${ClassName.CONTAINER}|form-group|col-(?:xs|sm|md|lg)-\\d+)(\\s|$)`);

  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  class PxValidator {
    constructor(element, config) {
      this.element = element;

      this.validator = $(element).validate(
        this._getConfig(element, config)
      );
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

    // public

    getValidator() {
      return this.validator;
    }

    destroy() {
      this.validator.destroy();
      $(this.element).removeData(DATA_KEY);
    }

    // private

    _highlight(control) {
      pxUtil.addClass(
        $(control).parents('.form-group')[0],
        `has-error ${ClassName.HAS_ERROR}`
      );
    }

    _unhighlight(control) {
      pxUtil.removeClass(
        $(control).parents('.form-group')[0],
        `has-error ${ClassName.HAS_ERROR}`
      );
    }

    _errorPlacement($error, $element) {
      const $container  = $(this._getParentContainer($element[0]));

      if (!$container.length) { return; }

      // Remove an old error
      $container
        .find(`.${ClassName.ERROR}`)
        .remove();

      // Check if the element is a checkbox or radio
      //

      let elementType = $element[0].getAttribute('type');

      // Normalize type to avoid bugs
      elementType = elementType ? elementType.toLowerCase() : null;

      if (elementType === 'checkbox' || elementType === 'radio') {
        pxUtil.addClass($error[0], ClassName.NO_ARROW);
      }

      // Append error
      //

      const $helpBlock = $container.find(`.${ClassName.FORM_HELP}`).first();

      if ($helpBlock.length) {
        $error.insertBefore($helpBlock);
      } else {
        $container.append($error);
      }
    }

    _getParentContainer(element) {
      const parent   = element.parentNode;
      const nodeName = parent.nodeName.toUpperCase();

      if (nodeName === 'FORM' || nodeName === 'BODY') {
        console.error(new Error('Cannot find parent container.'));

        return null;
      }

      return CONTAINER_REGEX.test(parent.className) ?
         parent :
         this._getParentContainer(parent);
    }

    _getConfig(element, config) {
      return $.extend({},
        this.constructor.Default,
        {
          highlight:      this._highlight,
          unhighlight:    this._unhighlight,
          errorPlacement: $.proxy(this._errorPlacement, this),
        },
        $(element).data(),
        config
      );
    }

    // static

    static _jQueryInterface(config) {
      let result;

      const $el = this.each(function() {
        let data      = $(this).data(DATA_KEY);
        const _config = typeof config === 'object' ? config : null;

        if (!data) {
          data = new PxValidator(this, _config);
          $(this).data(DATA_KEY, data);
        }

        if (typeof config === 'string') {
          if (!data[config]) {
            throw new Error(`No method named "${config}".`);
          }
          result = data[config]();
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

  $.fn[NAME]             = PxValidator._jQueryInterface;
  $.fn[NAME].Constructor = PxValidator;
  $.fn[NAME].noConflict  = function() {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return PxValidator._jQueryInterface;
  };

  return PxValidator;
})(jQuery);

export default PxValidate;
