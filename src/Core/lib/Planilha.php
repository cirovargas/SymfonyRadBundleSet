<?php

namespace Core\lib;

class Planilha {
    
    
    public static function decimal($valor){
        
        $valor = preg_replace("/[^0-9,\.]/", "", $valor);
        
        if(trim($valor) == '')
            return 0;
        
        preg_match("/([0-9,\.]*?)([,|\.][0-9][0-9])$/", $valor, $partes);
        
        if(count($partes) != 3 && (count($partes) ==2 && $partes[0] != $partes[1])){
            throw new Exception('Decimal inválido');
        }
        
        $partes[1] = preg_replace("/[^0-9]/", "", $partes[1]);
        
        return isset($partes[2])?floatval($partes[1].str_replace(',','.',$partes[2])): floatval($partes[1]);
    }
}
