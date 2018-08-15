<?php
namespace WADL;

class WadlElementResolver
{
	private $references = array(
	   'WadlResourceType' => array(),
	   'WadlMethod' => array(),
	   'WadlRepresentation' => array(),
	   'WadlParam' => array()
	);
	
	protected static $_instance = null;
	
	/**
     * @return WadlElementResolver
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
	
	public function resolve($class, $file, $id) {
		if (!isset($this->references[$class][$file][$id])) {
			throw new Exception("No $class reference found for $file#$id");
		}
		return $this->references[$class][$file][$id];
	}
	
	public function register($file, $ref) {
		$class = get_class($ref);
		if (!array_key_exists($class, $this->references)) {
			throw new Exception('Unsupported type ' . $class);
		}
        if (empty($ref->id)) {
            throw new Exception("$class without id could not be registered as reference");
        }
		
		$this->references[$class][$file][$ref->id] = $ref;
	}

}