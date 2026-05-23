<?php
// إعدادات الموقع
define('APP_NAME', 'ClinicDesk');
define('APP_URL', 'http://localhost/clinicdesk/');
define('ITEMS_PER_PAGE', 10);

// إعدادات رفع الملفات
define('MAX_IMAGE_SIZE', 1048576); // 1MB
define('MAX_PDF_SIZE', 3145728);    // 3MB
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');

// ✅ شغّل عرض الأخطاء مؤقتاً عشان نشوف المشكلة
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// بدء الجلسة إذا لم تكن بدأت
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}