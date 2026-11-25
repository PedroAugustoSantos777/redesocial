
<?php

require_once __DIR__ . "/../models/PublicacaoModel.php";

// CREATE - CRIAR POSTAGEM
function createPublicacao($con) {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (
        !is_array($dados) ||
        empty($dados["id_usuario"]) ||
        empty($dados["legenda"])
    ) {
        http_response_code(400);
        echo json_encode(["erro" => "Informe o usuário e a legenda"]);
        return;
    }

    $id = inserirPublicacao($con, $dados);

    http_response_code(201);

    echo json_encode([
        "mensagem" => "Publicação criada com sucesso!",
        "id_publicacao" => $id
    ]);
}

// READ - FEED
function readPublicacao($con) {
    echo json_encode(listarPublicacoes($con));
}

// UPDATE - ATUALIZAR POSTAGEM
function updatePublicacao($con) {
    $id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        echo json_encode(["erro" => "ID inválido"]);
        return;
    }

    $dados = json_decode(file_get_contents("php://input"), true);

    if (!is_array($dados) || empty($dados["legenda"])) {
        http_response_code(400);
        echo json_encode(["erro" => "Informe a legenda"]);
        return;
    }

    atualizarPublicacao($con, $id, $dados);

    echo json_encode([
        "mensagem" => "Publicação atualizada com sucesso!"
    ]);
}

// DELETE - EXCLUIR POSTAGEM
function deletePublicacao($con) {
    $id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        echo json_encode(["erro" => "ID inválido"]);
        return;
    }

    removerPublicacao($con, $id);

    echo json_encode([
        "mensagem" => "Publicação removida com sucesso!"
    ]);
}
