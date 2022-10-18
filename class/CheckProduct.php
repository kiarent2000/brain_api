<?php
class CheckProduct
{
    public function __construct($sku)
    {
        $this->sku=$sku;
    }

    public function check($dbh)
    {
        $sql = 'SELECT `product_id` FROM `'. DB_PREFIX.'_product` WHERE `sku` = '.$this->sku;
        $sth = $dbh->query($sql);
        $result = $sth->fetch();
        if($result)
        {
            return $result['product_id'];
        } else {
            return false;
        }
    }
}