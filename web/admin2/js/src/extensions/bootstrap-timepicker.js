import jQuery from 'jquery';
import 'px/extensions/modal';
import 'px-libs/bootstrap-timepicker';

// Extensions / Bootstrap-timepicker
// --------------------------------------------------

($ => {
  'use strict';

  if (!$.fn.timepicker) {
    throw new Error('bootstrap-timepicker.js required.');
  }

  const timepickerInit  = $.fn.timepicker.Constructor.prototype._init;
  const timepickerPlace = $.fn.timepicker.Constructor.prototype.place;

  $.fn.timepicker.Constructor.prototype._init = function() {
    this.$element
      .on({
        'focus.timepicker':   $.proxy(this.highlightUnit, this),
        'click.timepicker':   $.proxy(this.highlightUnit, this),
        'keydown.timepicker': $.proxy(this.elementKeydown, this),
        'blur.timepicker':    $.proxy(this.blurElement, this),
      });

    this.$element
      .parent('.input-group')
      .find('.input-group-addon')
      .addClass('bootstrap-timepicker-trigger')
      .on('click.timepicker', $.proxy(this.showWidget, this));

    timepickerInit.call(this);
  };

  $.fn.timepicker.Constructor.prototype.place = function() {
    if (this.template !== 'dropdown') { return; }
    timepickerPlace.call(this);
  };

  $.fn.timepicker.Constructor.prototype.getTemplate = function() {
    let hourTemplate;
    let minuteTemplate;
    let secondTemplate;
    let meridianTemplate;

    if (this.showInputs) {
      hourTemplate     = '<input type="text" name="hour" class="bootstrap-timepicker-hour form-control timepicker-input" maxlength="2"/>';
      minuteTemplate   = '<input type="text" name="minute" class="bootstrap-timepicker-minute form-control timepicker-input" maxlength="2"/>';
      secondTemplate   = '<input type="text" name="second" class="bootstrap-timepicker-second form-control timepicker-input" maxlength="2"/>';
      meridianTemplate = '<input type="text" name="meridian" class="bootstrap-timepicker-meridian form-control timepicker-input" maxlength="2"/>';
    } else {
      hourTemplate     = '<span class="bootstrap-timepicker-hour timepicker-value"></span>';
      minuteTemplate   = '<span class="bootstrap-timepicker-minute timepicker-value"></span>';
      secondTemplate   = '<span class="bootstrap-timepicker-second timepicker-value"></span>';
      meridianTemplate = '<span class="bootstrap-timepicker-meridian timepicker-value"></span>';
    }

    let templateContent = `
<table class="table">
<tr>
  <td><a href="#" data-action="incrementHour" class="timepicker-increment">+</a></td>
  <td class="separator">&nbsp;</td>
  <td><a href="#" data-action="incrementMinute" class="timepicker-increment">+</a></td>
  ${this.showSeconds ? '<td class="separator">&nbsp;</td><td><a href="#" data-action="incrementSecond" class="timepicker-increment">+</a></td>' : ''}
  ${this.showMeridian ? '<td class="separator">&nbsp;</td><td class="meridian-column"><a href="#" data-action="toggleMeridian" class="timepicker-increment">+</a></td>' : ''}
</tr>
<tr>
  <td>${hourTemplate}</td>
  <td class="separator">:</td>
  <td>${minuteTemplate}</td>
  ${this.showSeconds ? `<td class="separator">:</td><td>${secondTemplate}</td>` : ''}
  ${this.showMeridian ? `<td class="separator">&nbsp;</td><td>${meridianTemplate}</td>` : ''}
</tr>
<tr>
  <td><a href="#" data-action="decrementHour" class="timepicker-decrement">-</a></td>
  <td class="separator"></td>
  <td><a href="#" data-action="decrementMinute" class="timepicker-decrement">-</a></td>
  ${this.showSeconds ? '<td class="separator">&nbsp;</td><td><a href="#" data-action="decrementSecond" class="timepicker-decrement">-</a></td>' : ''}
  ${this.showMeridian ? '<td class="separator">&nbsp;</td><td><a href="#" data-action="toggleMeridian" class="timepicker-decrement">-</a></td>' : ''}
</tr>
</table>`;

    if (this.template !== 'modal') {
      return `<div class="bootstrap-timepicker-widget dropdown-menu">${templateContent}</div>`;
    }

    return `
<div class="bootstrap-timepicker-widget modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog modal-sm" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
      <h4 class="modal-title">Pick a Time</h4>
    </div>
    <div class="modal-body">
      ${templateContent}
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>
  </div>
</div>
</div>`;
  };
})(jQuery);
