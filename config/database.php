<?php

/* To use this you must install vlucas/spot2 */
/* $ composer require vlucas/spot2 */

$container = $app->getContainer();

$container["spot"] = function ($container) {

    $config = new \Spot\Config();
    $mysql = $config->addConnection("mysql", [
        "dbname" => getenv("DB_NAME"),
        "user" => getenv("DB_USER"),
        "password" => getenv("DB_PASSWORD"),
        "host" => getenv("DB_HOST"),
        "driver" => "pdo_mysql",
        "charset" => "utf8"
    ]);

    $spot = new \Spot\Locator($config);

    $logger = new Doctrine\DBAL\Logging\MonologSQLLogger($container["logger"]);
    $mysql->getConfiguration()->setSQLLogger($logger);

    return $spot;
};
