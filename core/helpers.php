<?php
function sanitize($data)
{
    return htmlspecialchars(trim((string)($data ?? '')), ENT_QUOTES, 'UTF-8');
}
function redirect($url) {
    header("Location: $url");
    exit;
}

function formatDate($date) {
    return date('d M Y', strtotime($date));
}

function formatTime($time) {
    return date('h:i A', strtotime($time));
}

function getStatusBadgeClass($status) {
    $classes = [
        'pending' => 'warning',
        'confirmed' => 'info',
        'completed' => 'success',
        'cancelled' => 'danger'
    ];
    return $classes[$status] ?? 'secondary';
}