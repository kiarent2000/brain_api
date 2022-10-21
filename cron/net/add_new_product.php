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
        
        $url='http://api.brain.com.ua/product_pictures/'.$product['product_id'].'/'.$sid.'?lang=ua&full=1';
        $result_brain = file_get_contents($url);
        $brain_images = json_decode($result_brain,true);
        
        
        $images=array();


        foreach ($brain_images['result'] as $brain_image)
        {
             if(basename($brain_image['full_image'])!=="no-photo-api.png")
            {
                $images[]=$brain_image['full_image']; 				
            } else if (($brain_image['large_image'])!=="no-photo-api.png")
				{
                $images[]=$brain_image['large_image'];
            } else {
				$images[]=$brain_image['medium_image'];
			}		
        }

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

        $url='http://api.brain.com.ua/product_options/'.$product['product_id'].'/'.$sid.'?lang=ua&full=1';
        $result_brain = file_get_contents($url);
        $brain_attributes = json_decode($result_brain,true); 
		
        $item_attrs=$brain_attributes['result'];
        $options=array();
        $brain_filters=array();

        foreach($item_attrs as $item_attr)
        {           
            $options[]=array($item_attr['OptionName']=>$item_attr['ValueName']);
            $brain_filters[]=$item_attr['FilterName'];
        }

        $attributes=array();
        $language_id=3;
        $attribute_group_id=370;


        foreach($options as $option)
        {
            foreach($option as $key=>$value)
            {
                $result = (new CheckAttribute($key, $language_id))->check($dbh);
            
                if(!$result)
                {
                    $new_attr = (new AddAttribute($dbh))->add($attribute_group_id, $language_id, $key);  
                    if($new_attr)
                    {
                        $attributes[]=array('attribute_id'=>$new_attr, 'text'=>$value, 'language_id' => $language_id,
                        'name' => $key);
                    }         
                } else {        
                    $attributes[]=array('attribute_id'=>$result, 'text'=>$value, 'language_id' => $language_id,
                    'name' => $key);
                } 
            }
        }


        $filter_group_id=2;

        $filters=array();


        foreach($brain_filters as $value)
        {
            $result = (new CheckFilter($value, $language_id))->check($dbh);
            
            if(!$result)
            {
                $new_filter = (new AddFilter($dbh))->add($filter_group_id, $language_id, $value);  
                if($new_filter)
                {
                    $filters[]=$new_filter;
                }         
            } else {        
                $filters[]=$result;
            }  
        }

        $item = array(
            'model' => $item_details['articul'],
            'sku' => $product['product_id'],
            'ean' => $item_details['EAN'],
            'quantity' => 7,
            'stock_status_id' => 6,
            'image' => $images[0],
            'price' => $item_details['price'],
            'retail_price' => $item_details['retail_price_uah'],
            'country' => $item_details['country'],
            'manufacturer_id' => 1,
            
            
            'product_category' => $item_details['categoryID'],
            
            'product_images' => $images,
            
            'product_descriptions' => array(
                array(
                    'language_id' => 3,
                    'name' => $item_details['name'],
                    'description' => $description
                )
            ),
            'product_attributes' => $attributes,
            
            
           'product_filters' => $filters,
            
            );
        
        }

        $new_item = new Item($item);

        $result =  (new AddProduct($dbh))->add($new_item);
        if($result===200){
            (new AddNewProduct($dbh))->delete($product['product_id']);
            echo "Продукт успешно добавлен!";
        } else {
            print_r($result);
        }

    }
} 
    catch (Exception $ex) {
    (new Logger($dbh))->add($ex->getMessage(), 'load_xml_file');
        echo $ex->getMessage();
}
?>