import jQuery from 'jquery';
import 'px-bootstrap/alert';

// Plugins / PxBlockAlert
// --------------------------------------------------

const PxBlockAlert = (function($) {
  'use strict';

  /**
   * ------------------------------------------------------------------------
   * Constants
   * ------------------------------------------------------------------------
   */

  const NAME               = 'pxBlockAlert';
  const JQUERY_NO_CONFLICT = $.fn[NAME];

  const Default = {
    type:        null,
    style:       null,
    namespace:   'default',
    animate:     true,
    timer:       0,
    closeButton: true,
  };

  /**
   * ------------------------------------------------------------------------
   * Class Definition
   * ------------------------------------------------------------------------
   */

  const BlockAlert = {

    // public

    add($el, content, _config = {}) {
      if (!content) {
        throw new Error('Content is not specified');
      }

      const config         = BlockAlert._getConfig(_config);
      const $container     = BlockAlert._getContainer($el);
      const namespaceClass = `px-block-alerts-namespace--${config.namespace}`;

      const $namespaceContainer = $(
        $container.find(`.${namespaceClass}`)[0] ||
        $(`<div class="${namespaceClass}"></div>`).appendTo($container)[0]
      );

      const $alert = $(`<div class="alert"></div>`);

      if (config.closeButton) {
        $alert.append('<button type="button" class="close">×</button>');
      }

      if (config.type) {
        $alert.addClass(`alert-${config.type}`);
      }

      if (config.style) {
        $alert.addClass(`alert-${config.style}`);
      }

      $alert
        .addClass(`${namespaceClass}__alert`)
        .append(content);

      $container.removeClass('px-block-alerts-empty');

      if (config.animate) {
        $alert
          .css('display', 'none')
          .attr('data-animate', 'true');
      }

      $namespaceContainer.append($alert);

      if (config.animate) {
        $alert.slideDown(300);
      }

      if (config.timer) {
        $alert.data(
          'px-block-alert-timer',
          setTimeout(() => BlockAlert.remove($el, $alert, config.animate), 1000 * config.timer)
        );
      }
    },

    remove($el, $target, animate = true) {
      const $alert = $el.find($target);

      if (!$alert.length) {
        return;
      }

      const timer = $alert.data('px-block-alert-timer');

      if (timer) {
        clearTimeout(timer);
        $alert.data('px-block-alert-timer', null);
      }

      function done() {
        const $container = $el.find('> .px-block-alerts');

        $alert.remove();

        if (!$container.find('.alert').length) {
          $container.addClass('px-block-alerts-empty');
        }
      }

      if ($alert.attr('data-animate') === 'true' && animate === true) {
        return $alert.slideUp(300, done);
      }

      done();
    },

    clear($el, namespace = 'default', animate = true) {
      if (typeof namespace !== 'string') {
        throw new Error('Namespace must be a string.');
      }

      const $namespaceContainer = $el.find(
        `> .px-block-alerts .px-block-alerts-namespace--${namespace}`
      );

      if (!$namespaceContainer.length) {
        return;
      }

      $namespaceContainer.find('.alert').each(function() {
        BlockAlert.remove($el, $(this), animate);
      });
    },

    clearAll($el, animate = true) {
      $el.find('> .px-block-alerts .alert').each(function() {
        BlockAlert.remove($el, $(this), animate);
      });
    },

    destroy($el) {
      const $container = $el.find('> .px-block-alerts');

      if (!$container.length) {
        return;
      }

      BlockAlert._unsetListeners($container);
      $container.remove();
    },

    // private

    _getContainer($el) {
      let $container = $el.find('> .px-block-alerts');

      if (!$container.length) {
        $container = $('<div class="px-block-alerts"></div>');

        if (!$el.hasClass('panel')) {
          $container.prependTo($el);
        } else {
          let $header = $el.find('> .panel-heading');

          if (!$header.length) {
            $header = $el.find('> .panel-subtitle');
          }

          if (!$header.length) {
            $header = $el.find('> .panel-title');
          }

          if ($header.length) {
            $container.insertAfter($header.first());
          } else {
            $container.prependTo($el);
          }
        }
      }

      if (!$container.data('pxBlockAlert-listenersDefined')) {
        BlockAlert._setListeners($container);
        $container.data('pxBlockAlert-listenersDefined', true);
      }

      return $container;
    },

    _getConfig(config) {
      const result = $.extend({}, Default, config);

      result.animate     = !(result.animate === 'false' || result.animate === false);
      result.closeButton = !(result.closeButton === 'false' || result.closeButton === false);
      result.timer       = parseInt(String(result.timer), 10) || 0;

      return result;
    },

    _setListeners($container) {
      $container.on('click', '.close', function() {
        BlockAlert.remove($container.parent(), $(this).parents('.alert'));
      });
    },

    _unsetListeners($container) {
      $container.off();
    },
  };

  function _jQueryInterface(...args) {
    return this.each(function() {
      if ([ 'remove', 'clear', 'clearAll', 'destroy' ].indexOf(args[0]) !== -1) {
        return BlockAlert[args[0]].apply(null, [$(this)].concat(args.slice(1)));
      }

      BlockAlert.add($(this), args[0], ...args.slice(1));
    });
  }

  /**
   * ------------------------------------------------------------------------
   * jQuery
   * ------------------------------------------------------------------------
   */

  $.fn[NAME]             = _jQueryInterface;
  $.fn[NAME].noConflict  = function() {
    $.fn[NAME] = JQUERY_NO_CONFLICT;
    return _jQueryInterface;
  };

  return BlockAlert;
})(jQuery);

export default PxBlockAlert;
