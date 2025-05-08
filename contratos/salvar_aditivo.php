<?php
session_start();
require 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = mysqli_real_escape_string($conn, $_POST['data_aditivo']);   
    $descricao = strtoupper(mysqli_real_escape_string($conn, $_POST['descricao']));
    $responsavel = strtoupper(mysqli_real_escape_string($conn, $_POST['responsavel']));
    $id_contrato = mysqli_real_escape_string($conn, $_POST['id_contrato']);

   
    $query = "INSERT INTO aditivo (id_contrato, data_aditivo, descricao, responsavel)
    VALUES ('$id_contrato', '$data', '$descricao', '$responsavel')"; 
              

    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true, 'aditivo' => ['data_aditivo' => $data, 'descricao' => $descricao, 'responsavel' => $responsavel]]);
    } else {
        echo json_encode(['success' => false]);
    }
}
