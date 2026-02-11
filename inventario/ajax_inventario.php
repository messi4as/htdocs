<?php
// Define o caminho para a pasta de uploads de fotos e documentos
$UPLOAD_FOTOS_DIR = 'uploads/fotos/';
$UPLOAD_DOCS_DIR = 'uploads/docs/';

// Inclui sua conexão existente ($conn)
require_once 'db_connect.php';

function redimensionarImagem($caminhoOriginal, $caminhoDestino, $larguraMaxima = 1000) {
    // Verifica se a biblioteca GD está instalada
    if (!function_exists('imagecreatefromjpeg')) {
        return false; 
    }

    list($larguraOrig, $alturaOrig, $tipo) = getimagesize($caminhoOriginal);
    
    if ($larguraOrig <= $larguraMaxima) {
        return true; // Imagem já é pequena
    }

    $fator = $larguraMaxima / $larguraOrig;
    $novaLargura = $larguraMaxima;
    $novaAltura = (int)($alturaOrig * $fator);

    $novaImagem = imagecreatetruecolor($novaLargura, $novaAltura);
    
    // Mantém transparência se for PNG
    if ($tipo == IMAGETYPE_PNG) {
        imagealphablending($novaImagem, false);
        imagesavealpha($novaImagem, true);
    }

    switch ($tipo) {
        case IMAGETYPE_JPEG: $origem = imagecreatefromjpeg($caminhoOriginal); break;
        case IMAGETYPE_PNG:  $origem = imagecreatefrompng($caminhoOriginal); break;
        case IMAGETYPE_GIF:  $origem = imagecreatefromgif($caminhoOriginal); break;
        default: return false;
    }

    imagecopyresampled($novaImagem, $origem, 0, 0, 0, 0, $novaLargura, $novaAltura, $larguraOrig, $alturaOrig);

    // Salva
    if ($tipo == IMAGETYPE_PNG) {
        imagepng($novaImagem, $caminhoDestino, 7); // Compressão 0-9
    } else {
        imagejpeg($novaImagem, $caminhoDestino, 75); // Qualidade 75%
    }

    // Removi o imagedestroy para sumir o aviso do VS Code
    return true;
}

// Verificar se a conexão falhou
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Define o charset para evitar problemas com acentos
$conn->set_charset("utf8mb4");




// Define a ação recebida (POST ou GET)
$acao = $_POST['acao'] ?? ($_GET['acao'] ?? ''); 

// ----------------------------------------------------------------------
// --- AÇÕES DE LEITURA (GET) ---
// (Sem alterações nestas ações)
// ----------------------------------------------------------------------

// --- AÇÃO: OBTER DADOS DE UM ITEM ESPECÍFICO (Para edição) ---
if ($acao == 'get_item') {
    // ... (CÓDIGO EXISTENTE MANTIDO) ...
    $item_id = $_GET['id'] ?? null; 

    if ($item_id) {
        // 1. Dados Principais do Item
        $stmt_item = $conn->prepare("SELECT * FROM inventario_itens WHERE id = ?");
        $stmt_item->bind_param("i", $item_id);
        $stmt_item->execute();
        $item = $stmt_item->get_result()->fetch_assoc();
        $stmt_item->close();

        // 2. Fotos do Item
        $stmt_fotos = $conn->prepare("SELECT id, arquivo, eh_capa FROM inventario_fotos WHERE item_id = ? ORDER BY id DESC");
        $stmt_fotos->bind_param("i", $item_id);
        $stmt_fotos->execute();
        $fotos = $stmt_fotos->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt_fotos->close();

        // 3. Documentos do Item
        $stmt_docs = $conn->prepare("SELECT id, nome_doc, arquivo FROM inventario_docs WHERE item_id = ? ORDER BY id DESC");
        $stmt_docs->bind_param("i", $item_id);
        $stmt_docs->execute();
        $docs = $stmt_docs->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt_docs->close();

        header('Content-Type: application/json');
        echo json_encode(['item' => $item, 'fotos' => $fotos, 'docs' => $docs]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['error' => 'ID do item não fornecido.']);
    }
    exit;
}

