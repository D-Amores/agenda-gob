<link rel="stylesheet" href="{{ asset('css/footer.css') }}">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

<footer class="custom-footer">
  <div class="container">
    <div class="row align-items-center text-center text-md-start">
      <!-- Logo -->
      <div class="col-12 col-md-auto mb-2 mb-md-0">
        <i class='bx bx-shield-quarter footer-logo'></i>
      </div>

      <!-- Texto -->
      <div class="col">
        <div class="d-flex flex-column flex-md-row justify-content-center align-items-center gap-2">
          <span class="secretaria-name">Secretaría Anticorrupción y Buen Gobierno</span>
          <span class="divider d-none d-md-inline">|</span>
          <span class="copyright">
            © <script>document.write(new Date().getFullYear())</script> Sistema de Gestión de Agenda y Eventos
          </span>
        </div>
      </div>
    </div>
  </div>

  <!-- Efecto de partículas -->
  <div class="particles" id="particles"></div>
</footer>

<script>
  // Crear partículas para el efecto de fondo
  document.addEventListener('DOMContentLoaded', function() {
    const particlesContainer = document.getElementById('particles');
    if (particlesContainer) {
      const particleCount = 20;

      for (let i = 0; i < particleCount; i++) {
        const particle = document.createElement('div');
        particle.classList.add('particle');

        // Tamaño y posición aleatorios
        const size = Math.random() * 5 + 2;
        const posX = Math.random() * 100;
        const delay = Math.random() * 15;

        particle.style.width = `${size}px`;
        particle.style.height = `${size}px`;
        particle.style.left = `${posX}%`;
        particle.style.top = `${Math.random() * 100}%`;
        particle.style.animationDelay = `${delay}s`;

        particlesContainer.appendChild(particle);
      }
    }
  });
</script>
