<?php

function url($path = '') {
    return BASE_URL . ltrim($path, '/');
}

// Dans config.php
define('BASE_URL', '/');