// --- AÇÃO: OBTER APENAS AS FOTOS DE UM ITEM (Para o Lightbox) ---
if ($acao == 'get_fotos_item') {
    // ... (CÓDIGO EXISTENTE MANTIDO) ...
    $item_id = $_GET['id'] ?? null; 

    if ($item_id) {
        // Busca todas as fotos do item, ordenadas para que a capa venha primeiro (eh_capa DESC)
        $stmt_fotos = $conn->prepare("SELECT id, arquivo, eh_capa FROM inventario_fotos WHERE item_id = ? ORDER BY eh_capa DESC, id DESC");
        $stmt_fotos->bind_param("i", $item_id);
        $stmt_fotos->execute();
        $fotos = $stmt_fotos->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt_fotos->close();

        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok', 'fotos' => $fotos]);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'erro', 'msg' => 'ID do item não fornecido.']);
    }
    exit;
}

// ----------------------------------------------------------------------
// --- 1. LISTAGEM DOS ITENS (Com Paginação - Retorna JSON) ---
// ----------------------------------------------------------------------
if ($acao == 'listar') {
    // Definindo filtros
    $filtro_local = "%" . ($_POST['local'] ?? '') . "%";
    $filtro_tipo = "%" . ($_POST['tipo'] ?? '') . "%";
    $filtro_amb = "%" . ($_POST['ambiente'] ?? '') . "%";
    $exibir_todos = $_POST['todos'] ?? 'false';

    // NOVO: Verifica se a requisição é para modo Somente Leitura
    $somente_leitura = ($_POST['somente_leitura'] ?? 'false') == 'true'; // <--- CHAVE PARA O CÓDIGO

    // NOVOS PARÂMETROS DE PAGINAÇÃO
    $limite = (int)($_POST['limite'] ?? 50); 
    $offset = (int)($_POST['offset'] ?? 0);

    // 1. Constrói a CLÁUSULA WHERE (usada tanto na contagem quanto na busca)
    $where_clause = "WHERE i.local_nome LIKE ? AND i.tipo LIKE ? AND i.ambiente LIKE ?";
    if ($exibir_todos == 'false') {
        $where_clause .= " AND i.status = 'ativo'";
    }

    // 2. CONTAGEM TOTAL (Para a paginação)
    $stmt_count = $conn->prepare("SELECT COUNT(*) as total FROM inventario_itens i " . $where_clause);
    if (!$stmt_count) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'erro', 'msg' => "Erro na preparação da contagem SQL: " . $conn->error]); 
        exit;
    }
    $stmt_count->bind_param("sss", $filtro_local, $filtro_tipo, $filtro_amb);
    $stmt_count->execute();
    $total_itens = $stmt_count->get_result()->fetch_assoc()['total'];
    $stmt_count->close();

    // 3. BUSCA DOS ITENS (Com LIMIT e OFFSET)
    $sql = "SELECT i.*, 
             DATE_FORMAT(i.data_cadastro, '%d/%m/%Y') AS data_formatada, 
             (SELECT arquivo FROM inventario_fotos WHERE item_id = i.id AND eh_capa = 1 LIMIT 1) as foto_capa
             FROM inventario_itens i 
             " . $where_clause . "
             ORDER BY i.id DESC 
             LIMIT ? OFFSET ?"; 

    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'erro', 'msg' => "Erro na preparação da lista SQL: " . $conn->error]); 
        exit;
    }

    $stmt->bind_param("sssii", $filtro_local, $filtro_tipo, $filtro_amb, $limite, $offset);
    
    $stmt->execute();
    $result = $stmt->get_result();

    $html_output = "";
    if ($result->num_rows > 0) {
        while($item = $result->fetch_assoc()) {
            // Define cor da linha e status visual
            $classe_linha = ($item['status'] != 'ativo') ? 'table-danger text-danger' : '';
            $img = $item['foto_capa'] ? $UPLOAD_FOTOS_DIR . $item['foto_capa'] : "assets/sem-foto.png"; 
            
            // Tratamento da imagem
            if(!$item['foto_capa']) {
                $img_tag = "<div class='bg-secondary text-white d-flex justify-content-center align-items-center rounded foto-lista-clicavel' data-item-id='{$item['id']}' style='width:50px; height:50px;'><i class='fa fa-camera'></i></div>";
            } else {
                $img_tag = "<img src='$img' width='50' height='50' class='rounded foto-lista-clicavel' data-item-id='{$item['id']}' title='Visualizar Fotos'>";
            }

            // Lógica de botões condicionais (MODIFICADA AQUI)
            $botoes = "";
            if ($somente_leitura) {
                // Se for somente leitura, a coluna AÇÕES é vazia
                $botoes = "";
            } else if ($item['status'] == 'ativo') {
                // Se estiver ativo e NÃO for somente leitura, exibe Editar e Mover/Baixar
                $botoes = "
                    <button class='btn btn-warning btn-sm btn-editar' data-id='{$item['id']}' title='Editar'><i class='fa fa-pencil'></i></button>
                    <button class='btn btn-danger btn-sm btn-mover' data-id='{$item['id']}' title='Mover/Baixar'><i class='fa fa-exchange'></i></button>
                ";
            } else {
                // Se NÃO estiver ativo (Baixado) e NÃO for somente leitura, exibe Editar e Reverter
                $botoes = "
                    <button class='btn btn-warning btn-sm btn-editar' data-id='{$item['id']}' title='Editar'><i class='fa fa-pencil'></i></button>
                    <button class='btn btn-success btn-sm btn-reverter' data-id='{$item['id']}' title='Reverter Baixa'><i class='fa fa-undo'></i> Reverter</button>
                ";
            }
            
            $html_output .= "<tr class='$classe_linha text-center align-middle'>";
            $html_output .= "<td>$img_tag</td>";
            $html_output .= "<td>{$item['id']}</td>";
            $html_output .= "<td><strong>{$item['nome']}</strong><br><small>{$item['tipo']}</small></td>";
            $html_output .= "<td><strong>{$item['local_nome']}</strong> <br> <small class='text-muted'>{$item['ambiente']}</small></td>";
            $html_output .= "<td>{$item['data_formatada']}</td>"; 
            $html_output .= "<td>" . ucfirst($item['status']) . "</td>";
            $html_output .= "<td>$botoes</td>"; // Ações (vazia se for somente leitura)
            $html_output .= "</tr>";
            
        }
    } else {
        $html_output = "<tr><td colspan='7' class='text-center text-muted'>Nenhum item encontrado.</td></tr>"; 
    }
    $stmt->close();

    // 4. RETORNA O RESULTADO COMO JSON
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'ok', 
        'html' => $html_output, 
        'total_itens' => $total_itens
    ]);
    exit;
}

