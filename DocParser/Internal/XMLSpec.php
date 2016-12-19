<?php
namespace DocParser\Internal;

use Exception;
use DocParser\Internal\Types as Type;
use DocParser\DTD\DTD as DTD;
use \Soothsilver\DtdParser\DTD as DTDParser;

if(!class_exists("\Soothsilver\DtdParser\DTD"))
	require_once("Soothsilver/SoothsilverDtdParser.php");

class XMLSpec
{
	const _SPLIT_STR = array('(', ')', '|', ',', '*', '?');

	private $spec = null;
	//private $dtd = null;

	/*
	list possible tags, read from config file when class in called (only once). Routines shoud go here, instances should have 0 to minimum code.
	
	tags[tag] = {type, list_extra_attr, list_extra_subs}
	
	type {list_tags, list_attr, value_allowed, list_subs, explicite_close}
	
	attrs[name] = {type (num, text, binary, one, zero, color, null, custom), values (array)};
    */
	protected $tags = array();

	protected $attrs = array();

	//protected $types = array();
	
	function __construct($spec)
	{
		if(self::specAvailible($spec)) $this->spec = $spec;

		if(strcmp(self::getCallerName(), 'DocParser\Internal\createXMLSpec') != 0)
			throw new Exception('Should be called by createXMLSpec');
		if(!self::specAvailible($spec))
			throw new Exception('Specified spec not availible');
 		$this->spec = $spec;

		if(($dtd = DTD::readDTD($spec)) == null)
			$dtd = self::parseDTD($spec, "./xhtml1-strict.dtd");
		$this->tags = $dtd['tags'];
		$this->attrs = $dtd['attrs'];
	}

	function __destruct()
	{
	}

	static public function parseDTD($name, $uri)
	{
		$tags = array();
		$attrs = array();
		$dtd_tree = DTDParser::parseText(file_get_contents("xhtml1-strict.dtd"));

		foreach($dtd_tree->elements as $value)
		{
			$tags[$value->type] = self::parseElement($value);
			$attrs = self::addAttrs($attrs, $value->attributes);
		}
		$dtd = array('tags' => $tags, 'attrs' => $attrs);
		DTD::saveToCache($name, $dtd);
		return $dtd;
	}

	static private function parseElement($value)
	{
		$r = array(
			TYPE::_XMLSPEC_PROP_ATTRS => array_keys($value->attributes),
			TYPE::_XMLSPEC_PROP_SUBS => self::parseSubs($value->contentSpecification),
			TYPE::_XMLSPEC_PROP_VALUES => null,
			TYPE::_XMLSPEC_PROP_MIXED => $value->mixed
		);
		return $r;
	}

	static private function parseSubs($value)
	{
		if($value == "EMPTY") return null;
		$parts = explode(' ', str_replace(self::_SPLIT_STR, ' ', $value));
		sort($parts);
		$i = 0;
		do
		{
			if($parts[$i] == "#PCDATA") $parts[$i] = Type::_XMLSPEC_VALUE_TEXT;
			if(empty($parts[$i]) || ($i > 0 && strcmp($parts[$i], $parts[$i - 1]) == 0)) array_splice($parts, $i, 1);
			else $i++;
		}
		while($i < count($parts));
		return $parts;
	}

	static private function addAttrs($list, array $values)
	{
		foreach($values as $attr)
		{
			if(array_key_exists($attr->name, $list)) continue;
			switch($attr->type)
			{
				case "##ENUMERATION_INTERNAL_IDENTIFIER##":
					$list[$attr->name] = array(
						TYPE::_XMLSPEC_ATTR_TYPE => Type::_XMLSPEC_VALUE_CUSTOM,
						TYPE::_XMLSPEC_ATTR_VALUES => $attr->enumeration
					);
					break;
				default:
					$list[$attr->name] = array(TYPE::_XMLSPEC_ATTR_TYPE => Type::_XMLSPEC_VALUE_TEXT);
					break;
			}
		}
		return $list;
	}

