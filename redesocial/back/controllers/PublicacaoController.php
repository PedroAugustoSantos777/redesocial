
<?php

require_once __DIR__ . "/../models/PublicacaoModel.php";

// Receber dados
function receberDadosPublicacao() {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (!is_array($dados)) {
        http_response_code(400);
        echo json_encode(["erro" => "Envie um JSON válido"]);
        return null;
    }

    foreach (["id_usuario", "conteudo"] as $campo) {
        if (!isset($dados[$campo]) || trim((string)$dados[$campo]) === "") {
            http_response_code(400);
            echo json_encode(["erro" => "Campo $campo obrigatório"]);
            return null;
        }
    }

    return $dados;
}

// Receber ID
function receberIdPublicacao() {
    $id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id < 1) {
        http_response_code(400);
        echo json_encode(["erro" => "Informe um ID válido"]);
        return null;
    }

    return $id;
}

// CREATE
function createPublicacao($con) {
    $dados = receberDadosPublicacao();

    if ($dados === null) return;

    $id = inserirPublicacao($con, $dados);

    http_response_code(201);
    echo json_encode([
        "mensagem" => "Publicação criada com sucesso",
        "id" => $id
    ]);
}

// READ
function readPublicacao($con) {
    echo json_encode(listarPublicacoes($con));
}

// UPDATE
function updatePublicacao($con) {
    $id = receberIdPublicacao();
    if ($id === null) return;

    $dados = json_decode(file_get_contents("php://input"), true);

    if (!is_array($dados) || empty($dados["conteudo"])) {
        http_response_code(400);
        echo json_encode(["erro" => "Informe o conteúdo da publicação"]);
        return;
    }

    atualizarPublicacao($con, $id, $dados);

    echo json_encode(["mensagem" => "Publicação atualizada com sucesso"]);
}

// DELETE
function deletePublicacao($con) {
    $id = receberIdPublicacao();
    if ($id === null) return;

    removerPublicacao($con, $id);

    echo json_encode(["mensagem" => "Publicação removida com sucesso"]);
}
