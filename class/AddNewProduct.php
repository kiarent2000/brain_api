<?php 
class AddNewProduct
{
    public function __construct($dbh)
    {
      $this->dbh=$dbh; 
    }   
    
    public function add($id)
    {
        $sql = 'REPLACE INTO `new_products` SET `product_id` = '.$id;
        $sth = $this->dbh->prepare($sql); 
        $sth->execute();
    }   

    public function delete($id)
    {
        $sql = 'DELETE FROM `new_products` WHERE `product_id` = '.$id;
        $sth = $this->dbh->prepare($sql); 
        $sth->execute();
    }
}