<?php
// ======================================================
// CONFIGURACIÓN DINÁMICA DE BASE_URL
// ======================================================
$esHttps = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443)
);

$protocolo = $esHttps ? 'https://' : 'http://';

// Puede traer localhost, 192.168.x.x, 100.x.x.x, dominio, etc.
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';

// Carpeta del proyecto
$carpetaProyecto = '/MechanicalSystem/';

// BASE_URL dinámica
define('BASE_URL', $protocolo . $host . $carpetaProyecto);

// ======================================================
// BASE DE DATOS
// ======================================================
const HOST = "localhost";
const USER = "root";
const PASS = ""; //cambio de clave para casa, clave para trabajo @Osc4r4rz4t3
const DB = "mechanical_system";
const CHARSET = "charset=utf8";

// ======================================================
// GENERALES
// ======================================================
const TITLE = "Mechanical System";
const MONEDA = "USD";
const CLIENT_ID = "";

// ======================================================
// RUTAS FÍSICAS
// ======================================================
define('BASE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

// Si después ocupas uploads, puedes dejar también algo así:
// define('UPLOAD_ROOT', rtrim(dirname(__DIR__), "/\\"));

// ======================================================
// SMTP
// ======================================================

// --- SMTP empresarial (comentado por ahora) ---
// const USER_SMTP = "sistemas@pacificnort.com";
// const PASS_SMTP = "Pacific2025.";
// const PUERTO_SMTP = 465;
// const HOST_SMTP = "mailc75.carrierzone.com";

// --- SMTP Gmail (activo actualmente) ---
const USER_SMTP = "arzateoscar33@gmail.com";
const PASS_SMTP = "mrpf vzvo ckyh txhp";
const PUERTO_SMTP = 465;
const HOST_SMTP = "smtp.gmail.com";
