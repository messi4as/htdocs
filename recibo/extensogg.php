<?php
function extenso($valor)
{
    if ($valor <= 0 || $valor > 999999999.99) {
        return "Valor fora do intervalo permitido";
    }

    $unidades = ["", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove"];
    $dezenas = ["", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa"];
    $centenas = ["", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos"];

    $valor = number_format($valor, 2, ".", "");
    $partes = explode(".", $valor);
    $reais = str_pad($partes[0], 9, "0", STR_PAD_LEFT);
    $centavos = str_pad($partes[1], 2, "0", STR_PAD_LEFT);

    $grupos = [
        substr($reais, 0, 3), // Bilhões
        substr($reais, 3, 3), // Milhões
        substr($reais, 6, 3), // Mil
        $centavos // Centavos
    ];

    $texto = [];
    foreach ($grupos as $i => $grupo) {
        $grupo = (int)$grupo;
        if ($grupo == 0) {
            continue;
        }

        $centena = (int)($grupo / 100);
        $dezena = (int)(($grupo % 100) / 10);
        $unidade = $grupo % 10;

        if ($centena > 0) {
            $texto[] = $centenas[$centena];
            if ($dezena > 0 || $unidade > 0) {
                $texto[] = "e";
            }
        }

        if ($dezena > 0) {
            $texto[] = $dezenas[$dezena];
            if ($unidade > 0) {
                $texto[] = "e";
            }
        }

        if ($unidade > 0) {
            $texto[] = $unidades[$unidade];
        }

        if ($i == 0 && $grupo > 0) {
            $texto[] = ($grupo > 1) ? "bilhões" : "bilhão";
        } elseif ($i == 1 && $grupo > 0) {
            $texto[] = ($grupo > 1) ? "milhões" : "milhão";
        } elseif ($i == 2 && $grupo > 0) {
            $texto[] = ($grupo > 1) ? "mil" : "mil";
        } elseif ($i == 3 && $grupo > 0) {
            $texto[] = ($grupo > 1) ? "centavos" : "centavo";
        }
    }

    return implode(" ", $texto);
}
