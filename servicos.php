<?php
/**
 * Ficheiro: servicos.php
 * Objetivo: Página de listagem de serviços e preços.
 */

$page_title = 'Os Nossos Serviços';
$header_style = 'minimal';
$body_class = 'servicos-page';

require_once 'templates/header.php';
?>

<main class="servicos-main">
    <section class="servico-categoria">
        <h2>Cortes de Cabelo</h2>
        <div class="servicos-grid">
            <div class="servico-item">
                <h3>Corte com único pente</h3>
                <p>Um corte prático e uniforme, feito à máquina com um único pente para um look limpo e de fácil manutenção.</p>
                <p class="preco">6€</p>
            </div>
            <div class="servico-item">
                <h3>Corte Social</h3>
                <p>O clássico corte masculino, ideal para o dia a dia e ambientes profissionais. Versátil e sempre elegante.</p>
                <p class="preco">8€</p>
            </div>
            <div class="servico-item">
                <h3>Corte clássico com tesoura</h3>
                <p>Um trabalho detalhado feito inteiramente à tesoura, perfeito para quem busca um estilo mais tradicional e personalizado.</p>
                <p class="preco">9€</p>
            </div>
            <div class="servico-item">
                <h3>Corte com degradê</h3>
                <p>O corte moderno que cria uma transição suave entre diferentes comprimentos. Estilo e precisão num só corte.</p>
                <p class="preco">10€</p>
            </div>
        </div>
    </section>

    <section class="servico-categoria">
        <h2>Barba</h2>
        <div class="servicos-grid">
            <div class="servico-item">
                <h3>Corte com único pente</h3>
                <p>Para quem gosta de manter a barba curta e alinhada, este corte é feito à máquina para um comprimento uniforme.</p>
                <p class="preco">4€</p>
            </div>
            <div class="servico-item">
                <h3>Contornos com máquina</h3>
                <p>Definição e limpeza para a tua barba. Desenhamos os contornos com precisão para um visual mais arrumado.</p>
                <p class="preco">5€</p>
            </div>
            <div class="servico-item">
                <h3>Corte aparado com navalha e toalha</h3>
                <p>Uma experiência completa. Aparamos a barba e finalizamos com toalha quente e navalha para contornos perfeitos.</p>
                <p class="preco">7€</p>
            </div>
            <div class="servico-item">
                <h3>Corte tradicional navalhado</h3>
                <p>O ritual clássico do barbear com navalha, para uma pele lisa e um acabamento impecável. Inclui toalha quente.</p>
                <p class="preco">8€</p>
            </div>
        </div>
    </section>

    <section class="servico-categoria">
        <h2>Combos</h2>
        <div class="servicos-grid">
            <div class="servico-item">
                <h3>Corte com único pente + Barba único pente</h3>
                <p>A combinação perfeita para um look prático e cuidado. Cabelo e barba aparados de forma uniforme e rápida.</p>
                <p class="preco">8€</p>
            </div>
            <div class="servico-item">
                <h3>Conte social + Barba com navalha</h3>
                <p>O melhor de dois mundos: a elegância do corte social com o acabamento impecável da barba feita à navalha.</p>
                <p class="preco">11€</p>
            </div>
            <div class="servico-item">
                <h3>Corte degradê + Barba com máquina</h3>
                <p>Um visual moderno e alinhado. O degradê no cabelo complementado com uma barba bem definida à máquina.</p>
                <p class="preco">12€</p>
            </div>
            <div class="servico-item">
                <h3>Corte degradê + Barba com navalha</h3>
                <p>O serviço premium para um estilo impecável. Combina a técnica do degradê com o ritual relaxante da barba à navalha.</p>
                <p class="preco">13€</p>
            </div>
        </div>
    </section>
</main>

<?php
// O rodapé da página de serviços é minimalista
$footer_style = 'minimal';
require_once 'templates/footer.php';
?>