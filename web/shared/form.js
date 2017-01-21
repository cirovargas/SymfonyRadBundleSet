function addFormFieldError(field,message){
    if(!field.closest('.form-group').hasClass('has-error')){
        field.closest('.form-group').addClass('has-error').append(
            '<span class="help-block">'+message+' <br></span>'
        );
    }
}

function removeFormFieldError(field){
    field.closest('.form-group').removeClass('has-error').find('.help-block').remove();
}
$(document).ready(function(){
    $('input,textarea,select').on('blur',function(){
        removeFormFieldError($(this));
    });


    $('.money').autoNumeric('init',{
        aSep: '',
        aDec: ','
    });



});
