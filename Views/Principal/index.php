<?php include_once 'Views/Template/header-principal.php'; ?>



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
                        <a href="<?php echo BASE_URL; ?>Principal/vistaCertificados" class="btn btn-ms-primary text-white">
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
                            <a href="<?php echo BASE_URL; ?>Principal/vistaCertificados" class="btn btn-ms-primary text-white">
                                Ir a búsqueda de certificados
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 order-1 order-lg-2">

                    <img src="<?php echo BASE_URL; ?>assets/images/EC.jpg" class="ms-search-img" alt="Pruebas de emisiones certificadas">
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
<!-- 
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
</section>-->

<?php include_once 'Views/Template/footer-principal.php'; ?>