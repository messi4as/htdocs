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

  // ... código anterior (unidades, dezenas, centenas) ...

    $texto = implode(" ", $texto);

    // --- BLOCO DE CORREÇÃO GRAMATICAL ---
    
    // 1. Corrigir "CEM" para "CENTO" quando houver complemento (ex: 111 vira CENTO E ONZE)
    // Procuramos "CEM" que não esteja no fim da frase ou que venha antes de "E"
    $texto = preg_replace('/\bcem\b(?=\s+e\b)/i', 'cento', $texto);

    // 2. Adicionar "E" entre Milhões e Milhares (ex: UM MILHÃO E CEM MIL)
    // Se houver "milhão/milhões" e logo depois vier uma centena (cento, duzentos...)
    $texto = preg_replace('/(milhão|milhões)\s+(cento|duzentos|trezentos|quatrocentos|quinhentos|seiscentos|setecentos|oitocentos|novecentos|dez|vinte|trinta|quarenta|cinquenta|sessenta|setenta|oitenta|noventa|um|dois|três|quatro|cinco|seis|sete|oito|nove)/i', '$1 e $2', $texto);

    // 3. Tratamento para Milhões/Milhão e o "DE REAIS"
    if (strpos($texto, "milhão") !== false || strpos($texto, "milhões") !== false) {
        $texto = str_replace("milhão reais", "milhão de reais", $texto);
        $texto = str_replace("milhões reais", "milhões de reais", $texto);
        
        // Se for milhão redondo (ex: 1.000.000,00), adiciona "de reais"
        if (strpos($texto, "reais") === false && strpos($texto, "centavo") === false) {
            $texto .= " de reais";
        }
    } 
    // 4. Tratamento para Milhares simples (Evita o erro "REAISHÃO")
    elseif (strpos($texto, "mil") !== false && strpos($texto, "reais") === false) {
        $texto = str_replace("mil", "mil reais", $texto);
    }

    // 5. Adicionar "E" entre Reais e Centavos
    if (strpos($texto, "reais") !== false && strpos($texto, "centavo") !== false) {
        $texto = str_replace("reais ", "reais e ", $texto);
    }

    // 6. Retornar em MAIÚSCULAS
    return mb_strtoupper($texto, 'UTF-8');
}

if (isset($_GET['valor_recibo'])) {
    $valor = floatval($_GET['valor_recibo']);
    echo extenso($valor);
}
