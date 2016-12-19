<?php
namespace DocParser\Internal;

class TagObject extends BasicXMLObject
{
	private $name;
	private $attrs;
	private $subs;
	private $value;
  
	function __construct($name, array $attrs = NULL, $subs = NULL, $strict = NULL, XMLSpec &$spec = NULL)
	{
	    parent::__construct();
		if($strict) $this->setStrict($strict);
		if($spec) $this->setSpec($spec);

		$this->name = ($this->getStrict() ? $this->getSpec()->validateTagName($name) : $name);

		if(is_array($attrs))
		{
		    foreach($attrs as $attr)
		    {
			    if($this->getStrict())
				    $attr = $this->getSpec()->validateAttr($this->name, $attr);
			    if($attr) $this->attrs[] = $attr;
		    }
		}
		if(is_array($subs))
		{
		    $this->value = NULL;
		    foreach($subs as $sub)
		    {
			    if($this->getStrict())
				    $sub = $this->getSpec()->validateSub($this->name, $sub);
			    if($sub) $this->subs[] = $sub;
		    }
		}
		elseif(is_string($subs))
		{
		    $this->value = ($this->getStrict() ? $this->getSpec()->validateTagValue($this->name, $subs) : $subs);
		    $this->subs = NULL;
		}
	}

	public function validate(XMLSpec &$spec = NULL)	
	{
	    if(!$spec) $spec = $this->spec;
		if(!$spec->validateTagName($this->name)) return false;
		foreach($this->attrs as $attr)
			if(!$spec->validateAttr($this->name, $attr)) return false;
		foreach($this->subs as $sub)
			if(!$spec->validateSub($this->name, $sub)) return false;
		return true;
	}

	public function printXML(XMLSpec &$spec = NULL)
	{
		return $this->printHTML($spec);
	}
	
	public function printHTML(XMLSpec &$spec = NULL)
	{
	    if(!$spec) $spec = $this->getSpec();
		echo '<' . $this->name;
		if($this->attrs)
			foreach($this->attrs as $attr)
			{
			    echo ' ';
				$attr->printXML($strict, $spec);
			}
		if($this->subs || $spec->expliciteClose($this->name))
		{
			echo '>';
			if($this->subs)
			{
				foreach($this->subs as $sub)
					$sub->printXML($strict, $spec);
			}
			elseif($this->value)
			    echo $this->value;
			echo '</' . $this->name . '>';
		}
		else
			echo ' />';
	}
}
?>