<?php

    namespace App\Models;
    use App\Src\Write;

    class User
    {
        public static function select($id) 
        {
                    throw new \Exception('Método GET não disponível');
        }

        public static function insert($data)
        {

            $prices = json_decode(file_get_contents("../prices.json"));

            $plans = json_decode(file_get_contents("../plans.json"));

            $rangesAges = json_decode(file_get_contents("../rangesAges.json"));
            
            $req = json_decode(file_get_contents('php://input'));

            $codigoArray = false;

            // Cria loop para percorrer os planos e verificar se há um código correspondente ao código inserido pelo cliente.
            for($p = 0; $p < count($plans); $p++){

                if($req->registro === $plans[$p]->registro){
                        $codigo = $plans[$p]->codigo;
                        // Caso tenha um código correspondente, troca o valor da variável para true.
                        $codigoArray = true;
                }
            }

            if($codigoArray == false){
                throw new \Exception('Nenhum registro encontrado!');
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

                // Array que armazena os códigos que obedecem as condiçoes proposta.
                $totalT = array();

                // Array que armazena os planos que possuem o código escolhido pelo beneficiário.
                $totalC = array();

            
                // Cria um loop para percorrer o arquivo prices.json
                for($i = 0; $i < count($prices); $i++){

                    // Verifica se o código inserido corresponde ao código do preço de algum plano existente.
                    if($prices[$i]->codigo == $codigo){
                    
                        //Adiciona a um array todos os preços de planos que possuem o código igual.
                        array_push($totalT, $prices[$i]);
                    } 
                }

                // Cria um loop para percorre o array criado com os preços que possuem o mesmo código 
                for($d = 0; $d < count($totalT); $d++){
                    if(intval($req->qtd_beneficiarios >= $totalT[$d]->minimo_vidas && $req->qtd_beneficiarios < $totalT[(count($totalT) -1)]->minimo_vidas)){
                        // Adiciona ao array as informações do preço do plano que obedece as condições acima.
                        array_push($totalC, $totalT[$d]);
                    }
                    if( $req->qtd_beneficiarios >= $totalT[(count($totalT) - 1)]->minimo_vidas){

                    array_push($totalC, $totalT[(count($totalT) - 1)]);
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

                        if( intval($req->beneficiarios[$z]->idade) >= $rangesAges[0]->range1->min && intval($req->beneficiarios[$z]->idade) <= $rangesAges[0]->range1->max){

                            $total += $totalC[$i]->faixa1;
                            
                            $valor = array (
                                "idade" =>  $req->beneficiarios[$z]->idade,
                                "valor" => $totalC[$i]->faixa1,
                            );
                            array_push($valorIndividual, $valor);
                        }
                        if(intval($req->beneficiarios[$z]->idade) >= $rangesAges[0]->range2->min && intval($req->beneficiarios[$z]->idade) <= $rangesAges[0]->range2->max) {
                            $total += $totalC[$i]->faixa2;
                        
                            $valor = array (
                                "idade" =>  $req->beneficiarios[$z]->idade,
                                "valor" => $totalC[$i]->faixa2
                            );
                            array_push($valorIndividual, $valor);
                        
                        } 
                        if( intval($req->beneficiarios[$z]->idade) > $rangesAges[0]->range3->min){
                        
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

                Write::write($input, "../beneficiarios.json");

                //Adiciona o valor da soma do plano de cada beneficiário e adiciona ao array.
                $totalPlanos = array (
                    "valorTotal" =>$total
                );

                array_push($input['beneficiarios'], $totalPlanos);
                Write::write($input, "../proposta.json");

                //Retorna o array contendo a idade do beneficiário, o valor respectivo a idade e a soma do preço de cada beneficiário.
                return $valorIndividual;
            }
    }
