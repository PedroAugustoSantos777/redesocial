
<?php

require_once __DIR__ . "/../models/UsuarioModel.php";

function teste() {
    echo json_encode([
        "mensagem" => "Backend respondendo com sucesso!"
    ]);
}

// CADASTRAR USUARIO
function createUsuario($con) {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (
        !is_array($dados) ||
        empty($dados["nome"]) ||
        empty($dados["username"]) ||
        empty($dados["email"]) ||
        empty($dados["senha"])
    ) {
        http_response_code(400);
        echo json_encode(["erro" => "Preencha todos os campos"]);
        return;
    }

    if (!filter_var($dados["email"], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["erro" => "E-mail inválido"]);
        return;
    }

    if (buscarUsuarioEmail($con, $dados["email"])) {
        http_response_code(409);
        echo json_encode(["erro" => "E-mail já cadastrado"]);
        return;
    }

    $id = inserirUsuario($con, $dados);

    http_response_code(201);

    echo json_encode([
        "mensagem" => "Usuário cadastrado com sucesso!",
        "id_usuario" => $id
    ]);
}

// LOGIN
function login($con) {
    $dados = json_decode(file_get_contents("php://input"), true);

    if (
        !is_array($dados) ||
        empty($dados["email"]) ||
        empty($dados["senha"])
    ) {
        http_response_code(400);
        echo json_encode(["erro" => "Informe e-mail e senha"]);
        return;
    }

    $usuario = buscarUsuarioEmail($con, $dados["email"]);

    if (!$usuario) {
        http_response_code(401);
        echo json_encode(["erro" => "E-mail ou senha incorretos"]);
        return;
    }

    // Aceita senha antiga em texto simples e atualiza para hash
    if (password_verify($dados["senha"], $usuario["senha"])) {
        $senhaValida = true;
    } elseif ($dados["senha"] === $usuario["senha"]) {
        $senhaValida = true;

        $novaSenha = password_hash($dados["senha"], PASSWORD_DEFAULT);

        $stmt = $con->prepare(
            "UPDATE Usuarios SET senha = ? WHERE id_usuario = ?"
        );

        $stmt->execute([$novaSenha, $usuario["id_usuario"]]);
    } else {
        $senhaValida = false;
    }

    if (!$senhaValida) {
        http_response_code(401);
        echo json_encode(["erro" => "E-mail ou senha incorretos"]);
        return;
    }

    unset($usuario["senha"]);

    echo json_encode([
        "mensagem" => "Login realizado com sucesso!",
        "usuario" => $usuario
    ]);
}

// LISTAR USUARIOS
function readUsuario($con) {
    echo json_encode(listarUsuarios($con));
}

// ATUALIZAR USUARIO
function updateUsuario($con) {
    $id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        echo json_encode(["erro" => "ID inválido"]);
        return;
    }

    $dados = json_decode(file_get_contents("php://input"), true);

    if (
        !is_array($dados) ||
        empty($dados["nome"]) ||
        empty($dados["username"]) ||
        empty($dados["email"]) ||
        empty($dados["senha"])
    ) {
        http_response_code(400);
        echo json_encode(["erro" => "Preencha todos os campos"]);
        return;
    }

    atualizarUsuario($con, $id, $dados);

    echo json_encode(["mensagem" => "Usuário atualizado com sucesso"]);
}

// EXCLUIR USUARIO
function deleteUsuario($con) {
    $id = filter_var($_GET["id"] ?? null, FILTER_VALIDATE_INT);

    if (!$id || $id < 1) {
        http_response_code(400);
        echo json_encode(["erro" => "ID inválido"]);
        return;
    }

    removerUsuario($con, $id);

    echo json_encode(["mensagem" => "Usuário removido com sucesso"]);
}
