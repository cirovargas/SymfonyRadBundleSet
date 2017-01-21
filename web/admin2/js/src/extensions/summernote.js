import jQuery from 'jquery';
import 'px-libs/summernote';

// Extensions / Summernote
// --------------------------------------------------

($ => {
  'use strict';

  if (!$.summernote) {
    throw new Error('summernote.js required.');
  }

  function PxFullscreen(context) {
    const $editor   = context.layoutInfo.editor;
    const $toolbar  = context.layoutInfo.toolbar;
    const $editable = context.layoutInfo.editable;
    const $codable  = context.layoutInfo.codable;

    const $window    = $(window);
    const $scrollbar = $('html, body');

    this.toggle = function() {
      function resize(size) {
        $editable.css('height', size.h);
        $codable.css('height', size.h);

        if ($codable.data('cmeditor')) {
          $codable.data('cmeditor').setsize(null, size.h);
        }
      }

      $editor.toggleClass('fullscreen');

      if (this.isFullscreen()) {
        $editable.data('orgHeight', $editable.css('height'));

        $window.on('resize.px.summernote', function() {
          resize({
            h: $window.height() - $toolbar.outerHeight(),
          });
        }).trigger('resize');

        $scrollbar.addClass('summernote-fullscreen');
      } else {
        $window.off('resize.px.summernote');
        resize({
          h: $editable.data('orgHeight'),
        });
        $scrollbar.removeClass('summernote-fullscreen');
      }

      context.invoke('toolbar.updateFullscreen', this.isFullscreen());
    };

    this.isFullscreen = function() {
      return $editor.hasClass('fullscreen');
    };
  }

  // Default summernote icons
  const summernoteIcons = $.summernote.options.icons;

  $.summernote.options = $.extend($.summernote.options, {
    defaultIcons: summernoteIcons,

    icons: {
      align:         'fa fa-align-left',
      alignCenter:   'fa fa-align-center',
      alignJustify:  'fa fa-align-justify',
      alignLeft:     'fa fa-align-left',
      alignRight:    'fa fa-align-right',
      indent:        'fa fa-indent',
      outdent:       'fa fa-outdent',
      arrowsAlt:     'fa fa-arrows-alt',
      bold:          'fa fa-bold',
      caret:         'fa fa-caret-down',
      circle:        'fa fa-circle-o',
      close:         'fa fa-close',
      code:          'fa fa-code',
      eraser:        'fa fa-eraser',
      font:          'fa fa-font',
      frame:         'fa fa-',
      italic:        'fa fa-italic',
      link:          'fa fa-link',
      unlink:        'fa fa-unlink',
      magic:         'fa fa-magic',
      menuCheck:     'fa fa-check',
      minus:         'fa fa-minus',
      orderedlist:   'fa fa-list-ol',
      pencil:        'fa fa-pencil',
      picture:       'fa fa-picture-o',
      question:      'fa fa-question',
      redo:          'fa fa-repeat',
      square:        'fa fa-square-o',
      strikethrough: 'fa fa-strikethrough',
      subscript:     'fa fa-subscript',
      superscript:   'fa fa-superscript',
      table:         'fa fa-table',
      textHeight:    'fa fa-text-height',
      trash:         'fa fa-trash',
      underline:     'fa fa-underline',
      undo:          'fa fa-undo',
      unorderedlist: 'fa fa-list-ul',
      video:         'fa fa-video-camera',
    },
  });

  $.summernote.options.modules = $.extend($.summernote.options.modules, {
    fullscreen: PxFullscreen,
  });

})(jQuery);
