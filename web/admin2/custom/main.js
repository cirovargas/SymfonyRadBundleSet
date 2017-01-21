
var COLORS = [
    '#0288D1',
    '#FF4081',
    '#4CAF50',
    '#D32F2F',
    '#FFC107',
    '#673AB7',
    '#FF5722',
    '#CDDC39',
    '#795548',
    '#607D8B',
    '#009688',
    '#E91E63',
    '#9E9E9E',
    '#E040FB',
    '#00BCD4'
];

$(document).ready(function(){

    $('#extranet-nav').pxNav();
    $('#navbar-notifications').perfectScrollbar();
    $('#navbar-messages').perfectScrollbar();


    $('#delete-buttom').click(function(){
        swal({
            title: "Você tem certeza que quer excluir o registro?",
            text: "Não será possível recuperar o registro posteriormente!",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Sim, excluir isto!",
            closeOnConfirm: false
        }, function(){
            $('#delete-form').submit();
            swal("Excluído!", "Registro deletado com sucesso.", "success");
        });
    });

    $('.multiselect').multiSelect();

    //$('.datetimepicker').datetimepicker({
    //    locale: 'pt-br'
    //});
    
    
    $('.char-limit').pxCharLimit();

    $('.datepicker').datepicker({
        format: "dd/mm/yyyy",
        language: "pt-BR"
    });

    $("time.timeago").timeago();

    $('.select2').select2({
        language: 'pt-BR'
    });

    $('#input-daterange').datepicker();

    $('.modal-manifestacao-anterior').click(function(event){
        event.preventDefault();
        $('#modal-manifestacoes-anteriores').find('.modal-content').html('').append(
            '<iframe src="'+$(this).attr('href')+'" style="width: 100%;min-height: 500px"></iframe>'
        );
        $('#modal-manifestacoes-anteriores').modal('show');
    });

    $('.px-file').pxFile();

    $('.summernote').each(function(){
        var $this = $(this);
        $this.summernote({
            height: 200,
            tabsize: 2,
            lang: 'pt-BR',
            toolbar: [
                ['style', ['style']],
                ['font', ['bold',  'italic','underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen','codeview']]
            ],
            codemirror: {
                theme: 'monokai'
            },
            callbacks: {
                onPaste: function(e) {
                    var updatePastedText = function(someNote){
                        var original = $this.summernote("code");
                        var cleaned = CleanPastedHTML(original);
                        $this.summernote("code",cleaned);
                    };
                    setTimeout(function () {
                        updatePastedText($this);
                    }, 10);

//                    setTimeout(function(){
//                        $this.summernote(
//                            "code", 
//                            $($this.summernote("code").replace(/<\/?div[^>]*?>/gi, '').replace(/<script[^>]*?>[\s\S]*?<\/script>/gi,''))
//                        );
//                    },10);


                }
            }
        });
    });

    tinymce.init({
        selector: '.tinymce',
        height: 200,
        theme: 'modern',
        menubar: 'edit insert view format table tools',
        language: 'pt_BR',
        browser_spellcheck: true,
        fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt',
        nonbreaking_force_tab: true,
        font_formats: 'Andale Mono=andale mono,times;'+ 'Arial=arial,helvetica,sans-serif;'+ 'Arial Black=arial black,avant garde;'+ 'Book Antiqua=book antiqua,palatino;'+ 'Comic Sans MS=comic sans ms,sans-serif;'+ 'Courier New=courier new,courier;'+ 'Georgia=georgia,palatino;'+ 'Helvetica=helvetica;'+ 'Impact=impact,chicago;'+ 'Symbol=symbol;'+ 'Tahoma=tahoma,arial,helvetica,sans-serif;'+ 'Terminal=terminal,monaco;'+ 'Times New Roman=times new roman,times;'+ 'Trebuchet MS=trebuchet ms,geneva;'+ 'Verdana=verdana,geneva;'+ 'Webdings=webdings;'+ 'Wingdings=wingdings,zapf dingbats',
        plugins: [
            'advlist autolink powerpaste lists link image charmap print preview hr anchor pagebreak',
            'searchreplace wordcount visualblocks visualchars code fullscreen',
            'insertdatetime media nonbreaking save table contextmenu directionality',
            'emoticons template textcolor colorpicker textpattern imagetools'
        ],
        toolbar1: ' undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
        toolbar2: ' preview media | forecolor backcolor emoticons | fontselect fontsizeselect',
        image_advtab: true,
        templates: [
            { title: 'Test template 1', content: 'Test 1' },
            { title: 'Test template 2', content: 'Test 2' }
        ],
        content_css: [
            '//fonts.googleapis.com/css?family=Lato:300,300i,400,400i',
            '//www.tinymce.com/css/codepen.min.css'
        ]
    });


    for (var i = 0, len = window.pxInit.length; i < len; i++) {
        window.pxInit[i].call(null);
    }

});

function salvarRascunhoManifestacao(options){

    if(options.encaminhamento == undefined && options.manifestacao == undefined){
        console.log('manifestação não identificada');
        return;
    }

    if(options.mensagem == ''){
        return;
    }


    $.ajax({
        method: "POST",
        url: Routing.generate('manifestacao_salvar_rascunho'),
        data: options
    });
}

