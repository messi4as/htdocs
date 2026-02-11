<?php
include('db_connect.php');

$id = $_GET['id'];

// O Banco de Dados configurado com ON DELETE CASCADE cuidará dos volumes.
// Mas precisamos apagar a pasta física da caixa:
$pasta = "uploads/caixa_" . $id;

function rrmdir($dir) { // Função para apagar pasta e subconteúdos
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
            }
        }
        reset($objects);
        rmdir($dir);
    }
}

rrmdir($pasta);

$stmt = $conn->prepare("DELETE FROM caixas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: index.php?msg=caixa_removida");
?>