<?php

namespace App\Src;

    class Write
    {


        public static function write($array, $file){

            $arrayJson = json_encode($array);

            $fileWrite = @fopen($file, 'r+');
            
            // Caso não exista, cria um arquivo.
            if($fileWrite === null){
                $fileWrite = fopen($filename, 'w+');
            }
            
            // Inicia o processo de escrita no arquivo beneficiarios.json.
            if($fileWrite){
    
                fseek($fileWrite, 0, SEEK_END);
    
                if(ftell($fileWrite) > 2){
    
                    fseek($fileWrite, -1, SEEK_END);
    
                    fwrite($fileWrite, ',', 1);
        
                    fwrite($fileWrite, $arrayJson . ']');
                }else {
                    fseek($fileWrite, -1, SEEK_END);
    
                    fwrite($fileWrite, $arrayJson . ']');
                }
    
                fclose($fileWrite);
            }
        }
    }