<?php
function to_currency($number)
{
	$CI =& get_instance();
	$currency_symbol = $CI->config->item('currency_symbol') ? $CI->config->item('currency_symbol') : '$';
	if($number >= 0)
	{
		if($number*10000%10>0)
			return $currency_symbol.number_format($number, 4, '.', ',');
		else if($number*1000%10>0)
			return $currency_symbol.number_format($number, 3, '.', ',');
		else
			return $currency_symbol.number_format($number, 2, '.', ',');
    }
    else
    {
		if(-$number*10000%10>0)
			return '-'.$currency_symbol.number_format(abs($number), 4, '.', ',');
		else if(-$number*1000%10>0)
			return '-'.$currency_symbol.number_format(abs($number), 3, '.', ',');
		else
			return '-'.$currency_symbol.number_format(abs($number), 2, '.', ',');
    }
}


function to_currency_no_money($number)
{
	if($number*10000%10>0)
		return number_format($number, 4, '.', ',');
	else if($number*1000%10>0)
		return number_format($number, 3, '.', ',');
	else
		return number_format($number, 2, '.', ',');
}
?>