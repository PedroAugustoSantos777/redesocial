
<?php

$host = "localhost";
$banco = "insta";
$usuario = "root";
$senha = "";

try {
    $con = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8",
        $usuario,
        $senha
    );

    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $con->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "erro" => "Erro na conexão com o banco de dados"
    ]);
    exit;
}
