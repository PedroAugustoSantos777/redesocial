
<?php

// CREATE - Curtir publicação
function inserirCurtida($con, $dados) {
    $stmt = $con->prepare(
        "INSERT INTO curtidas (id_usuario, id_publicacao)
         VALUES (?, ?)"
    );

    $stmt->execute([
        $dados["id_usuario"],
        $dados["id_publicacao"]
    ]);

    return $con->lastInsertId();
}

// READ - Listar curtidas
function listarCurtidas($con) {
    $stmt = $con->query("SELECT * FROM curtidas");

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// DELETE - Remover curtida
function removerCurtida($con, $id_usuario, $id_publicacao) {
    $stmt = $con->prepare(
        "DELETE FROM curtidas
         WHERE id_usuario = ? AND id_publicacao = ?"
    );

    $stmt->execute([$id_usuario, $id_publicacao]);
}
