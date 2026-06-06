<?php


define('APP_NAME', 'ClinicDesk');
define('APP_URL', 'http://localhost/clinicdesk/');
define('ITEMS_PER_PAGE', 10);


define('MAX_IMAGE_SIZE', 1048576);

define('MAX_PDF_SIZE', 3145728);

define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');


error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

