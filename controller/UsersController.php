<?php

require './model/User.php';
require './Components/ApiComponent.php';

class UsersController {

    public function add () {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $save = array(
                'nome' => $_POST['nome'],
                'email' => $_POST['email'],
                'senha' => md5($_POST['senha']),
            );
            
            $user = new User();

            if ($user->save('usuario', $save)) {
                return ApiComponent::jsonResponse(array(
                    'status' => 200,
                    'msg' => 'User saved with success',
                ), 200);
            }

            return ApiComponent::jsonResponse(array(
                'status' => 500,
                'msg' => 'Error on save user',
            ), 500);
        }
        
        return ApiComponent::jsonResponse(array(
            'status' => 400,
            'msg' => 'Only Post method allowed',
        ), 400);
    }

    public function edit () {
        if ($_SERVER['REQUEST_METHOD'] === "POST") {
            $save = array(
                'id' => $_POST['id'],
                'nome' => $_POST['nome'],
                'email' => $_POST['email'],
                'senha' => md5($_POST['senha']),
            );
            
            $user = new User();

            if ($user->update('usuario', $save['id'], $save)) {
                return ApiComponent::jsonResponse(array(
                    'status' => 200,
                    'msg' => 'User edited with success',
                ), 200);
            }

            return ApiComponent::jsonResponse(array(
                'status' => 500,
                'msg' => 'Error on edit user',
            ), 500);
        }
        
        return ApiComponent::jsonResponse(array(
            'status' => 400,
            'msg' => 'Only Post method allowed',
        ), 400);
    }
}