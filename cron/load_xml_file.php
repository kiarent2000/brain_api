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
unlink($_SERVER['DOCUMENT_ROOT'].'/brain_api/my_class.xml');

############### Получение  идентификатора сессии ################
    $sid = (new GetId(LOGIN, PASSWORD, URL_AUTH))->sid(); 
    if(empty($sid))	{
           throw new Exception('Пустой идентификатор сессии!');
        }
############### Загрузка xml файла ################        
    
    $i=1;
    while($i<=6)
    {
        $url='http://api.brain.com.ua/pricelists/29/xml/'.$sid.'?lang=ua&full=1';
        $result_brain = file_get_contents($url);
        $array = json_decode($result_brain,true); 
        
        if(copy($array['url'], $_SERVER['DOCUMENT_ROOT'].'/brain_api/my_class.xml')) break;
                
        $i++;
        if($i>=5) throw new Exception('Не удалось скопировать файл c 5 попыток!');
        sleep(2);
    }
} 
    catch (Exception $ex) {
    (new Logger($dbh))->add($ex->getMessage(), 'load_xml_file');
        echo $ex->getMessage();
}
?>