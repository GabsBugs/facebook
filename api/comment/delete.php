<?php

// Inclui o autoload do Composer, que carrega automaticamente as classes necessárias
require_once "../../vendor/autoload.php";

// Inclui o arquivo de inicialização do core da aplicação
require_once "../../core/rest_init.php";

// Importa a classe Comment do namespace models
use models\{Comment};

// Configura os cabeçalhos HTTP para permitir o acesso CORS
header("Access-Control-Allow-Origin: *"); // Permite requisições de qualquer origem
header("Content-Type: text/html"); // Define o tipo de conteúdo como HTML
header("Access-Control-Allow-Methods: POST"); // Permite apenas o método POST
header("Access-Control-Max-Age: 3600"); // Define o tempo máximo de cache das opções prévias
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); // Permite cabeçalhos específicos

// Inclui a função para sanitizar IDs
require_once "../../functions/sanitize_id.php";

// Verifica se o parâmetro "comment_id" foi enviado na requisição POST
if(!isset($_POST["comment_id"])) {
    // Se não estiver presente, retorna uma mensagem JSON de erro
    echo json_encode(array(
        "message"=>"comment id required !", // Mensagem de erro
        "success"=>false // Indica que a operação falhou
    ));
}

// Sanitiza o ID do comentário recebido
$post_id = sanitize_id($_POST["comment_id"]);

// Cria uma nova instância da classe Comment
$comment = new Comment();

// Define a propriedade "id" da instância de Comment com o ID sanitizado
$comment->set_property("id", $post_id);

// Chama o método delete para tentar excluir o comentário
if($comment->delete()) {
    // Se a exclusão for bem-sucedida, retorna 1
    echo 1;
} else {
    // Se a exclusão falhar, retorna -1
    echo -1;
}
