import jQuery from 'jquery';
import 'px-libs/jquery.growl';

// Extensions / Growl
// --------------------------------------------------

($ => {
  'use strict';

  if (!$.growl) {
    throw new Error('jquery.growl.js required.');
  }

  $.growl.success = function(options) {
    return $.growl($.extend({
      title: 'Success!',
      style: 'success',
    }, options || {}));
  };
})(jQuery);
