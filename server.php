<?php

/**
 * Laravel - Um Framework PHP Para Artesãos da Web
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylor@laravel.com>
 */

$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Este arquivo nos permite emular a funcionalidade do "mod_rewrite" do Apache
// usando o servidor web embutido no PHP. Isso proporciona uma maneira conveniente
// de testar uma aplicação Laravel sem a necessidade de instalar um software de
// servidor web "real" aqui.
if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
    return false;
}

require_once __DIR__ . '/public/index.php';
