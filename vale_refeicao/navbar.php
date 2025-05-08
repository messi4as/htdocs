<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-md d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <img src="logo_fazenda.png" alt="Logo" class="me-2">


            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="/index.php">Home</a>
                    </li>

                    <li class="nav-item"><a class="nav-link" href="/vale_refeicao/visualizar_grafico.php">Status</a></li>
                    <li class="nav-item"><a class="nav-link" href="/vale_refeicao/historico_cartoes.php">Histórico</a></li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            Movimentação
                        </a>

                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" aria-current="page" href="/vale_refeicao/entrega_cartoes.php">Entrega</a></li>
                            <li><a class="dropdown-item" aria-current="page" href="/vale_refeicao/devolver_cartoes.php">Devolução</a></li>
                            <li><a class="dropdown-item" aria-current="page" href="/vale_refeicao/extraviar_cartoes.php">Extravio</a></li>
                            <li><a class="dropdown-item" aria-current="page" href="/vale_refeicao/utilizacao_cartoes.php">Utilização</a></li>
                            <li><a class="dropdown-item" aria-current="page" href="/vale_refeicao/fechamento_cartoes.php">Fechamento</a></li>
                            <li><a class="dropdown-item" aria-current="page" href="/vale_refeicao/estoque_cartoes.php">Estoque</a> </li>

                        </ul>
                    </li>


            </div>

        </div>
        <a class="navbar-brand" href="/vale_refeicao/visualizar_grafico.php">GESTÃO DE VALE ALIMENTAÇÃO - FAZENDA ROSADA</a>
</nav>