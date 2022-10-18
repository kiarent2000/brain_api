<?php
class DB
{
    private static $instances = [];
	protected function __construct() { }
	protected function __clone() { }
	public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

	public static function getInstance(): DB
    {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

	public function connect()
	{
		return new PDO('mysql:dbname='.DB_USERNAME.';charset=UTF8;host='.DB_HOSTNAME, DB_USERNAME, DB_PASSWORD);
	}
}	
