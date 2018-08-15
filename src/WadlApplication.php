<?php

namespace WADL;

class WadlApplication extends DynamicObject
{
	protected $includeApplications = array();
	
	protected $resourceBase = '';
	protected $resources = array();
	protected $resourceTypes = array();
	protected $methods = array();
	protected $representations = array();
	protected $params = array();

	protected $filename;
    protected $basename;
	
    public static function load($filename)
    {
    	$xmlWadl = simplexml_load_file($filename);
    	return self::loadXml($xmlWadl, $filename);
    }
    public static function loadString($string)
    {
    	$xmlWadl = simplexml_load_string($string);
    	return self::loadXml($xmlWadl, 'noname.wadl');
    }
    public static function loadXml($xmlWadl, $filename)
    {	
        $application = new self();
        $application->filename = $filename;
        $application->basename = basename($filename);
        if (!empty($xmlWadl->resources)) {
            $application->resourceBase = (string)$xmlWadl->resources['base'];
            if (empty($xmlWadl->resources->resource)) {
            	throw new Exception('No resource found in resources');
            }
            $application->resources = WadlResource::loadAll($xmlWadl->resources->resource, $application);
        }
        
        $elementResolver = WadlElementResolver::getInstance();
        $basename = basename($filename);
        if (!empty($xmlWadl->resource_type)) {
        	$application->resourceTypes = WadlResourceType::loadAll($xmlWadl->resource_type, $application);
        	foreach ($application->resourceTypes as $resourceType) {
        		$elementResolver->register($basename, $resourceType);
        	}
        }
        if (!empty($xmlWadl->method)) {
        	$application->methods = WadlMethod::loadAll($xmlWadl->method, $application);
        	foreach ($application->methods as $method) {
        		$elementResolver->register($basename, $method);
        	}
        }
        if (!empty($xmlWadl->representation)) {
        	$application->representations = WadlRepresentation::loadAll($xmlWadl->representation, $application);
        	foreach ($application->representations as $representation) {
        		$elementResolver->register($basename, $representation);
        	}
        }
        if (!empty($xmlWadl->param)) {
        	$application->params = WadlParam::loadAll($xmlWadl->param, $application);
        	foreach ($application->params as $param) {
        		$elementResolver->register($basename, $param);
        	}
        }
        return $application;
    }
    
    public function import($filename) {
    	$this->includeApplications[] = self::load($filename);
    	return $this;
    }
    
    public function bind() {
    	foreach ($this->includeApplications as $application) {
    		$application->bind();
    	}
    	
    	// don't lazy binding params, representations, methods and reource_types
    	WadlParam::bindAll($this->params, $this);
    	WadlRepresentation::bindAll($this->representations, $this);
    	WadlMethod::bindAll($this->methods, $this);
    	WadlResourceType::bindAll($this->resourceTypes, $this);
    	
    	WadlResource::bindAll($this->resources, $this);
    	$this->buildResourceUri();
    	
    	return $this;
    }
    private function buildResourceUri()
    {
        foreach ($this->resources as $resource) {
            $resource->buildResourceUri($this->resourceBase);
        }
    }
}