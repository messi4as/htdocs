<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = mysqli_real_escape_string($conn, $_POST['data_movimentacao']);   
    $descricao = strtoupper(mysqli_real_escape_string($conn, $_POST['descricao']));
    $responsavel = strtoupper(mysqli_real_escape_string($conn, $_POST['responsavel']));
    $id_item = mysqli_real_escape_string($conn, $_POST['id_item']);

   
    $query = "INSERT INTO movimentacao (id_item, data_movimentacao, descricao, responsavel)
    VALUES ('$id_item', '$data', '$descricao', '$responsavel')"; 
              

    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'movimentacao' => ['data_movimentacao' => $data, 'descricao' => $descricao, 'responsavel' => $responsavel]]);
    } else {
        echo json_encode(['success' => false]);
    }
}
