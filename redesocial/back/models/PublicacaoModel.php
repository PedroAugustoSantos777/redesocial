
<?php

// CREATE
function inserirPublicacao($con, $dados) {
    $stmt = $con->prepare(
        "INSERT INTO Publicacoes (id_usuario, legenda, imagem_url)
         VALUES (?, ?, ?)"
    );

    $stmt->execute([
        $dados["id_usuario"],
        $dados["legenda"],
        $dados["imagem_url"] ?? null
    ]);

    return $con->lastInsertId();
}

// FEED
function listarPublicacoes($con) {
    $stmt = $con->query(
        "SELECT
            p.id_publicacao,
            p.id_usuario,
            u.username,
            p.legenda,
            p.imagem_url,
            (SELECT COUNT(*)
             FROM Curtidas c
             WHERE c.id_publicacao = p.id_publicacao) AS total_curtidas
         FROM Publicacoes p
         INNER JOIN Usuarios u
         ON p.id_usuario = u.id_usuario
         ORDER BY p.id_publicacao DESC"
    );

    return $stmt->fetchAll();
}

// UPDATE
function atualizarPublicacao($con, $id, $dados) {
    $stmt = $con->prepare(
        "UPDATE Publicacoes
         SET legenda = ?, imagem_url = ?
         WHERE id_publicacao = ?"
    );

    $stmt->execute([
        $dados["legenda"],
        $dados["imagem_url"] ?? null,
        $id
    ]);
}

// DELETE
function removerPublicacao($con, $id) {
    $stmt = $con->prepare(
        "DELETE FROM Publicacoes WHERE id_publicacao = ?"
    );

    $stmt->execute([$id]);
}
