
<?php

require_once __DIR__ . "/../models/CurtidaModel.php";

// RECEBER DADOS
function receberDadosCurtida() {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (
        !is_array($dados) ||
        !isset($dados["id_usuario"], $dados["id_publicacao"]) ||
        !filter_var($dados["id_usuario"], FILTER_VALIDATE_INT) ||
        !filter_var($dados["id_publicacao"], FILTER_VALIDATE_INT) ||
        $dados["id_usuario"] < 1 ||
        $dados["id_publicacao"] < 1
    ) {
        http_response_code(400);
        echo json_encode(["erro" => "Informe os IDs corretamente"]);
        return null;
    }

    return $dados;
}

// CREATE - CURTIR
function createCurtida($con) {
    $dados = receberDadosCurtida();

    if ($dados === null) return;

    $resultado = inserirCurtida($con, $dados);

    if ($resultado === false) {
        http_response_code(409);
        echo json_encode(["erro" => "Você já curtiu esta publicação"]);
        return;
    }

    http_response_code(201);

    echo json_encode([
        "mensagem" => "Publicação curtida com sucesso!"
    ]);
}

// READ - LISTAR
function readCurtida($con) {
    echo json_encode(listarCurtidas($con));
}

// DELETE - DESCURTIR
function deleteCurtida($con) {
    $dados = receberDadosCurtida();

    if ($dados === null) return;

    $quantidade = removerCurtida(
        $con,
        $dados["id_usuario"],
        $dados["id_publicacao"]
    );

    echo json_encode([
        "mensagem" => $quantidade > 0
            ? "Curtida removida com sucesso!"
            : "Curtida não encontrada"
    ]);
}
