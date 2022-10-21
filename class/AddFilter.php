<?php 
class AddFilter
{
    public function __construct($dbh)
    {
      $this->dbh=$dbh; 
    }    
    
    public  function add($filter_group_id, $language_id, $name) : int
    {
        $sql = 'INSERT INTO `'. DB_PREFIX.'_filter` SET `filter_group_id` = '.$filter_group_id;
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
		if($sth->errorInfo()[2]){ 
            return false;
        } else {
            $filter_id=$this->dbh->lastInsertId();
            $description = $this->addDescription($filter_id, $language_id, $name, $filter_group_id);
            if($description){
                return $filter_id;
            } else {
                return false;
            }                 
        }
    }

    private function addDescription($filter_id, $language_id, $name, $filter_group_id): bool
    {
        $sql = 'INSERT INTO `'. DB_PREFIX.'_filter_description` SET `filter_id` = '.$filter_id.', `language_id`='.$language_id.', `name`=:name, `filter_group_id` = '.$filter_group_id;
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
