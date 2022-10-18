<?php
class GetNewProducts
{
    public function check($dbh)
    {
        $sql = 'SELECT `product_id` FROM `new_products` LIMIT 1';
        $sth = $dbh->query($sql);
        $result = $sth->fetchAll();        
        return $result;
    }
}