// ----------------------------------------------------------------------
// --- NOVO: AÇÃO PARA IMPRIMIR LISTA COMPLETA (Ignora Paginação e retorna HTML puro) ---
// ----------------------------------------------------------------------
if ($acao == 'imprimir_lista') {
    // ... (CÓDIGO EXISTENTE MANTIDO) ...
    // A coluna de ações já é mantida VAZIA na impressão pelo código original: $html_output .= "<td></td>";
    
    // Definindo filtros (igual à ação listar)
    $filtro_local = "%" . ($_POST['local'] ?? '') . "%";
    $filtro_tipo = "%" . ($_POST['tipo'] ?? '') . "%";
    $filtro_amb = "%" . ($_POST['ambiente'] ?? '') . "%";
    $exibir_todos = $_POST['todos'] ?? 'false';

    // 1. Constrói a CLÁUSULA WHERE (igual à ação listar)
    $where_clause = "WHERE i.local_nome LIKE ? AND i.tipo LIKE ? AND i.ambiente LIKE ?";
    if ($exibir_todos == 'false') {
        $where_clause .= " AND i.status = 'ativo'";
    }

    // 2. BUSCA DOS ITENS (SEM LIMIT e OFFSET)
    $sql = "SELECT i.*, 
             DATE_FORMAT(i.data_cadastro, '%d/%m/%Y') AS data_formatada, 
             (SELECT arquivo FROM inventario_fotos WHERE item_id = i.id AND eh_capa = 1 LIMIT 1) as foto_capa
             FROM inventario_itens i 
             " . $where_clause . "
             ORDER BY i.id DESC"; 

    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        echo "<tr><td colspan='7' class='text-center text-danger'>Erro na preparação da lista SQL para impressão: " . $conn->error . "</td></tr>"; 
        exit;
    }

    $stmt->bind_param("sss", $filtro_local, $filtro_tipo, $filtro_amb);
    
    $stmt->execute();
    $result = $stmt->get_result();

    $html_output = "";
    if ($result->num_rows > 0) {
        while($item = $result->fetch_assoc()) {
            // Define cor da linha e status visual
            $classe_linha = ($item['status'] != 'ativo') ? 'table-danger text-danger' : '';
            $img = $item['foto_capa'] ? $UPLOAD_FOTOS_DIR . $item['foto_capa'] : "assets/sem-foto.png"; 
            
            // Tratamento da imagem (simplificado para impressão)
            if(!$item['foto_capa']) {
                $img_tag = "<div style='width:50px; height:50px; background-color:#ccc; display:flex; justify-content:center; align-items:center; border-radius:.25rem; font-size:10px;'>Sem Foto</div>";
            } else {
                $img_tag = "<img src='$img' width='50' height='50' style='object-fit:cover; border-radius:.25rem;'>"; 
            }

            // A coluna de ações é mantida VAZIA para impressão (CORRETO)
            
            $html_output .= "<tr class='$classe_linha text-center align-middle'>";
            $html_output .= "<td>$img_tag</td>";
            $html_output .= "<td>{$item['id']}</td>";
            $html_output .= "<td><strong>{$item['nome']}</strong><br><small>{$item['tipo']}</small></td>";
            $html_output .= "<td><strong>{$item['local_nome']}</strong> <br> <small class='text-muted'>{$item['ambiente']}</small></td>";
            $html_output .= "<td>{$item['data_formatada']}</td>"; 
            $html_output .= "<td>" . ucfirst($item['status']) . "</td>";
            $html_output .= "<td></td>"; // Coluna de ações vazia na impressão (MANTIDO)
            $html_output .= "</tr>";
            
        }
    } else {
        $html_output = "<tr><td colspan='7' class='text-center text-muted'>Nenhum item encontrado.</td></tr>"; 
    }
    $stmt->close();

    // Retorna APENAS O HTML puro da tabela, sem JSON
    echo $html_output;
    exit;
}
// ----------------------------------------------------------------------
// --- FIM DA AÇÃO DE IMPRESSÃO ---
// ----------------------------------------------------------------------


