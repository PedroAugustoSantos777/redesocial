
<?php

require_once __DIR__ . "/../models/CurtidaModel.php";

// Receber dados
function receberDadosCurtida() {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (!is_array($dados)) {
        http_response_code(400);
        echo json_encode(["erro" => "Envie um JSON válido"]);
        return null;
    }

    foreach (["id_usuario", "id_publicacao"] as $campo) {
        if (!isset($dados[$campo]) ||
            filter_var($dados[$campo], FILTER_VALIDATE_INT) === false ||
            $dados[$campo] < 1) {

            http_response_code(400);
            echo json_encode(["erro" => "Informe $campo corretamente"]);
            return null;
        }
    }

    return $dados;
}

// CREATE - Curtir
function createCurtida($con) {
    $dados = receberDadosCurtida();

    if ($dados === null) return;

    inserirCurtida($con, $dados);

    http_response_code(201);
    echo json_encode([
        "mensagem" => "Publicação curtida com sucesso!"
    ]);
}

// READ - Listar
function readCurtida($con) {
    echo json_encode(listarCurtidas($con));
}

// DELETE - Descurtir
function deleteCurtida($con) {
    $dados = receberDadosCurtida();

    if ($dados === null) return;

    removerCurtida(
        $con,
        $dados["id_usuario"],
        $dados["id_publicacao"]
    );

    echo json_encode([
        "mensagem" => "Curtida removida com sucesso!"
    ]);
}
