
<?php

// CREATE - CURTIR
function inserirCurtida($con, $dados) {
    // Evitar curtidas duplicadas
    $verificar = $con->prepare(
        "SELECT id_curtida FROM Curtidas
         WHERE id_usuario = ? AND id_publicacao = ?"
    );

    $verificar->execute([
        $dados["id_usuario"],
        $dados["id_publicacao"]
    ]);

    if ($verificar->fetch()) {
        return false;
    }

    $stmt = $con->prepare(
        "INSERT INTO Curtidas (id_usuario, id_publicacao)
         VALUES (?, ?)"
    );

    $stmt->execute([
        $dados["id_usuario"],
        $dados["id_publicacao"]
    ]);

    return $con->lastInsertId();
}

// READ - LISTAR CURTIDAS
function listarCurtidas($con) {
    $stmt = $con->query(
        "SELECT
            c.id_curtida,
            c.id_usuario,
            u.username,
            c.id_publicacao
         FROM Curtidas c
         INNER JOIN Usuarios u
         ON c.id_usuario = u.id_usuario"
    );

    return $stmt->fetchAll();
}

// DELETE - REMOVER CURTIDA
function removerCurtida($con, $id_usuario, $id_publicacao) {
    $stmt = $con->prepare(
        "DELETE FROM Curtidas
         WHERE id_usuario = ? AND id_publicacao = ?"
    );

    $stmt->execute([$id_usuario, $id_publicacao]);

    return $stmt->rowCount();
}
