<?php

require_once '../vendor/autoload.php';

// api/users/1
if($_GET['url']){

$url = explode('/', $_GET['url']);

    //Verificar se na url contem "api".
    if($url[0] === 'api') {

        array_shift($url);

        // Variável que armazena o serviço.
        $service = 'App\Services\\'.ucfirst($url[0].'Service');
        array_shift($url);

        // Verifica o método que está sendo usado.
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        try {
            // Função que chama o serviço de acordo com o serviço instanciado na url. O primeiro parâmetro cria um novo serviço, o segundo aponta o método que está sendo chamado, e o terceiro informa os parâmetros da url.
            $response = call_user_func_array(array(new $service, $method), $url);
            http_response_code(200);
            // Retorna o resultado.
            exit( json_encode(['status'=> "sucess", 'message'=> $response]));

        } catch (\Exception $e) {
            http_response_code(500);
            // Retorna o resultado.
            echo json_encode(array('status'=> 'error', 'data' => $e->getMessage()), JSON_UNESCAPED_UNICODE);
        }
    }

}