	/* public functions */

	final public function getSpec()
	{
		return $this->spec;
	}

	final public function validateTagName($name)
	{
		if(!is_string($name)) return false;
		return array_key_exists($name, $this->tags);
	}
	
	final public function validateTagValue($tag, $value)
	{
		if(!is_string($tag) || !isset($value)) return false;
		if(!$this->validateTagName($tag)) return false;
		return self::checkValues($value, $this->getTagProperty($tag, _XMLSPEC_PROP_VALUES));
	}

	final public function validateAttrName($name)
	{
	    if(!is_string($name)) return false;
		return array_key_exists($name, $this->attrs);
	}

	final public function validateAttrValue($name, $value)
	{
	    if(!is_string($name) || !isset($value)) return false;
		if($this->validateAttrName($name))
			return self::checkValues($value, $this->attrValue[$name]);
	    return false;
	}

	final public function validateAttr($tag, AttrObject &$attr)
	{
	    if(!is_string($tag) || !is_null($attr)) return false;
		$p = $this->getTagProperty($tag, self::_XMLSPEC_PROP_ATTRS);
		if(!is_array($p) || is_null($sub)) return false;
		$attr_name = $attr->getName();
		return in_array($attr_name, $p);
	}

	final public function validateSub($tag, TagObject &$sub)
	{
	    if(!is_string($tag) || !is_null($sub)) return false;
		$p = $this->getTagProperty($tag, self::_XMLSPEC_PROP_SUBS);
		if(!is_array($p) || is_null($sub)) return false;
		$sub_name = $sub->getName();
		return in_array($sub_name, $p);
	}

	final public function expliciteClose($tag)
	{/*
	    if(!is_string($tag)) return false;
		$p = $this->getTagProperty($tag, self::_XMLSPEC_PROP_EXPL_CLOSE);
		if(is_array($p) && exsists($p[0])) return retBool($p[0]);
		elseif(!is_array($p))
		return retBool($p);*/
		return true;
	}

	protected function getTagProperty($tag, $property = self::_XMLSPEC_PROP_ALL)
	{
		$r = null;
		if(!is_string($tag) || !array_key_exists($tag, $this->tags)) return $r;

		$tag = $this->tags[$tag];
		if(array_key_exists($property, $tag))
			$r = $tag[$propery];
		elseif($property == self::_XMLSPEC_PROP_ALL)
			$r = $tag;

		return $r;

	}

	/* static functions */

	final public static function specAvailible($spec)
	{
		return true;
	}

	final private static function getCallerName()
	{
		$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
		if(array_key_exists(2, $trace)) return $trace[2]['function'];
		return null;
	}

	private static function checkValues($value, array $values)
	{
	    if(!isset($value)) return false;
		if(!exsists($values['type']) || !exsists($values['values'])) return false;
		switch($values->type)
		{
			case Type::_XMLSPEC_TYPE_NUMERIC:
				return is_int($value);
			case Type::_XMLSPEC_TYPE_BOOL:
				return is_bool($value);
			case Type::_XMLSPEC_TYPE_ONE:
				return (isis_intnum($value) && $value == 1);
			case Type::_XMLSPEC_TYPE_ZERO:
				return (is_int($value) && $value == 0);
			case Type::_XMLSPEC_TYPE_NULL:
				return is_null($value);
			case Type::_XMLSPEC_TYPE_TEXT:
				return is_string($value);
			case Type::_XMLSPEC_TYPE_COLOR:
				return (is_string($value) && substr_compare($value, '#', 0, 1) == 0 && is_hex_color(substr($value, 1)));
			case Type::_XMLSPEC_TYPE_CUSTOM:
				return in_array($value, $values->values, FALSE);
		}
		return false;
	}
}

function createXMLSpec($spec)
{
	static $spec_list = array();

	if(!array_key_exists($spec, $spec_list))
		$spec_list[$spec] = new XMLSpec($spec);
	return $spec_list[$spec];
}

?>
