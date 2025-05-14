<?php
    $title = "ModUp Box - Gerenciamento de Caixa";
    $description = "O sistema ideal para gerenciar o caixa do seu supermercado.";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $title; ?></title>
    <link rel="stylesheet" href="style.css" />
    <link rel="icon" href="/img/logo_3-removebg-preview.png" />
    <!-- Ícones modernos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body>
    <header class="header">
        <div class="container">
            <img src="/img/logo_3-removebg-preview.png" alt="Logo ModUp" class="logo" />
            <h1><?php echo $title; ?></h1>
            <p><?php echo $description; ?></p>
        </div>
    </header>

    <section class="intro">
        <div class="container">
            <img src="img/fuxo+de+caixa.png" alt="Supermercado gerenciado pelo ModUp Tecnologia" class="intro-image" />
            <p>Facilidade, segurança e eficiência na gestão do seu caixa. Desde o cadastro até as configurações avançadas.</p>
        </div>
    </section>

    <section class="features">
        <div class="container">
            <h2><i class="fas fa-cogs"></i> Funcionalidades</h2>
            <div class="cards">
                <div class="card">
                    <i class="fas fa-user-plus fa-2x"></i>
                    <h3>Cadastro Rápido</h3>
                    <p>Cadastre operadores e produtos de forma simples e intuitiva.</p>
                </div>
                <div class="card">
                    <i class="fas fa-chart-line fa-2x"></i>
                    <h3>Relatórios Detalhados</h3>
                    <p>Acompanhe as movimentações financeiras com gráficos e dados precisos.</p>
                </div>
                <div class="card">
                    <i class="fas fa-shield-alt fa-2x"></i>
                    <h3>Segurança Avançada</h3>
                    <p>Controle de acesso e auditoria de operações para garantir confiabilidade.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="observations">
        <div class="container">
            <h2><i class="fas fa-info-circle"></i> Observações</h2>
            <div class="cards">
                <div class="card">
                    <i class="fas fa-map-marker-alt fa-2x"></i>
                    <h3>Cidades</h3>
                    <p>Atualmente atuamos na cidade de Porto Alegre, RS. Contate-nos para obter mais informações.</p>
                    <a href="https://wa.me/5551981527122" class="contact-link"><i class="fab fa-whatsapp"></i> Chame a gente!</a>
                </div>
                <div class="card">
                    <i class="fas fa-sync-alt fa-2x"></i>
                    <h3>Atualizações</h3>
                    <p>Estamos desenvolvendo novas funcionalidades para facilitar ainda mais o seu trabalho!</p>
                </div>
                <div class="card">
    <i class="fas fa-tasks fa-2x"></i>
    <h3>Gestão Eficiente</h3>
    <p>Organize processos com agilidade e mantenha o controle total das operações.</p>
</div>

            </div>
        </div>
    </section>

    <section class="cta">
        <div class="container">
            <h2><i class="fas fa-rocket"></i> Saiba Mais</h2>
            <p>Descubra tudo o que o ModUp Box pode fazer pelo seu supermercado.</p>
            <a href="system/inicial.php" class="button">Acessar o Sistema</a>
        </div>
    </section>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date("Y"); ?> ModUp Tecnologia - Todos os direitos reservados.</p>
        </div>
    </footer>
</body>
</html>
