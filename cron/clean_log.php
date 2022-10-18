<?php 
###########Скрипт для получения актуального файла выгрузки
declare(strict_types=1);

spl_autoload_register(function ($class) {
    include $_SERVER['DOCUMENT_ROOT'].'/brain_api/class/'.$class.'.php';
});

include($_SERVER['DOCUMENT_ROOT'].'/brain_api/config.php');
$dbh = DB::getInstance()->connect();
(new Logger($dbh))->clean();
