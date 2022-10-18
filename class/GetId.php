<?php
class GetId
{
	private $login;
	private $password;
	private $url;
	private $params;
	
	 public function __construct($login, $password, $url)
    {
        $this->login = $login;
		$this->password = md5($password);
		$this->url = $url;
		$this->params = array('login' => $this->login, 'password' => $this->password);
    }
	
	public function sid() {
		$result = file_get_contents($this->url, false, stream_context_create(array('http' => array('method'  => 'POST','header'  => 'Content-type: application/x-www-form-urlencoded','content' => http_build_query($this->params)))));
		$array = json_decode($result,true); 
        if(@$array['result'])
        {
            return  $array['result'];
        } else {
            return false;
        }
		}
}
?>