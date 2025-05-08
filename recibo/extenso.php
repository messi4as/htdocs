<?php
function extenso($valor)
{
    if ($valor <= 0 || $valor > 999999999.99) {
        return "Valor fora do intervalo permitido";
    }

    $unidades = ["", "um", "dois", "três", "quatro", "cinco", "seis", "sete", "oito", "nove", "dez", "onze", "doze", "treze", "quatorze", "quinze", "dezesseis", "dezessete", "dezoito", "dezenove"];
    $dezenas = ["", "dez", "vinte", "trinta", "quarenta", "cinquenta", "sessenta", "setenta", "oitenta", "noventa"];
    $centenas = ["", "cem", "duzentos", "trezentos", "quatrocentos", "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos"];

    $valor = number_format($valor, 2, ".", "");
    $partes = explode(".", $valor);
    $reais = str_pad($partes[0], 9, "0", STR_PAD_LEFT);
    $centavos = str_pad($partes[1], 2, "0", STR_PAD_LEFT);

    $grupos = [
        substr($reais, 0, 3),
        substr($reais, 3, 3),
        substr($reais, 6, 3),
        $centavos
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
            if ($grupo % 100 == 0) {
                $texto[] = $centenas[$centena];
            } else {
                $texto[] = $centenas[$centena] . " e";
            }
        }

        if ($dezena > 1) {
            $texto[] = $dezenas[$dezena];
            if ($unidade > 0) {
                $texto[] = " e " . $unidades[$unidade];
            }
        } else {
            $texto[] = $unidades[$dezena * 10 + $unidade];
        }

        if ($i == 0) {
            $texto[] = ($grupo > 1) ? "milhões" : "milhão";
        } elseif ($i == 1) {
            $texto[] = "mil";
        } elseif ($i == 2) {
            $texto[] = ($grupo > 1) ? "reais" : "real";
        } elseif ($i == 3) {
            $texto[] = ($grupo > 1) ? "centavos" : "centavo";
        }
    }

    // Corrige a formatação para valores redondos como 2000, 3000, etc.
    $texto = implode(" ", $texto);
    if (strpos($texto, "mil") !== false && strpos($texto, "reais") === false) {
        $texto = str_replace("mil", "mil reais", $texto);
    }

    // Adiciona "e" entre "mil" e "centenas" quando necessário
    $texto = preg_replace('/mil (cem|duzentos|trezentos|quatrocentos|quinhentos|seiscentos|setecentos|oitocentos|novecentos)/', 'mil e $1', $texto);

    // Adiciona "e" entre "reais" e "centavos" quando necessário
    if (strpos($texto, "reais") !== false && strpos($texto, "centavo") !== false) {
        $texto = str_replace("reais ", "reais e ", $texto);
    }

    // Adiciona "e" entre "mil" e "dezenas/unidades" quando necessário
    $texto = preg_replace('/mil (vinte|trinta|quarenta|cinquenta|sessenta|setenta|oitenta|noventa|um|dois|três|quatro|cinco|seis|sete|oito|nove|dez|onze|doze|treze|quatorze|quinze|dezesseis|dezessete|dezoito|dezenove)/', 'mil e $1', $texto);

    // Corrige a formatação para valores como "milhões de reais"
    $texto = str_replace("milhão reais", "milhão de reais", $texto);
    $texto = str_replace("milhões reais", "milhões de reais", $texto);

    // Corrige a formatação para valores como "milhões de reais e centavos"
    $texto = str_replace("milhões de reais e", "milhões de reais e", $texto);

    // Corrige a formatação para valores redondos em milhões
    $texto = preg_replace('/milhões e reais/', 'milhões de reais', $texto);

    // Corrige a formatação para valores redondos em milhões sem centavos
    $texto = preg_replace('/milhão de reais$/', 'milhão de reais', $texto);
    $texto = preg_replace('/milhões de reais$/', 'milhões de reais', $texto);

    // Corrige a formatação para "um milhão de reais"
    $texto = str_replace("um milhão de reais", "um milhão de reais", $texto);

    return $texto;
}

if (isset($_GET['valor_recibo'])) {
    $valor = floatval($_GET['valor_recibo']);
    echo extenso($valor);
}
