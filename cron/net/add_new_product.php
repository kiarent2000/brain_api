<?php 
###########Скрипт для получения актуального файла выгрузки
declare(strict_types=1);

spl_autoload_register(function ($class) {
    include $_SERVER['DOCUMENT_ROOT'].'/brain_api/class/'.$class.'.php';
});

include($_SERVER['DOCUMENT_ROOT'].'/brain_api/config.php');
$dbh = DB::getInstance()->connect();

try
{
    ############### Получение  идентификатора сессии ################
    $sid = (new GetId(LOGIN, PASSWORD, URL_AUTH))->sid(); 
    if(empty($sid))	{
           throw new Exception('Пустой идентификатор сессии!');
    }

    $products=(new GetNewProducts())->check($dbh); 

    foreach($products as $product)
    {
         $url='http://api.brain.com.ua/product/'.$product['product_id'].'/'.$sid.'?lang=ua&full=1';
        $result_brain = file_get_contents($url);
        $array = json_decode($result_brain,true); 
        print_r($array);
    }
} 
    catch (Exception $ex) {
    (new Logger($dbh))->add($ex->getMessage(), 'load_xml_file');
        echo $ex->getMessage();
}
?>