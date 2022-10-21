<?php
class CheckFilter
{
    public function __construct($name, $language_id)
    {
        $this->name=$name;
        $this->language_id=$language_id;
    }

    public function check($dbh)
    {
        $sql = 'SELECT `filter_id` FROM `'. DB_PREFIX.'_filter_description` WHERE `language_id` = '.$this->language_id.' AND `name`=:name';
        $sth = $dbh->prepare($sql);
        $sth->bindValue(':name', $this->name, PDO::PARAM_STR);
        $sth->execute();
       
        $result = $sth->fetch();
        if($result)
        {
            return $result['filter_id'];
        } else {
            return false;
        }
    }
}