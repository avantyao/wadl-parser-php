<?php
namespace WADL;

class WadlResource extends WadlObject
{
    protected $params = array();
    protected $methods = array();
    protected $resources = array();
    
    protected $type;
    protected $path;
    protected $id;
    protected $queryType;
    
    protected $uri;
    
    private $isChildrenBound = false;
	
    public static function loadAll($xmlResources)
    {
    	$resources = array();
    	foreach ($xmlResources as $xmlResource) {
    		$resources[] = self::load($xmlResource);
    	}
    	return $resources;
    }
    public static function load($xmlResource)
    {
        $resource = new self();
        $resource->type = (string)$xmlResource['type'];
        $resource->path = (string)$xmlResource['path'];
        $resource->id = (string)$xmlResource['id'];
        $resource->queryType = (string)$xmlResource['queryType'];
            
        if (!empty($xmlResource->param)) {
            $resource->params = WadlParam::loadAll($xmlResource->param);
        }
        if (!empty($xmlResource->method)) {
            $resource->methods = WadlMethod::loadAll($xmlResource->method);
        }
        if (!empty($xmlResource->resource)) {
            $resource->resources = self::loadAll($xmlResource->resource);
        }
    	return $resource;
    }
    
    // depth clone
    public function __clone()
    {
        $reources = array();
        foreach ($this->resources as $resource) {
            $reources[] = clone $resource;
        }
        $this->resources = $reources;
    }
    
    // override
    public function bind(WadlApplication $application)
    {
        if ($this->isReference()) {
            $reference = $this->getReference($application, 'WadlResourceType');
            if (empty($this->id) && !empty($reference->id)) {
            	$this->id = $reference->id;
            }
            $this->params = $reference->params;
            $this->methods = $reference->methods;
            foreach ($reference->resources as $key => $resource) {
            	$this->resources[$key] = clone $resource;
            	// echo "clone: " . $resource . " => " . $this->resources[$key] . "\n";
            }
        } else {
            $this->bindChildren($application);
        }
        return $this;
    }
    
    // override
    public function bindChildren(WadlApplication $application)
    {
        if (!$this->isChildrenBound) {
            $this->params = WadlParam::bindAll($this->params, $application);
            $this->methods = WadlMethod::bindAll($this->methods, $application);
            $this->resources = WadlResource::bindAll($this->resources, $application);

            $this->isChildrenBound = true;
        }
        return $this;
    }
    
    // override
    public function getReferenceId()
    {
        return $this->type;
    }
    
    public function buildResourceUri($parentUri)
    {
        $this->uri = $parentUri . $this->path;
        foreach ($this->resources as $resource) {
        	$resource->buildResourceUri($this->uri . '/');
        }
    }
    
    public function toArray()
    {
        $toArrayFunc = create_function('$e', 'return $e->toArray();');
        $params = array_map($toArrayFunc, $this->params);
        $methods = array_map($toArrayFunc, $this->methods);
        $resources = array_map($toArrayFunc, $this->resources);
        return array(
            'uri' => $this->uri,
            'path' => $this->path,
            'id' => $this->id,
            'params' => $params,
            'methods' => $methods,
            'resources' => $resources
        );
    }
}