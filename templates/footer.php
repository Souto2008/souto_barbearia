<?php
/**
 * Ficheiro: footer.php
 *
 * Objetivo: Rodapé reutilizável para todas as páginas.
 *
 * Utilização:
 * Definir $footer_style = 'minimal' para um rodapé simplificado (sem scripts de animação).
 */

// Define o estilo do footer. O padrão é 'full'.
$footer_style = isset($footer_style) ? $footer_style : 'full';
?>
    <footer>
        <p class="copyright">&copy; <span id="current-year"></span> Souto Barbearia. Todos os direitos reservados.</p>
    </footer>

    <?php if ($footer_style == 'full'): ?>
        <!-- Adicionar a biblioteca Lenis para o scroll suave -->
        <script src="https://cdn.jsdelivr.net/gh/studio-freight/lenis@1.0.42/dist/lenis.min.js"></script>
        <!-- Adicionar a biblioteca ScrollReveal para animações ao rolar a página -->
        <script src="https://unpkg.com/scrollreveal"></script>
    <?php endif; ?>

    <!-- Ligar o nosso ficheiro JavaScript centralizado -->
    <script src="scripts.js" defer></script>
</body>
</html>