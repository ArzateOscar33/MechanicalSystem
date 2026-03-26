<?php include_once 'Views/Template/header-principal.php'; ?>

<style>
    :root {
        --ms-primary: #0f4c81;
        --ms-primary-dark: #0b365b;
        --ms-accent: #2ea44f;
        --ms-accent-soft: #eaf7ef;
        --ms-warning: #f4b400;
        --ms-dark: #1f2937;
        --ms-text: #374151;
        --ms-muted: #6b7280;
        --ms-bg: #f7fafc;
        --ms-white: #ffffff;
        --ms-soft-blue: #eef6fb;
        --ms-soft-gray: #f3f6f8;
        --ms-border: rgba(15, 76, 129, 0.10);
        --ms-shadow: 0 18px 45px rgba(15, 76, 129, 0.08);
        --ms-radius: 22px;
    }

    body {
        background: linear-gradient(180deg, #f8fbfd 0%, #f3f7fa 100%);
        color: var(--ms-text);
    }

    .ms-hero {
        position: relative;
        overflow: hidden;
        background:
            linear-gradient(120deg, rgba(255, 255, 255, .84), rgba(255, 255, 255, .72)),
            url('Assets/images/portal_clientes/hero-taller.jpg') center/cover no-repeat;
        min-height: 86vh;
        display: flex;
        align-items: center;
        border-bottom: 1px solid rgba(15, 76, 129, .08);
    }

    .ms-hero::before {
        content: "";
        position: absolute;
        top: -120px;
        right: -120px;
        width: 360px;
        height: 360px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(46, 164, 79, .12) 0%, rgba(46, 164, 79, 0) 70%);
        pointer-events: none;
    }

    .ms-hero::after {
        content: "";
        position: absolute;
        bottom: -160px;
        left: -120px;
        width: 420px;
        height: 420px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(15, 76, 129, .10) 0%, rgba(15, 76, 129, 0) 70%);
        pointer-events: none;
    }

    .ms-hero-content {
        position: relative;
        z-index: 2;
    }

    .ms-badge-hero {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(15, 76, 129, .08);
        color: var(--ms-primary);
        border: 1px solid rgba(15, 76, 129, .12);
        padding: .60rem 1rem;
        border-radius: 999px;
        font-size: .92rem;
        font-weight: 700;
        margin-bottom: 1.2rem;
    }

    .ms-hero h1 {
        font-size: clamp(2.3rem, 5vw, 4.4rem);
        font-weight: 800;
        line-height: 1.05;
        color: var(--ms-dark);
        margin-bottom: 1rem;
    }

    .ms-hero p {
        font-size: 1.08rem;
        line-height: 1.8;
        color: var(--ms-text);
        max-width: 670px;
        margin-bottom: 1.7rem;
    }

    .ms-hero-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .btn-ms-primary {
        background: linear-gradient(135deg, var(--ms-primary), var(--ms-primary-dark));
        color: #fff;
        border: none;
        border-radius: 14px;
        padding: .95rem 1.5rem;
        font-weight: 700;
        box-shadow: 0 12px 24px rgba(15, 76, 129, .20);
    }

    .btn-ms-primary:hover {
        color: #fff;
        transform: translateY(-1px);
    }

    .btn-ms-soft {
        background: #fff;
        color: var(--ms-primary);
        border: 1px solid rgba(15, 76, 129, .15);
        border-radius: 14px;
        padding: .95rem 1.5rem;
        font-weight: 700;
        box-shadow: 0 10px 22px rgba(15, 76, 129, .06);
    }

    .btn-ms-soft:hover {
        color: var(--ms-primary-dark);
        background: #f8fcff;
    }

    .ms-hero-panel {
        position: relative;
        z-index: 2;
        background: rgba(255, 255, 255, .88);
        border: 1px solid rgba(15, 76, 129, .10);
        border-radius: 24px;
        padding: 1.6rem;
        box-shadow: 0 22px 50px rgba(15, 76, 129, .10);
        backdrop-filter: blur(6px);
    }

    .ms-hero-panel h5 {
        color: var(--ms-dark);
        font-weight: 800;
        margin-bottom: .9rem;
    }

    .ms-hero-panel ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    .ms-hero-panel li {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        padding: .7rem 0;
        border-bottom: 1px solid rgba(15, 76, 129, .08);
        color: var(--ms-text);
    }

    .ms-hero-panel li:last-child {
        border-bottom: none;
    }

    .ms-section {
        padding: 88px 0;
    }

    .ms-section-title {
        font-size: clamp(1.9rem, 3vw, 2.8rem);
        font-weight: 800;
        color: var(--ms-dark);
        margin-bottom: .8rem;
    }

    .ms-section-text {
        max-width: 760px;
        margin: 0 auto;
        color: var(--ms-muted);
        font-size: 1.03rem;
        line-height: 1.8;
    }

    .ms-card {
        height: 100%;
        background: #fff;
        border: 1px solid var(--ms-border);
        border-radius: var(--ms-radius);
        padding: 1.7rem;
        box-shadow: var(--ms-shadow);
        transition: .25s ease;
    }

    .ms-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 24px 45px rgba(15, 76, 129, 0.10);
    }

    .ms-icon {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, rgba(15, 76, 129, .10), rgba(46, 164, 79, .10));
        color: var(--ms-primary);
        font-size: 1.35rem;
        margin-bottom: 1rem;
    }

    .ms-card h5 {
        font-weight: 800;
        color: var(--ms-dark);
        margin-bottom: .75rem;
    }

    .ms-card p {
        color: var(--ms-muted);
        margin-bottom: 0;
        line-height: 1.75;
    }

    .ms-search-box {
        background: linear-gradient(135deg, #ffffff, #f6fbff);
        border: 1px solid rgba(15, 76, 129, .10);
        border-radius: 28px;
        box-shadow: 0 25px 55px rgba(15, 76, 129, .08);
        overflow: hidden;
    }

    .ms-search-content {
        padding: 2.3rem;
    }

    .ms-search-content h3 {
        font-size: clamp(1.8rem, 3vw, 2.5rem);
        font-weight: 800;
        color: var(--ms-dark);
        margin-bottom: 1rem;
    }

    .ms-search-content p {
        color: var(--ms-muted);
        line-height: 1.8;
    }

    .ms-search-img {
        width: 100%;
        height: 100%;
        min-height: 420px;
        object-fit: cover;
    }

    .ms-info-strip {
        background: linear-gradient(135deg, #0f4c81, #13619f);
        color: #fff;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 20px 45px rgba(15, 76, 129, .16);
    }

    .ms-info-item {
        text-align: center;
        padding: 1.7rem 1rem;
    }

    .ms-info-item h4 {
        font-weight: 800;
        margin-bottom: .4rem;
        font-size: 1.45rem;
    }

    .ms-info-item p {
        margin: 0;
        color: rgba(255, 255, 255, .86);
    }

    .ms-mvv-card {
        background: #fff;
        border-radius: 24px;
        border: 1px solid rgba(15, 76, 129, .10);
        box-shadow: 0 18px 40px rgba(15, 76, 129, .07);
        padding: 2rem;
        height: 100%;
    }

    .ms-mvv-card h4 {
        font-weight: 800;
        color: var(--ms-dark);
        margin-bottom: 1rem;
    }

    .ms-mvv-card p,
    .ms-mvv-card li {
        color: var(--ms-muted);
        line-height: 1.8;
    }

    .ms-values {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .ms-values li {
        display: flex;
        gap: 10px;
        margin-bottom: .8rem;
        align-items: flex-start;
    }

    .ms-contact-box {
        background: #fff;
        border-radius: 26px;
        border: 1px solid rgba(15, 76, 129, .10);
        box-shadow: 0 22px 50px rgba(15, 76, 129, .08);
        overflow: hidden;
    }

    .ms-contact-info {
        background: linear-gradient(135deg, #f0f8ff, #e6f2fa);
        padding: 2rem;
        height: 100%;
    }

    .ms-contact-info h3 {
        font-weight: 800;
        color: var(--ms-dark);
        margin-bottom: 1rem;
    }

    .ms-contact-info p {
        color: var(--ms-text);
        line-height: 1.8;
    }

    .ms-contact-item {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        margin-bottom: 1rem;
        color: var(--ms-text);
    }

    .ms-contact-form {
        padding: 2rem;
        background: #fff;
    }

    .form-control,
    .form-select {
        min-height: 50px;
        border-radius: 14px;
        border: 1px solid rgba(15, 76, 129, .14);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: rgba(15, 76, 129, .35);
        box-shadow: 0 0 0 .2rem rgba(15, 76, 129, .10);
    }

    .ms-mini-note {
        color: var(--ms-muted);
        font-size: .95rem;
    }

    @media (max-width: 991.98px) {
        .ms-hero {
            min-height: auto;
            padding: 90px 0 70px;
        }

        .ms-search-img {
            min-height: 280px;
        }
    }
</style>

<!-- HERO -->
<section class="ms-hero" id="inicio">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="ms-hero-content">
                    <span class="ms-badge-hero">
                        <i class="fas fa-certificate"></i>
                        Portal oficial de certificados
                    </span>

                    <h1>Consulta, visualiza y descarga tu certificado de emisiones</h1>

                    <p>
                        En <strong>Mechanical Emissions Services</strong> realizamos <strong>pruebas de emisiones certificadas</strong>
                        cumpliendo los estándares ambientales con nuestro grupo de expertos. Nuestro equipo capacitado
                        utiliza tecnología avanzada para garantizar que su vehículo cumpla con todos los requerimientos
                        aplicables, brindando un servicio eficiente, confiable y profesional.
                    </p>

                    <div class="ms-hero-actions">
                        <a href="<?php echo BASE_URL; ?>BuscarCertificados" class="btn btn-ms-primary">
                            Buscar certificado
                        </a>
                        <a href="#nosotros" class="btn btn-ms-soft">
                            Conocer más
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="ms-hero-panel">
                    <h5>¿Qué puedes hacer en este portal?</h5>
                    <ul>
                        <li><i class="fas fa-check-circle text-success mt-1"></i> Buscar tu certificado de emisiones</li>
                        <li><i class="fas fa-check-circle text-success mt-1"></i> Visualizar el documento emitido</li>
                        <li><i class="fas fa-check-circle text-success mt-1"></i> Descargar tu certificado de forma segura</li>
                        <li><i class="fas fa-check-circle text-success mt-1"></i> Consultar información básica del servicio</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- PRESENTACION -->
<section class="ms-section" id="nosotros">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="ms-section-title">Pruebas de emisiones certificadas</h2>
            <p class="ms-section-text">
                Asegúrese de que su vehículo cumpla con los estándares ambientales en
                <strong>Mechanical Emissions Services</strong>. Ofrecemos inspecciones integrales
                para una conducción más limpia y segura, con un proceso enfocado en precisión técnica,
                cumplimiento y confianza para el cliente.
            </p>
        </div>

        <div class="ms-search-box">
            <div class="row g-0 align-items-stretch">
                <div class="col-lg-6 order-2 order-lg-1">
                    <div class="ms-search-content">
                        <h3>Una empresa especializada en control y certificación de emisiones</h3>
                        <p>
                            Somos una extensión del taller enfocada en dar al cliente una forma clara y accesible
                            de consultar su certificado. Nuestro compromiso es ofrecer procesos profesionales,
                            atención responsable y resultados respaldados por un trabajo técnico bien ejecutado.
                        </p>
                        <p>
                            Este portal está diseñado para ser simple: aquí el cliente puede localizar su certificado,
                            revisarlo en línea y descargarlo cuando lo necesite.
                        </p>

                        <div class="mt-4">
                            <a href="<?php echo BASE_URL; ?>BuscarCertificados" class="btn btn-ms-primary">
                                Ir a búsqueda de certificados
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 order-1 order-lg-2">
                    <img
                        src="Assets/images/portal_clientes/about-taller.jpg"
                        alt="Pruebas de emisiones certificadas"
                        class="ms-search-img">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- SERVICIOS -->
<section class="ms-section pt-0" id="servicios">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="ms-section-title">Lo que ofrecemos</h2>
            <p class="ms-section-text">
                Un servicio claro y especializado para clientes que necesitan consultar
                la documentación de sus pruebas de emisiones.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="ms-card">
                    <div class="ms-icon"><i class="fas fa-search"></i></div>
                    <h5>Búsqueda rápida de certificados</h5>
                    <p>Localiza tu certificado de emisiones mediante los datos de consulta definidos por el sistema.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="ms-card">
                    <div class="ms-icon"><i class="fas fa-file-pdf"></i></div>
                    <h5>Visualización del documento</h5>
                    <p>Consulta tu certificado directamente desde el portal con una experiencia clara y profesional.</p>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="ms-card">
                    <div class="ms-icon"><i class="fas fa-download"></i></div>
                    <h5>Descarga segura</h5>
                    <p>Descarga tu certificado cuando lo necesites para respaldo, trámite o consulta personal.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FRANJA DE CONFIANZA -->
<section class="ms-section py-5">
    <div class="container">
        <div class="ms-info-strip">
            <div class="row g-0">
                <div class="col-md-4">
                    <div class="ms-info-item">
                        <h4>Precisión</h4>
                        <p>Procesos técnicos realizados con cuidado y control.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="ms-info-item">
                        <h4>Cumplimiento</h4>
                        <p>Compromiso con estándares ambientales y documentación formal.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="ms-info-item">
                        <h4>Confianza</h4>
                        <p>Un portal sencillo para consultar certificados sin complicaciones.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MISION / VISION / VALORES -->
<section class="ms-section pt-0" id="empresa">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="ms-section-title">Nuestra identidad</h2>
            <p class="ms-section-text">
                Una empresa automotriz profesional dedicada a pruebas de emisiones certificadas,
                con una imagen moderna, seria y orientada a la confianza del cliente.
            </p>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="ms-mvv-card">
                    <h4>Misión</h4>
                    <p>
                        Brindar pruebas de emisiones certificadas con precisión, responsabilidad y tecnología adecuada,
                        ayudando a que cada vehículo cumpla con los estándares ambientales requeridos mediante un servicio
                        eficiente, confiable y profesional.
                    </p>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ms-mvv-card">
                    <h4>Visión</h4>
                    <p>
                        Ser una referencia en servicios de inspección y certificación de emisiones, destacando por la
                        calidad técnica, la transparencia en el proceso y la confianza que generamos en cada cliente.
                    </p>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="ms-mvv-card">
                    <h4>Valores</h4>
                    <ul class="ms-values">
                        <li><i class="fas fa-check-circle text-success mt-1"></i> Profesionalismo en cada servicio</li>
                        <li><i class="fas fa-check-circle text-success mt-1"></i> Honestidad en la atención al cliente</li>
                        <li><i class="fas fa-check-circle text-success mt-1"></i> Responsabilidad ambiental</li>
                        <li><i class="fas fa-check-circle text-success mt-1"></i> Precisión técnica en las pruebas</li>
                        <li><i class="fas fa-check-circle text-success mt-1"></i> Compromiso con la calidad</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CONTACTO -->
<section class="ms-section pt-0" id="contacto">
    <div class="container">
        <div class="ms-contact-box">
            <div class="row g-0">
                <div class="col-lg-5">
                    <div class="ms-contact-info">
                        <h3>Contáctanos</h3>
                        <p>
                            Si necesitas apoyo relacionado con tu certificado de emisiones,
                            puedes comunicarte con <strong>Mechanical Emissions Services</strong>.
                        </p>

                        <div class="ms-contact-item">
                            <i class="fas fa-map-marker-alt mt-1 text-primary"></i>
                            <div>
                                <strong>Dirección</strong><br>
                                Av. Cayetano Pérez No. 240<br>
                                Burócrata Ruiz Cortines C.P. 22406<br>
                                Tijuana, Baja California
                            </div>
                        </div>

                        <div class="ms-contact-item">
                            <i class="fas fa-phone-alt mt-1 text-primary"></i>
                            <div>
                                <strong>Teléfono</strong><br>
                                (664) 000 0000
                            </div>
                        </div>

                        <div class="ms-contact-item">
                            <i class="fas fa-envelope mt-1 text-primary"></i>
                            <div>
                                <strong>Correo</strong><br>
                                contacto@mecemissionsmx.com
                            </div>
                        </div>

                        <div class="ms-contact-item">
                            <i class="fas fa-clock mt-1 text-primary"></i>
                            <div>
                                <strong>Horario</strong><br>
                                Lunes a Viernes · 8:00 AM a 6:00 PM
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="ms-contact-form">
                        <h3 class="fw-bold mb-3">Solicitar información</h3>


                        <form action="#" method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre</label>
                                    <input type="text" class="form-control" placeholder="Tu nombre">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" placeholder="Tu teléfono">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Correo electrónico</label>
                                    <input type="email" class="form-control" placeholder="tu@correo.com">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Asunto</label>
                                    <select class="form-select">
                                        <option selected>Selecciona una opción</option>
                                        <option>Consulta de certificado</option>
                                        <option>Descarga de documento</option>
                                        <option>Información general</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Mensaje</label>
                                    <textarea class="form-control" rows="5" placeholder="Escribe tu mensaje"></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-ms-primary">
                                        Enviar solicitud
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include_once 'Views/Template/footer-principal.php'; ?>