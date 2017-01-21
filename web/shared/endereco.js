/**
 * Created by ciro on 27/03/16.
 * http://apps.widenet.com.br/busca-cep/api/cep/71909-180.json
 * http://cep.republicavirtual.com.br/web_cep.php?cep=71909-180
 * http://api.postmon.com.br/v1/cep/71909-180
 * https://viacep.com.br/ws/71909-180/json
 */


var endereco = {
    buscar: function(cep,callback){
        
        if(cep.length === 9){
            var retorno = this.buscarWidenet(cep);
            retorno = !retorno ? this.buscarPostmon(cep) : retorno;
            retorno = !retorno ? this.buscarViaCep(cep) : retorno;
            callback(retorno);
        }
    },
    buscarWidenet: function(cep){
        return this.doRequest(
                'http://apps.widenet.com.br/busca-cep/api/cep/'+cep+'.json',
                'GET',
                'JSON'
            );
    },
    buscarRepublicaVirtual:function(cep){
        return this.doRequest(
                'http://cep.republicavirtual.com.br/web_cep.php',
                'GET',
                'XML',
                {cep:cep}
            );
    },
    buscarPostmon:function(cep){
        return this.doRequest(
                'http://api.postmon.com.br/v1/cep/'+cep,
                'GET',
                'JSON'
            );
    },
    buscarViaCep:function(cep){
        return this.doRequest(
                'https://viacep.com.br/ws/'+cep+'/json',
                'GET',
                'JSON'
            );
    },
    doRequest: function(url,method,dataType,data){
        var ajax = $.ajax({
                    url: url,
                    method: method,
                    data: data,
                    async: false,
                    dataType: dataType
                }).fail(function( jqXHR, textStatus ) {
                    return false;
                });


        if(ajax.status === 200){
            if(dataType === 'JSON')
                return ajax.responseJSON;
            if(dataType === 'XML')
                return ajax.responseXML;
            
            return ajax.responseText;
        }

        return false;
    }
};