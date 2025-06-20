<?php

// Laravel .env

const PATH_TO_ENV = '.env';

$config = file_get_contents(PATH_TO_ENV);

$config = preg_replace('=APP_URL\=http://localhost=', 'APP_URL="https://localhost:${_NGINX_PORT_HTTPS}"', $config);

$config = preg_replace('=DB_CONNECTION\=sqlite=', 'DB_CONNECTION="${DB_CONNECTION}"', $config);
$config = preg_replace('=# DB_HOST\=127\.0\.0\.1=', 'DB_HOST="${DB_HOST}"', $config);
$config = preg_replace('=# DB_PORT\=3306=', 'DB_PORT="${DB_PORT}"', $config);
$config = preg_replace('=# DB_DATABASE\=laravel=', 'DB_DATABASE="${DB_DATABASE}"', $config);
$config = preg_replace('=# DB_USERNAME\=root=', 'DB_USERNAME="${DB_USERNAME}"', $config);
$config = preg_replace('=# DB_PASSWORD\==', 'DB_PASSWORD="${DB_PASSWORD}"', $config);

file_put_contents(PATH_TO_ENV, $config);

// package.json

$packageJsonContent = json_decode(file_get_contents('package.json'), true);
$packageJsonContent['scripts']['dev'] = 'vite --host --port=${VITE_PORT}';
file_put_contents('package.json', json_encode($packageJsonContent, JSON_PRETTY_PRINT));

// vite.config.js

$viteConfigJsContent = file_get_contents('vite.config.js');
$viteConfigJsContent = preg_replace(
    '=(export default defineConfig\(\{)=',
    '$1' . <<<SERVER

    server: {
        https: {
            cert: '/etc/nginx/certs/localhost-cert.pem',
            key: '/etc/nginx/certs/localhost-key.pem',
        },
    },
SERVER
,
    $viteConfigJsContent
);
file_put_contents('vite.config.js', $viteConfigJsContent);
