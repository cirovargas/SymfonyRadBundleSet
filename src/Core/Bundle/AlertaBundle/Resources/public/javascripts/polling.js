$('#button-notifications').click(function(){

    var ids = [];

    $('#navbar-notifications > .notification').each(function(){
        if($(this).data('readed') == false || typeof $(this).data('readed') == 'undefined'){
            ids.push($(this).data('id'));
        }
    });
    if(ids.length > 0){
        $.ajax({
            method: "POST",
            url: Routing.generate('core_alert_set_readed'),
            dataType: "JSON",
            data: {notificationIds:ids},
            success: function(data){
                $('#count-notifications').html(data.count > 0 ? data.count : '');
                for(var i =0;i<data.ids.length;i++)
                    $('#main-navbar-notifications > .notification[data-id="'+data.ids[i]+'"]').attr('data-readed',true);
            }
        });
    }

});


(function poll(){
    setTimeout(function(){
        var lastId = $('#navbar-notifications > .notification').first().data('id');
        $.ajax({
            method: "GET",
            url: Routing.generate('core_alert_refresh', {lastId: typeof lastId == 'undefined'?0:lastId}),
            dataType: "JSON",
            success: function(data){
                $('#count-notifications').html(data.count > 0 ? data.count : '');
                if(data.messages.length > 0){
                    var append = '';
                    for(var i =0;i<data.messages.length;i++){
                        append += '\
                        <div class="widget-notifications-item notification" data-id="'+data.messages[i].id+'">\
                            <div class="widget-notifications-title text-'+data.messages[i].alert.level+'">'+data.messages[i].alert.sistema+'</div>\
                            <div class="widget-notifications-description">'+data.messages[i].alert.text+'</div>\
                            <div class="widget-notifications-date" title="'+data.messages[i].alert.data.date+'">'+data.messages[i].alert.data.date+'</div>\
                            <div class="widget-notifications-icon fa '+data.messages[i].alert.icon+' bg-'+data.messages[i].alert.level+'"></div>\
                        </div>';
                        $.growl({ title: data.messages[i].alert.sistema, message: data.messages[i].alert.texto, duration: 10000 });

                    }
                    //notificar('PROJETO','Existem novas notificações para você na intranet.');
                    $(append).prependTo('#navbar-notifications');
                }
                poll();
            }
        });
    }, 3000);
})();