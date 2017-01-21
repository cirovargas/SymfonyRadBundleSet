function maskInputs(){

    $(".mask-date").mask("99/99/9999");
    $(".mask-cpf").mask("999.999.999-99");
    $(".mask-cep").mask("99999-999");
    $(".mask-telefone").mask("(99) 9999-9999?9");
    $(".mask-cnpj").mask("99.999.999/9999-99");
    $(".mask-pad").mask("9999/9999");
    $(".mask-ramal").mask("9999");
}

$(document).ready(maskInputs);