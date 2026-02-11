<?php
include('db_connect.php');
$sql = "SELECT * FROM caixas WHERE id <= 144 ORDER BY armario, bandeja, posicao_na_bandeja";
$res = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { size: 215.9mm 279.4mm; margin: 0; }
        body { margin: 0; padding: 0; }

        .page-container {
            width: 215.9mm;
            height: 279.4mm;
            position: relative;
            page-break-after: always;
            background: white;
        }

        .etiqueta {
            position: absolute;
            width: 44.45mm;
            height: 12.7mm;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            font-size: 8.5pt;
            font-weight: bold;
            text-align: center;
            line-height: 1.0;
            box-sizing: border-box;
        }

        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="padding:10px; background:#007bff; color:white; text-align:center;">
        <strong>AJUSTE FINAL: COLUNAS 3 E 4</strong> | Escala: <strong>100%</strong> | Margens: <strong>Nenhuma</strong>
        <button onclick="window.print()">GERAR NOVO PDF / IMPRIMIR</button>
    </div>

    <div class="page-container">
        <?php 
        $count = 0;
        $margem_topo = 12.7; 
        $margem_esq = 14.5; 
        
        // Aumentei o gap horizontal para 1.2mm para empurrar mais as colunas da direita
        $gap_horizontal = 1.2; 

        while($row = $res->fetch_assoc()): 
            $pos_folha = $count % 80;
            $linha = floor($pos_folha / 4);
            $coluna = $pos_folha % 4;

            $top = $margem_topo + ($linha * 12.7);
            
            // A coluna 0 (1ª) fica no lugar, a 1 move x1, a 2 move x2, a 3 move x3
            $left = $margem_esq + ($coluna * (44.45 + $gap_horizontal));

            if ($count > 0 && $count % 80 == 0) echo "</div><div class='page-container'>";
            
            echo "<div class='etiqueta' style='top: {$top}mm; left: {$left}mm;'>";
            echo "ARMÁRIO " . $row['armario'] . "<br>";
            echo "BANDEJA " . $row['bandeja'] . $row['posicao_na_bandeja'];
            echo "</div>";
            
            $count++;
        endwhile; 
        ?>
    </div>
</body>
</html>