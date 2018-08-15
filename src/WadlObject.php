<?php
namespace WADL;

class WadlObject extends DynamicObject
{
	private $_reference;
	
    public static function bindAll($objects, WadlApplication $application)
    {
        $result = array();
        foreach ($objects as $key => $object) {
            $result[$key] = $object->bind($application);;
        }
        return $result;
    }
	public function bind(WadlApplication $application)
	{
        if ($this->isReference()) {
            return $this->getReference($application, get_class($this));
		} else {
			$this->bindChildren($application);
			return $this;
		}
	}
	// vitual
	public function bindChildren(WadlApplication $application)
	{
		return $this;
	}
    public function isReference()
    {
    	$refId = $this->getReferenceId();
        return !empty($refId);   	
    }
    public function getReferenceId()
    {
        return null;
    }
    public function getReference(WadlApplication $application, $class)
    {
    	if ($this->_reference === null) {
	        $refs = explode('#', $this->getReferenceId());
	        if (count($refs) !== 2) {
	            throw new Exception('Bad reference id of ' . $class);
	        }
	        list($file, $id) = $refs;
	        if (trim($file) == '') {
	            $file = $application->basename;
	        }
	        $this->_reference = WadlElementResolver::getInstance()->resolve($class, $file, $id);
    	}
    	return $this->_reference;
    }
}