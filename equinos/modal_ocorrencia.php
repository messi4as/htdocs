<div class="modal fade" id="modalOcorrencia" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="code_equino.php" method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Registar Ocorrência</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="cod_equino" value="<?= $equino['cod_equino'] ?>">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label>Data</label>
                            <input type="date" name="data_evento" class="form-control" required value="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Tipo</label>
                            <select name="tipo_evento" class="form-control" required>
                                <option value="Vacinação">Vacinação</option>
                                <option value="Exame">Exame</option>
                                <option value="Ferrageamento">Ferrageamento</option>
                                <option value="Outros">Outros</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label>Peso (kg)</label>
                            <input type="number" step="0.01" name="peso_kg" class="form-control">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Responsável/Veterinário</label>
                            <input type="text" name="veterinario" class="form-control">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label>Descrição</label>
                            <textarea name="descricao_detalhada" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="fw-bold text-primary"><i class="bi bi-paperclip"></i> Anexar Documento (PDF, Imagem, etc)</label>
                            <input type="file" name="anexo" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="save_ocorrencia" class="btn btn-primary">Guardar Ocorrência</button>
                </div>
            </form>
        </div>
    </div>
</div>