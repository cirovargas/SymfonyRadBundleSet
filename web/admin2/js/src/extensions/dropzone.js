import jQuery from 'jquery';
import 'px-libs/dropzone';

// Extensions / Dropzone
// --------------------------------------------------

(($, Dropzone) => {
  'use strict';

  if (!Dropzone) {
    throw new Error('dropzone.js required.');
  }

  const dropzoneError = Dropzone.prototype.defaultOptions.error;

  // Extend Dropzone default options
  Dropzone.prototype.defaultOptions = $.extend({}, Dropzone.prototype.defaultOptions, {
    previewTemplate: `
<div class="dz-preview dz-file-preview">
  <div class="dz-details">
    <div class="dz-filename" data-dz-name></div>
    <div class="dz-size" data-dz-size></div>
    <div class="dz-thumbnail">
      <img data-dz-thumbnail>
      <span class="dz-nopreview">No preview</span>
      <div class="dz-success-mark"></div>
      <div class="dz-error-mark"></div>
      <div class="dz-error-message"><span data-dz-errormessage></span></div>
    </div>
  </div>
  <div class="progress">
    <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
  </div>
</div>`,
    addRemoveLinks: true,

    error(file, message) {
      const result = dropzoneError.call(this, file, message);

      if (file.previewElement) {
        $(file.previewElement)
          .find('.progress-bar-success')
          .removeClass('progress-bar-success')
          .addClass('progress-bar-danger');
      }

      return result;
    },
  });

  // jQuery plugin
  $.fn.dropzone = function(config, ...args) {
    return this.each(function() {
      let data    = $(this).data('dropzone');
      let _config = typeof config === 'object' ? config : null;

      if (!data) {
        data = new Dropzone(this, _config);
        $(this).data('dropzone', data);
      }

      if (typeof config === 'string') {
        if (!data[config]) {
          throw new Error(`No method named "${config}".`);
        }
        data[config](...args);
      }
    });
  };
})(jQuery, window.Dropzone);
