<?php

namespace CakeWeb;

class Iterator
{
	private function __construct()
	{

	}

	// Exemplo de $callback:
	//	function(&$array, $depth) {
	//		if($depth > 2)
	//		{
	//			$array = null;
	//			return false; // se $array deixar de ser um array, deve-se retornar false
	//		}
	//		return true; // se $array continuar a ser um array, deve-se retornar true
	//	});
	public static function foreachArray(&$array, $callback, $depth = 0)
	{
		if($callback($array, $depth++))
		{
			foreach($array as $key => $value)
			{
				if(is_array($array[$key]))
				{
					self::foreachArray($array[$key], $callback, $depth);
				}
			}
		}
	}

	public static function arrayClone($array)
	{
		return array_map(function($element) {
			if(is_array($element))
			{
				return self::arrayClone($element);
			}
			elseif(is_object($element))
			{
				if($element instanceof \MongoDB\BSON\ObjectID)
				{
					return new \MongoDB\BSON\ObjectID((string)$element);
				}
				elseif($element instanceof \MongoDB\BSON\UTCDateTime)
				{
					return new \MongoDB\BSON\UTCDateTime((string)$element);
				}
				else
				{
					return clone $element;
				}
			}
			else
			{
				return $element;
			}
		}, $array);
	}
}