<!-- ============================================================
     FOOTER
     role="contentinfo" → landmark ARIA, buena práctica
     ============================================================ -->
<footer class="footer" role="contentinfo">
  <div class="container">
    <p>
      Diseñado y desarrollado por <strong>Matias McIntire</strong> &mdash;
      Curso Diseño y Desarrollo Web + IA &middot; <?= date('Y') ?>
    </p>
    <p>HTML5 &bull; CSS3 &bull; JavaScript &bull; PHP &bull; MySQL</p>
  </div>
</footer>

<!-- Toast de notificación (se muestra/oculta por JS) -->
<div id="toast" class="toast" role="status" aria-live="polite" aria-atomic="true"></div>

<!-- Bootstrap 5.3 JS bundle (incluye Popper, necesario para navbar collapse / dropdowns / modales) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- JavaScript principal -->
<script src="<?= isset($jsPath) ? $jsPath : '' ?>assets/js/main.js" defer></script>

<!-- Lucide: iconos de categorías -->
<script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
<script>lucide.createIcons();</script>

</body>
</html>
