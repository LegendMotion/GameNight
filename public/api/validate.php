<?php
function validate_slug(string $slug): bool {
    return (bool) preg_match('/^[a-z0-9-]+$/', $slug);
}

function validate_gamecode(string $code): bool {
    return (bool) preg_match('/^[A-Z0-9]{4,10}$/', $code);
}

function validate_int(string $value, int $min, int $max) {
    if (!preg_match('/^\d+$/', $value)) {
        return false;
    }
    $int = (int) $value;
    return ($int >= $min && $int <= $max) ? $int : false;
}

function sanitize_field(string $value, int $maxLen) {
    $value = trim($value);
    $value = strip_tags($value);
    if ($value === '' || strlen($value) > $maxLen) {
        return false;
    }
    return $value;
}
?>
