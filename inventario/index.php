<?php
include('db_connect.php');

function getIconeLocal($nome) {
    $nome = mb_strtolower($nome, 'UTF-8');
    if (strpos($nome, 'fazenda') !== false) return 'fa-tractor';
    if (strpos($nome, 'escritório') !== false || strpos($nome, 'm2') !== false) return 'fa-briefcase';
    if (strpos($nome, 'apartamento') !== false || strpos($nome, 'ap') !== false || strpos($nome, 'kingdom') !== false) return 'fa-building-user';
    if (strpos($nome, 'galpão') !== false || strpos($nome, 'depósito') !== false) return 'fa-warehouse';
    if (strpos($nome, 'casa') !== false) return 'fa-house-chimney';
    return 'fa-location-dot';
}

$sql = "SELECT DISTINCT local_nome FROM inventario_itens WHERE local_nome IS NOT NULL AND local_nome != '' ORDER BY local_nome ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOCAIS DE INVENTÁRIO - M2 SHOWS</title>
    <link rel="icon" href="images/ico_m2.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body { background-image: url('images/logo_m2_fundo.png'); background-repeat: no-repeat; background-size: contain; background-position: center center; background-attachment: fixed; min-height: 100vh; }
        .local-card { transition: all 0.3s ease; cursor: pointer; border: none; border-radius: 15px; background: rgba(172, 141, 81, 0.75); box-shadow: 0 4px 15px rgba(0,0,0,0.1); min-height: 200px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; color: white; }
        .local-card:hover { transform: translateY(-10px); background: rgba(172, 141, 81, 0.95); }
        .icon-box { width: 70px; height: 70px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin-bottom: 15px; }
        .local-name { font-weight: 700; text-transform: uppercase; text-shadow: 1px 1px 2px rgba(0,0,0,0.3); }
    </style>
</head>
<body style="background-image: url('/images/LOGO ESCRITÓRIO1.png'); background-repeat: no-repeat; background-size: contain; background-position: center 10%; background-attachment: fixed;">
    <?php include('/xampp/htdocs/navbar.php'); ?>
    <div class="container py-5">
        <div class="row mb-5 text-center">
           <div class="col-12 text-center">
    <h1 class="display-6 fw-bold" style="color: #D4AF37;">📋 INVENTÁRIO DE ATIVOS M2 SHOWS</h1>
<p class="fw-bold" style="color: #a78926;">Selecione uma janela para listar os itens ou adicione um novo</p>
    
   <a href="inventario.php?cadastrar=true" class="btn btn-success btn-lg shadow mt-2 px-5">
    <i class="fa fa-plus-circle me-2"></i>CADASTRAR NOVO ITEM
</a>
    <hr class="w-25 mx-auto">
</div>
        </div>
        <div class="row g-4">
            <?php while ($row = $result->fetch_assoc()): 
                $nomeLocal = $row['local_nome']; ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card local-card p-3" onclick="window.location.href='inventario.php?local=<?php echo urlencode($nomeLocal); ?>'">
                        <div class="icon-box"><i class="fa-solid <?php echo getIconeLocal($nomeLocal); ?>"></i></div>
                        <h5 class="local-name"><?php echo $nomeLocal; ?></h5>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
     <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>