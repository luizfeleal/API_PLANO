<?php

    namespace App\Models;

    class User
    {
        public static function select($id) 
        {
            $json = file_get_contents("../beneficiarios.json");
            $dataJ = json_decode($json);

           
            for($data = 0; $data < count($dataJ); $data++){

                if($dataJ[$data]->beneficiarios[0]->nome == $id){
                    return $dataJ[$data];
                } 
                if($dataJ[$data]->beneficiarios[0]->nome != $id ){
                    throw new \Exception('Nenhum usuário encontrado!');
                }
            }
        }

        public static function insert($data)
        {
            $pricesJson =  file_get_contents("../prices.json");
            $prices = json_decode($pricesJson);

            $plansJson =  file_get_contents("../plans.json");
            $plans = json_decode($plansJson);


            $arquivo = file_get_contents('php://input');

            $req = json_decode($arquivo);

            //Verifica se a opção de registro veio vazia, retorna um erro.
            if(empty($req->registro)){
                throw new \Exception('Nenhum registro encontrado!');
                http_response_code(404);
            }

            for($p = 0; $p < count($plans); $p++){
                if($req->registro === $plans[$p]->registro){
                        $codigo = $plans[$p]->codigo;
                }
            }
           
            

            //Instanciamento do array para inserção do cliente nos arquivos json.
    
            $input =  [
                    'registro' => $req->registro,
                    'qtd_beneficiarios' => $req->qtd_beneficiarios,
                    'beneficiarios' => 
                    array (
                        
                    )
            ];

            //Verifica a quantidade de beneficiários, especificado pelo cliente, e insere os dados no array, para ser inserido no arquivo json.
            if($req->qtd_beneficiarios > 1){
                for($b = 0; $b < $req->qtd_beneficiarios; $b++){
                    $beneficiarioAdicional = array (
                        "nome" =>  $req->beneficiarios[$b]->nome,
                        'idade' => $req->beneficiarios[$b]->idade,
                    );
                    array_push($input["beneficiarios"], $beneficiarioAdicional);
                }
                //var_dump($input["beneficiarios"]);
            } else if($req->qtd_beneficiarios = 1){
                $beneficiario = array (
                    "nome" =>  $req->beneficiarios[0]->nome,
                    'idade' => $req->beneficiarios[0]->idade
                );
                array_push($input["beneficiarios"], $beneficiario);
            }
            
            
            // Variável que determina valor do plano, na qual inicia em 0 e é modificado de acordo com os dados recebidos.
            $total = 0;

            // Array que acrescenta o valor individual dos beneficiário.
            $valorIndividual = array();

            // Array que armazena os planos que possuem o código escolhido pelo beneficiário.
            $totalC = array();
            
            // Cria um loop para percorrer o arquivo prices.json
            for($i = 0; $i < count($prices); $i++){


                if($prices[$i]->codigo == $codigo){
                    
                    if(intval($req->qtd_beneficiarios >= $prices[$i]->minimo_vidas) || intval($req->qtd_beneficiarios) < $prices[($i + 1)]->minimo_vidas){
                        
                        // Adiciona ao array o plano com o código, escolhido que obedece as condições acima
                        array_push($totalC, $prices[$i]);
                    }
                } 
            }
                   
                    // Verifica se o número de elementos do array é igual ou maior que um. Caso seja maior que um, significa que o usuário pode ter preços variados,
                    // e a partir desse array, pega o preço correto de acordo com o minimo de vidas.
                    if(count($totalC) > 1){
                        $i = count($totalC) - 1;
                    }else if(count($totalC) == 1){
                        $i = count($totalC) - 1;
                    }
                    
                    // Cria loop para percorrer os beneficiários.
                    for($z = 0; $z < intval($req->qtd_beneficiarios); $z++){ 

                        // Cria as condições de idade, atribuindo o valor a variável "total".

                        if( intval($req->beneficiarios[$z]->idade) >= 0 && intval($req->beneficiarios[$z]->idade) <= 17){

                            $total += $totalC[$i]->faixa1; //$prices[i];

                        $valor = array (
                            "idade" =>  $req->beneficiarios[$z]->idade,
                            "valor" => $totalC[$i]->faixa1,
                        );
                        array_push($valorIndividual, $valor);
                    }
                    if(intval($req->beneficiarios[$z]->idade) >= 18 && intval($req->beneficiarios[$z]->idade) <= 40) {
                        
                        $total += $totalC[$i]->faixa2;
                        
                        $valor = array (
                            "idade" =>  $req->beneficiarios[$z]->idade,
                            "valor" => $prices[$i]->faixa2
                        );
                        array_push($valorIndividual, $valor);
                    } 
                    if( intval($req->beneficiarios[$z]->idade) > 40){
                        
                        $total += $totalC[$i]->faixa3;
                        
                        $valor = array (
                            "idade" =>  $req->beneficiarios[$z]->idade,
                            "valor" => $totalC[$i]->faixa3
                        );
                        array_push($valorIndividual, $valor);
                    }
                }


                $valor = array (
                    "valorTotal" => $total
                );
                // Adiciona o valor ao array criado mais acima.
                array_push($valorIndividual, $valor);
               
                // Lê o arquivo, caso exista.

                $arrayJson = json_encode($input);

                $beneficiarios = @fopen('../beneficiarios.json','r+');
                
                // Caso não exista, cria um arquivo.
                if($beneficiarios === null){
                    $beneficiarios = fopen($filename, 'w+');
                }
                
                // Inicia o processo de escrita no arquivo beneficiarios.json.
                if($beneficiarios){
        
                    fseek($beneficiarios, 0, SEEK_END);
        
                    if(ftell($beneficiarios) > 2){
        
                        fseek($beneficiarios, -1, SEEK_END);
        
                        fwrite($beneficiarios, ',', 1);
            
                        fwrite($beneficiarios, $arrayJson . ']');
                    }else {
                        fseek($beneficiarios, -1, SEEK_END);
        
                        fwrite($beneficiarios, $arrayJson . ']');
                    }
        
                    fclose($beneficiarios);
                }

                //Adiciona o valor da soma do plano de cada beneficiário e adiciona ao array.
                $totalPlanos = array (
                    "valorTotal" =>$total
                );

                array_push($input['beneficiarios'], $totalPlanos);
                

                $json = json_encode($input);
                
                $proposta = @fopen('../proposta.json','r+');
                
                // Caso não exista, cria um arquivo.
                if($proposta === null){
                    $proposta = fopen($filename, 'w+');
                }


                // Inicia o processo de escrita no arquivo proposta.json.
                if($proposta){
        
                    fseek($proposta, 0, SEEK_END);
        
                    if(ftell($proposta) > 2){
        
                        fseek($proposta, -1, SEEK_END);
        
                        fwrite($proposta, ',', 1);
            
                        fwrite($proposta, $json . ']');
                    }else {
                        fseek($proposta, -1, SEEK_END);
        
                        fwrite($proposta, $json . ']');
                    }
        
                    fclose($proposta);
                }

                //Retorna o array contendo a idade do beneficiário, o valor respectivo a idade e a soma do preço de cada beneficiário.
                return $valorIndividual;
            }
    }