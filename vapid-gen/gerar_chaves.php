<?php
require __DIR__ . '/vendor/autoload.php';

use Minishlink\WebPush\VAPID;

$keys = VAPID::createVapidKeys();

echo "Chave pública:\n" . $keys['publicKey'] . "\n\n";
echo "Chave privada:\n" . $keys['privateKey'] . "\n";
