<?php
namespace DocParser\Internal;

class AttrObject extends BasicXMLObject
{
	private $name;
	private $value;

	function __construct($name, $value, $strict = NULL, XMLSpec &$spec = NULL)
	{
		parent::__construct();
		if($strict) $this->setSpec($spec);
		if($spec) $this->setStrict($strict);
		$this->name = ($this->getStrict() ? $this->getSpec()->validateAttrName($name) : $name);
		$this->value = ($this->getStrict() ? $this->getSpec()->ValidateAttrValue($this->name, $value) : $value);
	}

	public function validate(XMLSpec &$spec = NULL)
	{
	    if(!$spec) $spec = $this->getSpec();
		return $spec->validateAttrName($this->name) &&  $spec->validateAttrValue($this->name, $this->value);
	}
	
	public function printXML(XMLSpec &$spec = NULL)
	{
		if(!$spec) $spec = $this->getSpec();
		echo $this->name . '="' . $this->value . '"';
	}
}
?>