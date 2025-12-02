<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AutoMec - Soluciones Mecánicas</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lora&family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/fontawesome/css/all.min.css">
  <style>
    :root {
      --bg-light: #ffffff;
      --primary: #0077cc;
      --secondary: #e0f0ff;
      --text-dark: #333333;
      --gray: #888;
      --font-main: 'Montserrat', sans-serif;
      --font-serif: 'Lora', serif;
    }
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: var(--font-main);
      color: var(--text-dark);
      background-color: var(--bg-light);
    }
    a {
      text-decoration: none;
      color: var(--text-dark);
      transition: color 0.3s ease;
    }
    a:hover {
      color: var(--primary);
    }
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
      background-color: var(--secondary);
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .logo {
      font-size: 1.5rem;
      font-weight: bold;
      color: var(--primary);
    }
    nav {
      display: flex;
      gap: 1.5rem;
    }
    .nav-links {
      display: flex;
      gap: 1.5rem;
    }
    .hamburger {
      display: none;
      font-size: 1.5rem;
      cursor: pointer;
    }
    .hero {
      background: url('https://source.unsplash.com/1600x900/?auto-mechanic') no-repeat center center/cover;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      text-align: center;
      padding: 2rem;
      color: #fff;
    }
    .hero-text {
      background-color: rgba(0, 119, 204, 0.7);
      padding: 2rem;
      border-radius: 12px;
      transition: all 0.3s ease;
    }
    .hero-text:hover {
      transform: scale(1.05);
      background-color: rgba(0, 119, 204, 0.9);
    }
    .hero h1 {
      font-size: 3rem;
      font-family: var(--font-serif);
    }
    .services, .features, .contact {
      padding: 4rem 2rem;
      max-width: 1200px;
      margin: auto;
    }
    .services h2, .features h2, .contact h2 {
      text-align: center;
      margin-bottom: 2rem;
      font-size: 2rem;
    }
    .service-blocks {
      display: flex;
      flex-wrap: wrap;
      gap: 2rem;
      justify-content: center;
    }
    .service {
      background-color: var(--secondary);
      padding: 2rem;
      border-radius: 12px;
      width: 300px;
      text-align: center;
      opacity: 0;
      transform: translateY(20px);
      transition: opacity 0.6s ease, transform 0.6s ease;
    }
    .service.visible {
      opacity: 1;
      transform: translateY(0);
    }
    .service i {
      font-size: 2rem;
      color: var(--primary);
      margin-bottom: 1rem;
    }
    .features ul {
      list-style: none;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1.5rem;
    }
    .features li {
      background-color: var(--secondary);
      padding: 1rem;
      border-radius: 10px;
      display: flex;
      align-items: center;
      gap: 1rem;
      transition: background-color 0.3s ease;
    }
    .features li:hover {
      background-color: #cbe6ff;
    }
    .features i {
      color: var(--primary);
    }
    form {
      display: flex;
      flex-direction: column;
      gap: 1rem;
      max-width: 600px;
      margin: auto;
    }
    input, textarea {
      padding: 1rem;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 1rem;
    }
    button {
      background-color: var(--primary);
      color: #fff;
      padding: 1rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
      transition: transform 0.2s ease;
    }
    button:hover {
      transform: scale(1.05);
    }
    footer {
      background-color: var(--secondary);
      padding: 2rem;
      text-align: center;
      font-size: 0.9rem;
    }
    .social-icons a {
      margin: 0 0.5rem;
      color: var(--gray);
    }
    .social-icons a:hover {
      color: var(--primary);
    }
    @media (max-width: 768px) {
      nav {
        display: none;
        flex-direction: column;
        background-color: var(--secondary);
        position: absolute;
        top: 60px;
        right: 0;
        width: 200px;
        padding: 1rem;
      }
      nav.active {
        display: flex;
      }
      .hamburger {
        display: block;
      }
    }
  </style>
</head>
<body>
  <header>
    <div class="logo">AutoMec</div>
    <div class="hamburger" onclick="toggleMenu()"><i class="fas fa-bars"></i></div>
    <nav id="nav-menu" class="nav-links">
      <a href="#">Inicio</a>
      <a href="#servicios">Servicios</a>
      <a href="#caracteristicas">Ventajas</a>
      <a href="#contacto">Contacto</a>
    </nav>
  </header>

  <section class="hero">
    <div class="hero-text">
      <h1>Expertos en mantenimiento y reparación automotriz</h1>
    </div>
  </section>

  <section class="services" id="servicios">
    <h2>Servicios</h2>
    <div class="service-blocks">
      <div class="service"><i class="fas fa-tools"></i><p>Mantenimiento general</p></div>
      <div class="service"><i class="fas fa-car-crash"></i><p>Reparación de colisiones</p></div>
      <div class="service"><i class="fas fa-oil-can"></i><p>Cambio de aceite y fluidos</p></div>
    </div>
  </section>

  <section class="features" id="caracteristicas">
    <h2>¿Por qué elegirnos?</h2>
    <ul>
      <li><i class="fas fa-check-circle"></i> Diagnóstico computarizado</li>
      <li><i class="fas fa-check-circle"></i> Personal certificado</li>
      <li><i class="fas fa-check-circle"></i> Garantía en todos nuestros servicios</li>
      <li><i class="fas fa-check-circle"></i> Atención rápida y personalizada</li>
    </ul>
  </section>

  <section class="contact" id="contacto">
    <h2>Contáctanos</h2>
    <form onsubmit="return validateForm()">
      <input type="text" id="nombre" placeholder="Tu nombre" required>
      <input type="email" id="correo" placeholder="Tu correo" required>
      <textarea id="mensaje" rows="5" placeholder="Tu mensaje" required></textarea>
      <button type="submit">Enviar</button>
    </form>
  </section>

  <footer>
    <p>Síguenos en redes sociales:</p>
    <div class="social-icons">
      <a href="#"><i class="fab fa-facebook"></i></a>
      <a href="#"><i class="fab fa-twitter"></i></a>
      <a href="#"><i class="fab fa-instagram"></i></a>
    </div>
    <p>© 2025 AutoMec. Todos los derechos reservados.</p>
  </footer>

  <script>
    function toggleMenu() {
      document.getElementById('nav-menu').classList.toggle('active');
    }

    function validateForm() {
      const nombre = document.getElementById('nombre').value.trim();
      const correo = document.getElementById('correo').value.trim();
      const mensaje = document.getElementById('mensaje').value.trim();

      if (!nombre || !correo || !mensaje) {
        alert('Por favor, completa todos los campos.');
        return false;
      }
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(correo)) {
        alert('Por favor, ingresa un correo válido.');
        return false;
      }
      alert('Formulario enviado correctamente.');
      return true;
    }

    window.addEventListener('scroll', () => {
      document.querySelectorAll('.service').forEach(el => {
        const rect = el.getBoundingClientRect();
        if (rect.top < window.innerHeight - 100) {
          el.classList.add('visible');
        }
      });
    });
  </script>
</body>
</html>