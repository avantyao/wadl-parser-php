<?php

namespace WADL;

class DynamicObject
{
	private $properties;
	
    public function __isset($property) {
        return isset($this->$property);
    }
    public function __get($property) {
        $method = "get$property";
        if (method_exists($this, $method))
            return $this->$method();
        $result = $this->internalGet($property);
        if ($result !== false)
            return $result;
            
        if (in_array($property, $this->getProperties()) == false) 
            throw new Exception("get invalid property : $property");
        return $this->$property;
    }
    public function __set($property, $value) {
        $method = "set$property";
        if (method_exists($this, $method)) 
            return $this->$method($value);
        $result = $this->internalSet($property, $value);
        if ($result !== false)
            return $result;
            
        if (in_array($property, $this->getProperties()) == false)
            throw new Exception("set invalid property : $property");
        $this->$property = $value;
        return $this;
    }
    
    public function getProperties() {
        if ($this->properties === null) {
            $this->properties = array_keys($this->__dump());
        }
        return $this->properties;
    }	
    public function __dump() {
        $__dump = array();
        foreach ($this as $property => $value) {
            $__dump[$property] = $value;
        }
        return $__dump;
    }
    
    protected function internalGet($property) {
        return false;
    }
    protected function internalSet($property, $value) {
    	return false;
    }
    
    public function toJson()
    {
    	$json = '{';
    	foreach ($this->getProperties() as $prop) {
    		$json .= "$prop: {$this->$prop}, ";
    	}
    	$json .= '}';
        return $json;
    }
    public function __toString()
    {
        return $this->toJson();
    }
}
