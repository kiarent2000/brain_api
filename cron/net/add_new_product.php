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
        
        $item_details=$array['result'];  
        
        if(!$item_details['is_archive'])
        {

        if($item_details['description'])
        {
            $description = $item_details['description'];
        } else {
            $description = $item_details['brief_description'];
        }   

        $item = array(
            'model' => $item_details['articul'],
            'sku' => $product['product_id'],
            'ean' => $item_details['EAN'],
            'quantity' => 7,
            'stock_status_id' => 6,
            'image' => $item_details['full_image'],
            'price' => $item_details['price'],
            'retail_price' => $item_details['retail_price_uah'],
            'country' => $item_details['country'],
            'manufacturer_id' => 1,
            
            
            'product_category' => 412,
            
            'product_images' => array('image1', 'image2', 'image3'),
            
            'product_descriptions' => array(
                array(
                    'language_id' => 3,
                    'name' => $item_details['name'],
                    'description' => $description
                )
            ),
            
            'product_attributes' => array(
                array(
                    'attribute_id'=>1,
                    'language_id' => 1,
                    'name' => 'name_rus',
                    'text' => 'description_rus'
                ),
                array(
                    'attribute_id'=>1,
                    'language_id' => 3,
                    'name' => 'name_ua',
                    'text' => 'description_ua'
                )
            ),
            
            'product_filters' => array(1, 2),
            
            );
        
        }

        $new_item = new Item($item);

        $result =  (new AddProduct($dbh))->add($new_item);
        if($result===200){echo "Продукт успешно добавлен!";}else{print_r($result);}

       // echo $array['result']['articul'].'<br>';
        
        //print_r($array);
    }
} 
    catch (Exception $ex) {
    (new Logger($dbh))->add($ex->getMessage(), 'load_xml_file');
        echo $ex->getMessage();
}
?>