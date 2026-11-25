
<?php

// CREATE
function inserirUsuario($con, $dados) {
    $stmt = $con->prepare(
        "INSERT INTO usuarios (nome, username, email, senha)
         VALUES (?, ?, ?, ?)"
    );

    $stmt->execute([
        $dados['nome'],
        $dados['username'],
        $dados['email'],
        password_hash($dados['senha'], PASSWORD_DEFAULT)
    ]);

    return $con->lastInsertId();
}

// READ
function listarUsuarios($con) {
    $stmt = $con->query(
        "SELECT id_usuario, nome, username, email
         FROM Usuarios"
    );

    return $stmt->fetchAll();
}

// BUSCAR POR EMAIL
function buscarUsuarioEmail($con, $email) {
    $stmt = $con->prepare(
        "SELECT * FROM Usuarios WHERE email = ?"
    );

    $stmt->execute([$email]);

    return $stmt->fetch();
}

// UPDATE
function atualizarUsuario($con, $id, $dados) {
    $stmt = $con->prepare(
        "UPDATE Usuarios
         SET nome = ?, username = ?, email = ?, senha = ?
         WHERE id_usuario = ?"
    );

    $senhaHash = password_hash($dados["senha"], PASSWORD_DEFAULT);

    $stmt->execute([
        $dados["nome"],
        $dados["username"],
        $dados["email"],
        $senhaHash,
        $id
    ]);
}

// DELETE
function removerUsuario($con, $id) {
    $stmt = $con->prepare(
        "DELETE FROM Usuarios WHERE id_usuario = ?"
    );

    $stmt->execute([$id]);
}
