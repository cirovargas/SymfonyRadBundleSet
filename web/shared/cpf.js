function validCPF (cpf) {
    cpf = cpf.replace(/[^\d]+/g,'');    
    if(cpf == '') return false;

    if (cpf.length != 11 || 
        cpf == "00000000000" || 
        cpf == "11111111111" || 
        cpf == "22222222222" || 
        cpf == "33333333333" || 
        cpf == "44444444444" || 
        cpf == "55555555555" || 
        cpf == "66666666666" || 
        cpf == "77777777777" || 
        cpf == "88888888888" || 
        cpf == "99999999999")
            return false;

    add = 0;    
    for (i=0; i < 9; i ++)       
        add += parseInt(cpf.charAt(i)) * (10 - i);  
        rev = 11 - (add % 11);  
        if (rev == 10 || rev == 11)     
            rev = 0;    
        if (rev != parseInt(cpf.charAt(9)))     
            return false;

    add = 0;    
    for (i = 0; i < 10; i ++)        
        add += parseInt(cpf.charAt(i)) * (11 - i);  
    rev = 11 - (add % 11);  
    if (rev == 10 || rev == 11) 
        rev = 0;    
    if (rev != parseInt(cpf.charAt(10)))
        return false;
    return true;   
}

function consultarBipBop(cpf,nascimento,campo){
    return;
    $.bipbop("SELECT FROM 'BIPBOPJS'.'CPFCNPJ'", null, {
        data: {
            documento: cpf,
            nascimento: nascimento
        },
        success: function(data) {
            var nome = $(data).find("body nome").text();
            var exception = $(data).find("header exception").text();

            if (exception) {
              //exception = exception.replace(/, t/, '. T');
              //$('#status').text(exception);
            } else {
                campo.val(nome);
            }
        }
    });
}

function validaNomeCPF(){
    var cpf = $("#manifestacao_manifestacaoPessoaFisica_cpf").val(),
        nascimento = $("#manifestacao_manifestacaoPessoaFisica_nascimento").val(),
        campo = $("#manifestacao_manifestacaoPessoaFisica_nomeVerificado");

    if(
        $('#manifestacao_natureza').val() == 1 &&
        validCPF(cpf) && 
        /^\d{2}\/\d{2}\/\d{4}$/.test(nascimento)
    ) {
            console.log('oi2');
        $.ajax({
            url: "http://irql.bipbop.com.br",
            method: "GET",
            crossDomain: true,
            data: { 
                apiKey	:'6057b71263c21e4ada266c9d4d4da613',
                documento	: cpf,
                nascimento	: nascimento,
                q:	"SELECT FROM 'BIPBOPJS'.'CPFCNPJ'"
            },
            dataType: "XML"
        }).done(function( msg ) {
            if($(msg).find('BPQL > body > nome').length > 0){
                campo.val($(msg).find('BPQL > body > nome').text());
            } else if($(msg).find("header exception").length <= 0) {
                consultarBipBop(cpf,nascimento,campo);
            }
        }).fail(function( jqXHR, textStatus ) {
            console.log('oi');
            consultarBipBop(cpf,nascimento,campo);
        });
    }
}