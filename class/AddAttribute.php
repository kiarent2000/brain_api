<?php 
class AddAttribute
{
    public function __construct($dbh)
    {
      $this->dbh=$dbh; 
    }    
    
    public  function add($attribute_group_id, $language_id, $name) : int
    {
        $sql = 'INSERT INTO `'. DB_PREFIX.'_attribute` SET `attribute_group_id` = '.$attribute_group_id;
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
		if($sth->errorInfo()[2]){ 
            return false;
        } else {
            $attribute_id=$this->dbh->lastInsertId();
            $description = $this->addDescription($attribute_id, $language_id, $name);
            if($description){
                return $attribute_id;
            } else {
                return false;
            }                 
        }
    }

    private function addDescription($attribute_id, $language_id, $name): bool
    {
        $sql = 'INSERT INTO `'. DB_PREFIX.'_attribute_description` SET `attribute_id` = '.$attribute_id.', `language_id`='.$language_id.', `name`=:name';
        $sth = $this->dbh->prepare($sql);
        $sth->bindValue(':name', $name, PDO::PARAM_STR);  
        $sth->execute();  
        if($sth->errorInfo()[2]){ 
            return false;
        } else {
            return true;     
        }
    } 


}
