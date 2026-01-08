<?php
/**
 * Ficheiro: index.php
 * 
 * Objetivo: Página principal (Homepage) do site.
 */

$page_title = 'Souto Barbearia - Início';
require_once 'templates/header.php';
?>

<!-- Secção "Hero": a área de destaque principal, com a imagem de fundo e o botão de chamada para ação (CTA). -->
<section id="hero">
    <h2>O teu estilo, <br> a nossa paixão!</h2>
    <?php if ($is_barbeiro): ?>
        <!-- Se for barbeiro, o botão leva para a agenda dele. -->
        <a href="agenda_barbeiro.php" class="cta-button">Ver Agenda</a>
    <?php elseif ($is_logged_in): ?>
        <!-- Se o utilizador estiver logado, o botão leva diretamente para a página de marcação. -->
        <a href="marcacao.php" class="cta-button">Faz a tua Marcação</a>
    <?php else: ?>
        <!-- Agora, mesmo para utilizadores não logados, o botão aponta para marcacao.php. A própria página de marcação irá redirecionar para o login se necessário. -->
        <a href="marcacao.php" class="cta-button">Faz a tua Marcação</a>
    <?php endif; ?>
</section>

<!-- Secção "Sobre Nós": Apresenta a história e a filosofia da barbearia. -->
<section id="sobre">
    <h2>Sobre a Barbearia</h2>
    <p>Fundada em 2022, a nossa barbearia combina técnicas clássicas com as últimas tendências para oferecer um serviço de excelência. O nosso espaço foi pensado para o teu conforto, com um ambiente que mistura o rústico e o moderno, onde cada detalhe conta. A nossa missão é mais do que apenas cortar cabelo; é criar uma experiência. Desde o momento em que entras até ao aperto de mão final, queremos que te sintas em casa. Bem-vindo à Souto Barbearia.</p>
</section>

<!-- O <main> agrupa o conteúdo principal da página. -->
<main>
    <!-- Secção "Galeria": Mostra uma pré-visualização de imagens e um link para a galeria completa. -->
     <section id="galeria">
        <h2>A Nossa Arte</h2>
        <p style="text-align: center;">Dá uma vista de olhos em alguns dos nossos melhores cortes e no ambiente único da nossa barbearia.</p>
        <div class="galeria-preview">
            <a href="galeria.php" class="preview-item"><img src="imagens/corte1.JPG"></a>
            <a href="galeria.php" class="preview-item"><img src="imagens/foto2.JPG"> </a>
            <a href="galeria.php" class="preview-item"><img src="imagens/corte2.JPG"></a>
        </div>
        <a href="galeria.php" class="cta-button" style="margin-top: 2rem;">Ver Galeria Completa</a>
     </section>
     
    <!-- Secção de Contactos e Localização: Inclui morada, telefone, horário e um mapa incorporado. -->
    <section id="contactos">
        <h2>Entra em Contacto</h2>
        <p>Estamos à tua espera! Faz a tua marcação aqui no site ou visita-nos na nossa morada. Segue-nos também nas redes sociais!</p>
        <div class="contact-layout">
            <div class="contact-details">
                <p><strong>Morada:</strong> Rua Central, Nº 39, 4740-498 Braga</p>
                <p><strong>Telefone:</strong> (+351) 925 009 222</p>
                <p><strong>Horário:</strong> Terça a Sábado, das 8:30 às 20:00.</p>
            </div>
            <div class="mapa">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d11925.68882043511!2d-8.720863544580077!3d41.515478900000004!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xd244d30fee9d5cb%3A0xf1001963f8d1d999!2sSouto%20Barbearia!5e0!3m2!1spt-PT!2spt!4v1716390143162!5m2!1spt-PT!2spt" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>
</main>

<?php
require_once 'templates/footer.php';
?>