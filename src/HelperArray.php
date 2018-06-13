<?php

namespace CakeWeb;

class HelperArray
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
	public static function foreachArray(array &$array, callable $callback, int $depth = 0): void
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

	public static function arrayClone(array $array): array
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

	/**
	* array_merge_recursive does indeed merge arrays, but it converts values with duplicate
	* keys to arrays rather than overwriting the value in the first array with the duplicate
	* value in the second array, as array_merge does. i.e., with array_merge_recursive,
	* this happens (documented behavior):
	*
	* array_merge_recursive(['key' => 'org value'], ['key' => 'new value']);
	*     => ['key' => ['org value', 'new value']];
	*
	* mergeRecursive does not change the datatypes of the values in the arrays.
	* Matching keys' values in the second array overwrite those in the first array, as is the
	* case with array_merge, i.e.:
	*
	* mergeRecursive(['key' => 'org value'], ['key' => 'new value']);
	*     => ['key' => ['new value']];
	*
	* Parameters are passed by reference, though only for performance reasons. They're not
	* altered by this function.
	*
	* @param array $array1
	* @param array $array2
	* @return array
	* @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
	* @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
	*/
	public static function mergeRecursive(array &$array1, array &$array2): array
	{
		$merged = $array1;
		foreach($array2 as $key => &$value)
		{
			$merged[$key] = (is_array($value) && isset($merged[$key]) && is_array($merged[$key]))
				? self::mergeRecursive($merged[$key], $value)
				: $value;
		}
		return $merged;
	}
}