<?php
    namespace App\Services;

    use App\Models\User;

    class UserService
    {
        public function get($id = null)
        {
            if($id) {
                return User::select($id);
            } else {
                return User::selectAll();
            }
            return $_GET;
        }
        public function post()
        {
            $data = $_POST;
            return User::insert($data);
        }
    }