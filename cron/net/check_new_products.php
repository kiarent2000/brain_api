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
    $categories=array(1392, 1561, 1261, 1333, 7327, 1379, 7328, 7329, 7967, 7331, 7980, 1293, 1037, 1369, 1564, 1390, 7906, 7907, 7908, 7909, 1402, 7353, 7354, 8253, 1094, 1359, 7332, 7333, 7334, 7383, 7390, 7384, 7391, 7392, 7394, 7393, 7395, 7398, 7397, 7396, 7385, 7386, 7387, 7388, 7389, 8254, 8350, 8351, 8352, 8463, 8672, 8715, 8716, 8717, 8747, 1562, 1563, 1419, 1434, 1423, 8932, 8942, 8925, 8941);

     $products=array();

    $source = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/brain_api/my_class.xml');
    $source_products=new SimpleXMLElement($source);

    foreach ($source_products->xpath('//product') as $source_product) {        
        if(in_array($source_product['CategoryID'][0], $categories)) 
        {
            $products[]=$source_product['ProductID']->__toString(); 
        }
    }

    foreach($products as $product)
    {
        $product_id=(new CheckProduct($product))->check($dbh); //проверяем наличие продукта по полю 'sku'
        if(!$product_id) (new AddNewProduct($dbh))->add(intval($product));        
    }
} 
    catch (Exception $ex) {
    (new Logger($dbh))->add($ex->getMessage(), 'load_xml_file');
        echo $ex->getMessage();
}
?>