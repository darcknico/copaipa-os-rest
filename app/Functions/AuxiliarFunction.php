<?php

namespace App\Functions;

use Carbon\Carbon;

class AuxiliarFunction{

	public static function if_in_array($array, $object,$array_key="id",$object_key="id") 
	{
		foreach ($array as $item) {
			if(is_null($object_key)){
				if(is_null($array_key)){
					if ($item == $object) {
						return true;
					}
				} else {
					if (isset($item[$array_key]) and $item[$array_key] == $object) {
						return true;
					}
				}
				
			} else {
				if(is_null($array_key)){
					if (isset($object[$object_key]) and $item == $object[$object_key]) {
						return true;
					}
				} else {
					if(!isset($object[$object_key]) or !isset($item[$array_key]) ){
						return false;
					} else if ($item[$array_key] == $object[$object_key]) {
						return true;
					}
				}
			}
			
		}
		return false;
	}

	public static function get_in_array($array, $object,$array_key="id",$object_key="id") 
	{
		foreach ($array as $item) {
			if(is_null($object_key)){
				if ($item[$array_key] == $object) {
					return $item;
				}
			} else {
				if ($item[$array_key] == $object[$object_key]) {
					return $item;
				}
			}
			
		}
		return null;
	}

	public static function is_true($val, $return_null=false){
	    $boolval = ( is_string($val) ? filter_var($val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val );
	    return ( $boolval===null && !$return_null ? false : $boolval );
	}

}