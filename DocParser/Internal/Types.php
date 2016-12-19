<?php
namespace DocParser\Internal;

class Types
{
	const _XMLSPEC_NULL = 0x0;

	const _XMLSPEC_VALUE_NUMERIC = 0x1;
	const _XMLSPEC_VALUE_BOOL = 0x2;
	const _XMLSPEC_VALUE_ONE = 0x3;
	const _XMLSPEC_VALUE_ZERO = 0x4;
	const _XMLSPEC_VALUE_NULL = 0x5;
	const _XMLSPEC_VALUE_TEXT = 0x6;
	const _XMLSPEC_VALUE_COLOR = 0x7;
	const _XMLSPEC_VALUE_CUSTOM = 0x8;
	const _XMLSPEC_VALUE_MAX = 0x9;

	const _XMLSPEC_PROP_ATTRS = 0x1;
	const _XMLSPEC_PROP_SUBS = 0x2;
	const _XMLSPEC_PROP_EXPL_CLOSE = 0x3;
	const _XMLSPEC_PROP_VALUES = 0x4;
	const _XMLSPEC_PROP_MIXED = 0x5;
	const _XMLSPEC_PROP_TYPE = 0x6;
	const _XMLSPEC_PROP_ALL = 0xF;

	const _XMLSPEC_ATTR_TYPE = 0x1;
	const _XMLSPEC_ATTR_VALUES = 0x2;
}
?>
