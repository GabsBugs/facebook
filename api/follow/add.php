<?php

// Inclui o autoload do Composer, que carrega automaticamente as classes necessárias
require_once "../../vendor/autoload.php";

// Inclui o arquivo de inicialização do core da aplicação
require_once "../../core/rest_init.php";

// Importa as classes necessárias do namespace
use models\{User, Follow}; // Classes User e Follow do namespace models

// Configura os cabeçalhos HTTP para permitir o acesso CORS
header("Access-Control-Allow-Origin: *"); // Permite requisições de qualquer origem
header("Content-Type: application/json"); // Define o tipo de conteúdo como JSON
header("Access-Control-Allow-Methods: POST"); // Permite apenas o método POST
header("Access-Control-Max-Age: 3600"); // Define o tempo máximo de cache das opções prévias
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); // Permite cabeçalhos específicos

// Inclui a função para sanitizar IDs
require_once "../../functions/sanitize_id.php";

// Verifica se o parâmetro "current_user_id" foi enviado na requisição POST
if(!isset($_POST["current_user_id"])) {
    // Se não estiver presente, retorna uma mensagem JSON de erro
    echo json_encode(
        array(
            "message"=>"You should provide current_user_id as post form input", // Mensagem de erro
            "success"=>false // Indica que a operação falhou
        )
    );

    exit(); // Termina a execução do script
}

// Verifica se o parâmetro "current_profile_id" foi enviado na requisição POST
if(!isset($_POST["current_profile_id"])) {
    // Se não estiver presente, retorna uma mensagem JSON de erro
    echo json_encode(
        array(
            "message"=>"You should provide followed_id as post form input", // Mensagem de erro
            "success"=>false // Indica que a operação falhou
        )
    );

    exit(); // Termina a execução do script
}

// Armazena os IDs do seguidor e do seguido a partir dos dados da requisição
$follower = $_POST["current_user_id"];
$followed = $_POST["current_profile_id"];

/*
    Aqui não podemos permitir que um usuário siga a si mesmo, pois criamos uma restrição UNIQUE (follower_id, followed_id) no banco de dados.
    Se quiser permitir que o usuário se siga, remova a restrição e também remova este bloco de condição.
*/
if($follower === $followed) {
    // Se o seguidor e o seguido forem o mesmo, retorna uma mensagem de erro
    echo json_encode(
        array(
            "message"=>"You can't follow yourself", // Mensagem de erro
            "success"=>false // Indica que a operação falhou
        )
    );

    exit(); // Termina a execução do script
}

// Verifica se o ID do seguidor é válido, numérico e se existe no banco de dados
if(($follower = sanitize_id($follower)) && 
    User::user_exists("id", $follower)) {
        // Verifica se o ID do seguido está definido, é numérico e existe no banco de dados
        if(isset($followed) && 
            ($followed = sanitize_id($followed)) && 
            User::user_exists("id", $followed)) {
                
                // Verifica se já existe uma relação de seguimento entre os usuários
                if(Follow::follow_exists($follower, $followed)) {
                    // Se o seguidor já segue o usuário, retorna uma mensagem de erro
                    echo json_encode(
                        array(
                            "message"=>"The follower user is already following the followed user", // Mensagem de erro
                            "success"=>false // Indica que a operação falhou
                        )
                    );
                } else {
                    // Agora sabemos que o ID do seguidor e do seguido são válidos, então podemos adicioná-los ao banco de dados
                    $follow = new Follow(); // Cria uma nova instância da classe Follow
                    $follow->set_data(array( // Define os dados do seguimento
                        "follower"=>$follower,
                        "followed"=>$followed
                    ));
                    $follow->add(); // Adiciona a relação de seguimento ao banco de dados

                    // Retorna uma mensagem de sucesso
                    echo json_encode(
                        array(
                            "message"=>"user with id: " . $follower . " followed user with id: " . $followed . " successfully !", // Mensagem de sucesso
                            "success"=>true // Indica que a operação foi bem-sucedida
                        )
                    );
                }
        } else {
            // Se o ID do seguido não for válido ou não existir, retorna uma mensagem de erro
            echo json_encode(
                array(
                    "message"=>"followed id is either not valid or not exists in our db", // Mensagem de erro
                    "success"=>false // Indica que a operação falhou
                )
            );
        }
} else {
    // Se o ID do seguidor não for válido ou não existir, retorna uma mensagem de erro
    echo json_encode(
        array(
            "message"=>"follower id is either not valid or not exists in our db", // Mensagem de erro
            "success"=>false // Indica que a operação falhou
        )
    );
}
