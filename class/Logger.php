<?php 
class Logger
{
    public function __construct($dbh)
    {
      $this->dbh=$dbh; 
    }    
    
    public  function add($message, $type) : int
    {
        $sql = 'INSERT INTO `brain_api_log` SET `message` = "'.$message.'", `type`="'.$type.'", `date_added`="'.date("Y-m-d").'"';
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
		$id=$this->dbh->lastInsertId();
        return $id;
    }


    public  function clean() : bool
    {
        $sql = 'DELETE FROM `brain_api_log` WHERE `date_added`< DATE_SUB(NOW(), INTERVAL 1 MONTH)';
        $sth = $this->dbh->prepare($sql);
        $sth->execute();
		$id=$this->dbh->lastInsertId();
        return true;
    }
}
?>