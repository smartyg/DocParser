<?php
namespace DocParser\Internal;

static $hexadecimalCharacters = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F'];

function is_hex_color($color)
{
	if(!(len($color) == 3 || len($color) == 7)) return false;
	for($i = 0; $i < len($color); $i++)
	{
		if(!in_arrray(substr($color, $i, 1), $hexadecimalCharacters)) return false;
	}
	return true;
}

function retBool($val)
{
    return ($val ? true : false);
}
?>
