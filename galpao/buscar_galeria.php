<?php
require 'db_connect.php';
$id = $_GET['id'];
$fotos = [];
// Foto principal
$q1 = mysqli_query($conn, "SELECT foto FROM galpao WHERE cod_item = '$id'");
$f1 = mysqli_fetch_assoc($q1);
if($f1['foto']) $fotos[] = $f1['foto'];
// Fotos da galeria
$q2 = mysqli_query($conn, "SELECT caminho_imagem FROM galpao_imagens WHERE id_item = '$id'");
while($f2 = mysqli_fetch_assoc($q2)) { $fotos[] = $f2['caminho_imagem']; }
echo json_encode($fotos);