// ----------------------------------------------------------------------
// --- AÇÕES DE ALTERAÇÃO (POST) ---
// (Estas ações só são chamadas por inventario.php, então permanecem inalteradas)
// ----------------------------------------------------------------------

// --- AÇÃO: DEFINIR FOTO DE CAPA ---
if ($acao == 'definir_capa') {
    // ... (CÓDIGO EXISTENTE MANTIDO) ...
    $item_id = $_POST['item_id'];
    $foto_id = $_POST['foto_id'];

    // 1. Desmarcar todas as capas para este item
    $stmt_reset = $conn->prepare("UPDATE inventario_fotos SET eh_capa = 0 WHERE item_id = ?");
    $stmt_reset->bind_param("i", $item_id);
    $stmt_reset->execute();
    $stmt_reset->close();

    // 2. Marcar a foto selecionada como capa
    $stmt_set = $conn->prepare("UPDATE inventario_fotos SET eh_capa = 1 WHERE id = ? AND item_id = ?");
    $stmt_set->bind_param("ii", $foto_id, $item_id);
    $stmt_set->execute();
    $stmt_set->close();

    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok']);
    exit;
}

// --- AÇÃO: REMOVER FOTO (Com exclusão do arquivo físico) ---
if ($acao == 'remover_foto') {
    // ... (CÓDIGO EXISTENTE MANTIDO) ...
    $foto_id = $_POST['foto_id'];
    $item_id = $_POST['item_id'];

    // 1. Pegar o nome do arquivo da foto
    $stmt_get_file = $conn->prepare("SELECT arquivo, eh_capa FROM inventario_fotos WHERE id = ?");
    $stmt_get_file->bind_param("i", $foto_id);
    $stmt_get_file->execute();
    $result_file = $stmt_get_file->get_result();
    $foto_info = $result_file->fetch_assoc();
    $stmt_get_file->close();

    if ($foto_info) {
        $arquivo = $foto_info['arquivo'];
        $era_capa = $foto_info['eh_capa'];

        // 2. Excluir do banco
        $stmt_delete = $conn->prepare("DELETE FROM inventario_fotos WHERE id = ?");
        $stmt_delete->bind_param("i", $foto_id);
        $stmt_delete->execute();
        $stmt_delete->close();

        // 3. Excluir o arquivo físico
        if (file_exists($UPLOAD_FOTOS_DIR . $arquivo)) {
            unlink($UPLOAD_FOTOS_DIR . $arquivo); // <--- DELETA O ARQUIVO DO DIRETÓRIO
        }

        // 4. Se a foto excluída era a capa, tentar definir outra como capa (se houver)
        if ($era_capa) {
             $stmt_check_cover = $conn->prepare("SELECT 1 FROM inventario_fotos WHERE item_id = ? AND eh_capa = 1 LIMIT 1");
             $stmt_check_cover->bind_param("i", $item_id);
             $stmt_check_cover->execute();
             $stmt_check_cover->store_result();
             
             if ($stmt_check_cover->num_rows == 0) {
                 $conn->query("UPDATE inventario_fotos SET eh_capa = 1 WHERE item_id = $item_id ORDER BY id DESC LIMIT 1"); 
             }
             $stmt_check_cover->close();
        }
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'erro', 'msg' => 'Foto não encontrada.']);
    }
    exit;
}

