<?php

require_once '../vendor/autoload.php';

// api/users/1
if($_GET['url']){

$url = explode('/', $_GET['url']);

    //Verificar se na url contem "api".
    if($url[0] === 'api') {

        array_shift($url);

        //Instancia o servico
        $service = 'App\Services\\'.ucfirst($url[0].'Service');
        array_shift($url);

        // Verifica o método que está sendo usado.
        $method = strtolower($_SERVER['REQUEST_METHOD']);
        try {
            
            $response = call_user_func_array(array(new $service, $method), $url);
            http_response_code(200);
            exit( json_encode(['status'=> "sucess", 'message'=> $response]));

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(array('status'=> 'error', 'data' => $e->getMessage()), JSON_UNESCAPED_UNICODE);
        }
    }

}