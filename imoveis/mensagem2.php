<?php if (isset($_SESSION['mensagem'])): ?>
    <div class="alert alert-info">
        <?= $_SESSION['mensagem']; ?>
        <?php unset($_SESSION['mensagem']); ?>
    </div>
<?php endif; ?>