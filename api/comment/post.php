<?php

// Inclui o autoload do Composer, que carrega automaticamente as classes necessárias
require_once "../../vendor/autoload.php";

// Inclui o arquivo de inicialização do core da aplicação
require_once "../../core/rest_init.php";

// Importa as classes necessárias do namespace
use classes\DB; // Classe para interações com o banco de dados
use models\{User, Comment}; // Classes User e Comment do namespace models
use layouts\post\Post as Post_Manager; // Classe Post, renomeada como Post_Manager para uso

// Configura os cabeçalhos HTTP para permitir o acesso CORS
header("Access-Control-Allow-Origin: *"); // Permite requisições de qualquer origem
header("Content-Type: text/html"); // Define o tipo de conteúdo como HTML
header("Access-Control-Allow-Methods: POST"); // Permite apenas o método POST
header("Access-Control-Max-Age: 3600"); // Define o tempo máximo de cache das opções prévias
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With"); // Permite cabeçalhos específicos

// Inclui funções para sanitizar IDs e texto
require_once "../../functions/sanitize_id.php";
require_once "../../functions/sanitize_text.php";

// Verifica se o parâmetro "post_id" foi enviado na requisição POST
if(!isset($_POST["post_id"])) {
    // Se não estiver presente, retorna uma mensagem JSON de erro
    echo json_encode(array(
        "message"=>"post id required !", // Mensagem de erro
        "success"=>false // Indica que a operação falhou
    ));
}

// Verifica se o parâmetro "current_user_id" foi enviado na requisição POST
if(!isset($_POST["current_user_id"])) {
    // Se não estiver presente, retorna uma mensagem JSON de erro
    echo json_encode(array(
        "message"=>"Current user id required !", // Mensagem de erro
        "success"=>false // Indica que a operação falhou
    ));
}

// Verifica se o parâmetro "comment_owner" foi enviado na requisição POST
if(!isset($_POST["comment_owner"])) {
    // Se não estiver presente, retorna uma mensagem JSON de erro
    echo json_encode(array(
        "message"=>"comment owner required !", // Mensagem de erro
        "success"=>false // Indica que a operação falhou
    ));
}

// Verifica se o parâmetro "comment_owner" está vazio
if(!isset($_POST["comment_owner"]) || empty($_POST["comment_owner"])) {
    // Se estiver vazio, retorna uma mensagem JSON de erro
    echo json_encode(array(
        "message"=>"comment should not be empty or unset !", // Mensagem de erro
        "success"=>false // Indica que a operação falhou
    ));
}

// Sanitiza os parâmetros recebidos
$comment_owner = sanitize_id($_POST["comment_owner"]); // Sanitiza o ID do proprietário do comentário
$post_id = sanitize_id($_POST["post_id"]); // Sanitiza o ID do post
$comment_text = sanitize_text($_POST["comment_text"]); // Sanitiza o texto do comentário
$current_user_id = sanitize_id($_POST["current_user_id"]); // Sanitiza o ID do usuário atual

// Cria uma nova instância da classe Comment
$comment = new Comment();
// Define os dados do comentário usando um array
$comment->setData(array(
    "comment_owner"=>$comment_owner,
    "post_id"=>$post_id,
    "comment_text"=>$comment_text
));

// Adiciona o comentário ao banco de dados
$comment = $comment->add();

// Neste ponto, não sabemos o ID do comentário adicionado, então buscamos no banco de dados
$captured_id = DB::getInstance()->query("SELECT id FROM comment WHERE comment_owner = ? AND comment_date = ?", array(
    "comment_owner"=>$comment->get_property("comment_owner"), // Captura o ID do proprietário do comentário
    "comment_date"=>$comment->get_property("comment_date") // Captura a data do comentário
))->results()[0]->id; // Obtém o ID do primeiro resultado da consulta

// Define o ID do comentário na instância do comentário
$comment->set_property("id", $captured_id);

// Gera o componente do comentário para o post usando o Post_Manager
$post_manager = Post_Manager::generate_comment($comment, $current_user_id);

// Retorna o componente do comentário gerado
echo $post_manager;

