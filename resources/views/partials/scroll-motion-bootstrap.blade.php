<script>
  if ('IntersectionObserver' in window &&
      (!window.matchMedia || !window.matchMedia('(prefers-reduced-motion: reduce)').matches)) {
    document.documentElement.classList.add('eq-scroll-motion');
    window.eqMotionFallbackTimer = setTimeout(function () {
      window.eqMotionFallbackTriggered = true;
      document.documentElement.classList.remove('eq-scroll-motion');
    }, 2000);
  }
</script>
