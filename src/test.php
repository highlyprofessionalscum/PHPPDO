<?php
if (!($loader = @include __DIR__.'/../vendor/autoload.php')) {
    echo <<<'EOT'
You need to install the project dependencies using Composer:
$ wget http://getcomposer.org/composer.phar
OR
$ curl -s https://getcomposer.org/installer | php
$ php composer.phar install --dev
$ phpunit
EOT;
    exit(1);
}

use PhpPdo\PhpPdo;
header('Content-Type: text/html; charset=utf-8');

odbc_close_all();
$pdo = new PhpPdo('pdo3', 'user', 'secret');

$res = $pdo->prepare('select * from test;');
$res->execute();
$r = $res->fetchAll(PhpPdo::FETCH_BOTH);
var_dump($r);
