<?php
require_once('../configuration/configuration.php');

$name = $_REQUEST['user'];
$url = "http://api.ereality.ru/" . API_KEY . "/pinfo/?h_name=";

$getter = new Getter($name, $url);
echo json_encode($getter->getItems());

class Getter {
	private static $categorys = array(50, 53);
	
	private $url = null;
	
	public function __construct($name, $apiURL) {
		$this->url = $this->prepareUrl($name, $apiURL);
	}
	
	public function getItems()
	{
		$user = unserialize(file_get_contents($this->url));

		if (empty($user)) {
			return array();
		}
		return $items = $this->filter($user['inv']);
	}
	
	private function filter ($inventory) {
		$items = array();
		
		foreach ($inventory as $itemInInvetory) {
			if (in_array($itemInInvetory['w_category'], self::$categorys)) {
				$itemInInvetory['w_name'] = $this->convertFromErType($itemInInvetory['w_name']);
				$items[] = $itemInInvetory;
			}
		}
		
		return $items;
	} 
	
	private function prepareUrl($name, $apiURL) {
	
		return $apiURL . $this->convertToErType($name);
	} 
	
	private function convertToErType($string) {
		return iconv("UTF-8", "windows-1251", $string);
	}
	
	private function convertFromErType($string) {
		return iconv("windows-1251", "UTF-8", $string);
	}
}
