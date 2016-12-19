<?php
namespace DocParser\Internal;

abstract class BasicXMLObject
{
	private $strict = true;
	private $spec = null;
	
	abstract public function validate(XMLSpec &$spec = NULL);
	abstract public function printXML(XMLSpec &$spec = NULL);
	
	function __construct()
	{
	    $this->setSpec(createXMLSpec("XHTML"));
	}
	
	protected function setSpec(XMLSpec &$spec)
	{
		$this->spec = $spec;
	}
	
	protected function setStrict($strict)
	{
		$this->strict = $strict;
	}

	public function getSpec()
	{
		return $this->spec;
	}

	public function getStrict()
	{
		return $this->strict;
	}	
}
?>