// --- AÇÃO: REMOVER DOCUMENTO (Com exclusão do arquivo físico) ---
if ($acao == 'remover_doc') {
    // ... (CÓDIGO EXISTENTE MANTIDO) ...
    $doc_id = $_POST['doc_id'];

    // 1. Pegar o nome do arquivo do documento
    $stmt_get_file = $conn->prepare("SELECT arquivo FROM inventario_docs WHERE id = ?");
    $stmt_get_file->bind_param("i", $doc_id);
    $stmt_get_file->execute();
    $result_file = $stmt_get_file->get_result();
    $doc_info = $result_file->fetch_assoc();
    $stmt_get_file->close();

    if ($doc_info) {
        $arquivo = $doc_info['arquivo'];
        
        // 2. Excluir do banco
        $stmt_delete = $conn->prepare("DELETE FROM inventario_docs WHERE id = ?");
        $stmt_delete->bind_param("i", $doc_id);
        $stmt_delete->execute();
        $stmt_delete->close();

        // 3. Excluir o arquivo físico
        if (file_exists($UPLOAD_DOCS_DIR . $arquivo)) {
            unlink($UPLOAD_DOCS_DIR . $arquivo); // <--- DELETA O ARQUIVO DO DIRETÓRIO
        }
        
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'erro', 'msg' => 'Documento não encontrado.']);
    }
    exit;
}

// --- AÇÃO: REVERTER BAIXA ---
if ($acao == 'reverter_baixa') {
    // ... (CÓDIGO EXISTENTE MANTIDO) ...
    $id = $_POST['id'];

    // Define o status como 'ativo' e zera o motivo da baixa.
    $stmt = $conn->prepare("UPDATE inventario_itens SET status = 'ativo', motivo_baixa = NULL WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()){
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'erro', 'msg' => $stmt->error]);
    }
    $stmt->close();
    exit;
}

// --- 2. MOVER (BAIXA) ---
if ($acao == 'mover') {
    // ... (CÓDIGO EXISTENTE MANTIDO) ...
    $novo_status = $_POST['novo_status'];
    $motivo  = $_POST['motivo'];
    $id   = $_POST['id'];

    $stmt = $conn->prepare("UPDATE inventario_itens SET status = ?, motivo_baixa = ? WHERE id = ?");
    $stmt->bind_param("ssi", $novo_status, $motivo, $id);
    
    if($stmt->execute()){
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok']);
    } else {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'erro', 'msg' => $stmt->error]);
    }
    $stmt->close();
    exit;
}

