<?php
/**
 * Ficheiro: galeria.php
 * Objetivo: Página da galeria de fotos.
 */

$page_title = 'Galeria de Fotos';
$header_style = 'minimal';
$body_class = 'galeria-page';

require_once 'templates/header.php';
?>

<main>
    <!-- A grelha de imagens. Cada imagem está dentro de um contentor 'gallery-item'. -->
    <div class="galeria-grid">
        <div class="galeria-item">
            <img src="imagens/corte1.JPG">
        </div>
        <div class="galeria-item">
            <img src="imagens/foto2.JPG">
        </div>
        <div class="galeria-item">
            <img src="imagens/foto3.JPG">
        </div>
        <div class="galeria-item">
            <img src="imagens/foto1.JPG" alt="Detalhe da decoração da barbearia.">
        </div>
        <div class="galeria-item">
            <img src="imagens/placasouto.JPG" alt="Vista do interior da Souto Barbearia.">
        </div>
        <div class="galeria-item">
            <img src="imagens/foto4.JPG" alt="Cadeira de barbeiro em destaque.">
        </div>
        <div class="galeria-item">
            <img src="imagens/logo.JPG" alt="Vista do interior da Souto Barbearia.">
        </div>
        <div class="galeria-item">
            <img src="imagens/barbeiro.jpg" alt="Cadeira de barbeiro em destaque.">
        </div>
    </div>
</main>

<!-- Secção do Instagram -->
<section class="instagram-section">
    <h2>Segue-nos no Instagram</h2>
    <p>Vê os nossos últimos trabalhos e novidades em <a href="https://www.instagram.com/soutobarbearia_/" target="_blank">@soutobarbearia_</a></p>    
</section>

<?php
// O rodapé da galeria é minimalista, sem os scripts de animação
$footer_style = 'minimal';
require_once 'templates/footer.php';
?>
