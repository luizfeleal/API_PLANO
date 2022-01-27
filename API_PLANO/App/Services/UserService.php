<?php
    namespace App\Services;

    use App\Models\User;

    class UserService
    {
        public function post()
        {
            $data = $_POST;
            //Retorna o valor da execução da função chamada, localizada na class user.
            return User::insert($data);
        }
    }