// --- 3. SALVAR / EDITAR (Lógica de Inserção e Upload de Múltiplas Fotos/Docs) ---
if ($acao == 'salvar') {
    // ... (CÓDIGO EXISTENTE MANTIDO) ...
    $nome  = $_POST['nome'];
    $tipo  = $_POST['tipo'];
    $local_nome = $_POST['local_nome'];
    $ambiente = $_POST['ambiente'];
    $descricao = $_POST['descricao'];
    $id_item  = $_POST['id'];

    if (empty($id_item)) {
        // INSERIR: data_cadastro é definido como NOW()
        $stmt = $conn->prepare("INSERT INTO inventario_itens (nome, tipo, data_cadastro, local_nome, ambiente, descricao, status) VALUES (?, ?, NOW(), ?, ?, ?, 'ativo')");
        $stmt->bind_param("sssss", $nome, $tipo, $local_nome, $ambiente, $descricao);
        $stmt->execute();
        $id_item = $conn->insert_id;
        $stmt->close();
    } else {
        // ATUALIZAR
        $stmt = $conn->prepare("UPDATE inventario_itens SET nome=?, tipo=?, local_nome=?, ambiente=?, descricao=? WHERE id=?");
        $stmt->bind_param("sssssi", $nome, $tipo, $local_nome, $ambiente, $descricao, $id_item); 
        $stmt->execute();
        $stmt->close();
    }

 
    

  // UPLOAD DE FOTOS (Adiciona novas fotos ao item)
if (isset($_FILES['fotos']) && !empty($_FILES['fotos']['name'][0])) {
    
    if (!file_exists($UPLOAD_FOTOS_DIR)) { 
        mkdir($UPLOAD_FOTOS_DIR, 0777, true); 
    }

    $has_existing_cover = false;
    if (!empty($id_item)) { 
         $stmt_check_cover = $conn->prepare("SELECT 1 FROM inventario_fotos WHERE item_id = ? AND eh_capa = 1 LIMIT 1");
         $stmt_check_cover->bind_param("i", $id_item);
         $stmt_check_cover->execute();
         $stmt_check_cover->store_result();
         $has_existing_cover = $stmt_check_cover->num_rows > 0;
         $stmt_check_cover->close();
    }

    foreach ($_FILES['fotos']['name'] as $key => $name) {
        // Verifica se o índice existe para evitar "Undefined array key"
        if(isset($_FILES['fotos']['error'][$key]) && $_FILES['fotos']['error'][$key] == 0){
            $ext = pathinfo($name, PATHINFO_EXTENSION);
            $novo_nome = uniqid() . "." . $ext;
            
            if(move_uploaded_file($_FILES['fotos']['tmp_name'][$key], $UPLOAD_FOTOS_DIR . $novo_nome)){
                $eh_capa = 0;
                if (!$has_existing_cover) {
                    $eh_capa = 1;
                    $has_existing_cover = true; 
                }

                $stmt_foto = $conn->prepare("INSERT INTO inventario_fotos (item_id, arquivo, eh_capa) VALUES (?, ?, ?)");
                $stmt_foto->bind_param("isi", $id_item, $novo_nome, $eh_capa);
                $stmt_foto->execute();
                $stmt_foto->close();
            }
        }
    }
}

// VERIFIQUE ABAIXO: O erro da linha 507 provavelmente está no UPLOAD DE DOCUMENTO
if (isset($_FILES['doc']) && !empty($_FILES['doc']['name'])) {
    if ($_FILES['doc']['error'] === 0) {
        $name_doc = $_FILES['doc']['name'];
        $ext_doc = pathinfo($name_doc, PATHINFO_EXTENSION);
        $novo_nome_doc = uniqid() . "_doc." . $ext_doc;

        if (!file_exists($UPLOAD_DOCS_DIR)) { mkdir($UPLOAD_DOCS_DIR, 0777, true); }

        if (move_uploaded_file($_FILES['doc']['tmp_name'], $UPLOAD_DOCS_DIR . $novo_nome_doc)) {
            $stmt_doc = $conn->prepare("INSERT INTO inventario_docs (item_id, arquivo, nome_doc) VALUES (?, ?, ?)");
            $stmt_doc->bind_param("iss", $id_item, $novo_nome_doc, $name_doc);
            $stmt_doc->execute();
            $stmt_doc->close();
        }
    }
}
    
    // UPLOAD DE DOCUMENTO (Adiciona novo documento ao item)
    if (!empty($_FILES['doc']['name'])) {
        // ... (RESTO DO CÓDIGO DE UPLOAD DE DOCUMENTOS MANTIDO) ...
        if (!file_exists($UPLOAD_DOCS_DIR)) { mkdir($UPLOAD_DOCS_DIR, 0777, true); }
        
        $nome_doc_orig = $_FILES['doc']['name'];
        $ext_doc = pathinfo($nome_doc_orig, PATHINFO_EXTENSION);
        $novo_nome_doc = uniqid("doc_") . "." . $ext_doc;

        if(move_uploaded_file($_FILES['doc']['tmp_name'], $UPLOAD_DOCS_DIR . $novo_nome_doc)){
            $stmt_doc = $conn->prepare("INSERT INTO inventario_docs (item_id, nome_doc, arquivo) VALUES (?, ?, ?)");
            $stmt_doc->bind_param("iss", $id_item, $nome_doc_orig, $novo_nome_doc);
            $stmt_doc->execute();
            $stmt_doc->close();
        }
    }

    header('Content-Type: application/json');
    echo json_encode(['status' => 'ok', 'id_item' => $id_item]);
    exit;
}

// Se nenhuma ação for definida, apenas fecha a conexão
$conn->close();
?>