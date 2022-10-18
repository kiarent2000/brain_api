<?php 
declare(strict_types=1);

spl_autoload_register(function ($class) {
    include __DIR__ . '/class/'.$class.'.php';
});

include(__DIR__.'/config.php');
$dbh = DB::getInstance()->connect();

try
{



} 
    catch (Exception $ex) {
    echo $ex->getMessage();
}


?>