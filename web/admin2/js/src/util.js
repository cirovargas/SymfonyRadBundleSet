const pxUtil = (function() {
  'use strict';

  const classListSupported = ('classList' in document.documentElement);

  /* Based on https://github.com/toddmotto/apollo */

  function forEach(items, fn) {
    let itemsArray = Object.prototype.toString.call(items) === '[object Array]' ?
      items :
      items.split(' ');

    for (var i = 0; i < itemsArray.length; i++) {
      fn(itemsArray[i], i);
    }
  }

  const hasClass = classListSupported ?
    ((elem, className) => elem.classList.contains(className)) :
    ((elem, className) => new RegExp(`(?:^|\\s)${className}(?:\\s|$)`).test(elem.className));

  const addClass = classListSupported ?
    ((elem, className) => elem.classList.add(className)) :
    function(elem, className) {
      if (hasClass(elem, className)) { return; }
      elem.className += (elem.className ? ' ' : '') + className;
    };

  const removeClass = classListSupported ?
    ((elem, className) => elem.classList.remove(className)) :
    function(elem, className) {
      if (!hasClass(elem, className)) { return; }
      elem.className = elem.className
        .replace(new RegExp(`(?:^${className}\\s+)|(?:^\\s*${className}\\s*$)|(?:\\s+${className}$)`, 'g'), '')
        .replace(new RegExp(`\\s+${className}\\s+`, 'g'), ' ');
    };

  const toggleClass = classListSupported ?
    ((elem, className) => elem.classList.toggle(className)) :
    ((elem, className) => (hasClass(elem, className) ? removeClass : addClass)(elem, className));

  /*** ***/

  return {
    // Based on http://stackoverflow.com/a/34168882
    generateUniqueId() {
      // desired length of Id
      var idStrLen = 32;

      // always start with a letter -- base 36 makes for a nice shortcut
      var idStr = (Math.floor((Math.random() * 25)) + 10).toString(36) + '_';

      // add a timestamp in milliseconds (base 36 again) as the base
      idStr += (new Date()).getTime().toString(36) + '_';

      // similar to above, complete the Id using random, alphanumeric characters
      do {
        idStr += (Math.floor((Math.random() * 35))).toString(36);
      } while (idStr.length < idStrLen);

      return idStr;
    },

    escapeRegExp(str) {
      return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
    },

    hexToRgba(color, opacity) {
      const hex = color.replace('#', '');
      const r   = parseInt(hex.substring(0, 2), 16);
      const g   = parseInt(hex.substring(2, 4), 16);
      const b   = parseInt(hex.substring(4, 6), 16);

      return `rgba(${r}, ${g}, ${b}, ${opacity})`;
    },

    // Triggers native resize event
    triggerResizeEvent() {
      let event;

      if (document.createEvent) {
        event = document.createEvent('HTMLEvents');
        event.initEvent('resize', true, true);
      } else {
        event = document.createEventObject();
        event.eventType = 'resize';
      }

      event.eventName = 'resize';

      if (document.createEvent) {
        window.dispatchEvent(event);
      } else {
        window.fireEvent(`on${event.eventType}`, event);
      }
    },

    hasClass(elem, className) {
      return hasClass(elem, className);
    },

    addClass(elem, classes) {
      forEach(classes, className => addClass(elem, className));
    },

    removeClass(elem, classes) {
      forEach(classes, className => removeClass(elem, className));
    },

    toggleClass(elem, classes) {
      forEach(classes, className => toggleClass(elem, className));
    },
  };
})();

export default pxUtil;
