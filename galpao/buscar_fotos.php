<?php
require 'db_connect.php';
$id = mysqli_real_escape_string($conn, $_GET['id']);

$fotos = [];

// 1. Pega a foto principal da tabela galpao
$res_principal = mysqli_query($conn, "SELECT foto FROM galpao WHERE cod_item = '$id'");
if($row = mysqli_fetch_assoc($res_principal)) {
    if(!empty($row['foto'])) $fotos[] = $row['foto'];
}

// 2. Pega as fotos extras da galeria
$res_extras = mysqli_query($conn, "SELECT caminho_imagem FROM galpao_imagens WHERE id_item = '$id'");
while($row_extra = mysqli_fetch_assoc($res_extras)) {
    $fotos[] = $row_extra['caminho_imagem'];
}

echo json_encode($fotos);