<?php
namespace Docparser\DTD;

class DTD
{
	const _DTD_ALL = 'a';
	const _DTD_CACHED = __DIR__ . "/Cache";
	const _DTD_PREDEFINED = __DIR__;

	static public function readDTD($name)
	{
		$json_dtd = null;
		if(($filename = self::exsistDTD($name)))
		{
			$json_string = file_get_contents("$filename");
			if(is_string($json_string)) $json_dtd = json_decode($json_string, true);
		}
		return $json_dtd;
	}

	static public function exsistDTD($name, $dir = self::_DTD_ALL)
	{
		if($dir == self::_DTD_ALL)
		{
			foreach(array(self::_DTD_CACHED, self::_DTD_PREDEFINED) as $dir)
				if(($f = self::exsistDTD($name, $dir))) return $f;
		}
		elseif(file_exists(($f = $dir . "/" . $name . ".json"))) return $f;
		else return false;
	}

	static public function saveToCache($name, array $dtd)
	{
		if(!is_string($name) || !is_array($dtd)) return false;
		$json_string = json_encode($dtd);
		$file = self::_DTD_CACHED . "/" . $name . ".json";
		if(!($fp = fopen($file, 'w'))) throw new \Exception("unable to open file for writing: " . $file);
		if(!fwrite($fp, $json_string))
		{
			fclose($fp);
			throw new \Exception("unable to write to file: " . $file);
		}
		return fclose($fp);
	}
}
?>