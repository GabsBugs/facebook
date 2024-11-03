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

// Inclui funções para sanitizar IDs e texto
require_once "../../functions/sanitize_id.php";
require_once "../../functions/sanitize_text.php";

// Verifica se o parâmetro "comment_id" foi enviado na requisição POST
if(!isset($_POST["comment_id"])) {
    // Se não estiver presente, retorna uma mensagem JSON de erro
    echo json_encode(array(
        "message"=>"comment id required !", // Mensagem de erro
        "success"=>false // Indica que a operação falhou
    ));
}

// Verifica se o parâmetro "new_comment" foi enviado na requisição POST
if(!isset($_POST["new_comment"])) {
    // Se não estiver presente, retorna uma mensagem JSON de erro
    echo json_encode(array(
        "message"=>"new comment required !", // Mensagem de erro
        "success"=>false // Indica que a operação falhou
    ));
}

// Sanitiza o ID do comentário recebido
$comment_id = sanitize_id($_POST["comment_id"]);
// Sanitiza o novo comentário recebido
$new_comment = sanitize_text($_POST["new_comment"]);

// Cria uma nova instância da classe Comment
$comment = new Comment();

// Busca o comentário existente pelo ID
$comment->fetch_comment($comment_id);

// Define a propriedade "comment_text" da instância de Comment com o novo texto do comentário
$comment->set_property("comment_text", $new_comment);

// Chama o método update para tentar atualizar o comentário
if($comment->update()) {
    // Se a atualização for bem-sucedida, retorna o novo texto do comentário
    echo $new_comment;
} else {
    // Se a atualização falhar, retorna -1
    echo -1;
}
