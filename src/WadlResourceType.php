<?php
namespace WADL;

class WadlResourceType extends WadlObject
{
    protected $params = array();
    protected $methods = array();
    protected $resources = array();
    
    protected $id;
    
    private $isChildrenBound = false;
	
    public static function loadAll($xmlResourceTypes)
    {
        $resourceTypes = array();
        foreach ($xmlResourceTypes as $xmlResourceType) {
            $resourceTypes[] = self::load($xmlResourceType);
        }
        return $resourceTypes;
    }
    public static function load($xmlResourceType)
    {
        $resourceType = new self();
        $resourceType->id = (string)$xmlResourceType['id'];
        
        if (!empty($xmlResourceType->param)) {
            $resourceType->params = WadlParam::loadAll($xmlResourceType->param);
        }
        if (!empty($xmlResourceType->method)) {
            $resourceType->methods = WadlMethod::loadAll($xmlResourceType->method);
        }
        if (!empty($xmlResourceType->resource)) {
            $resourceType->resources = WadlResource::loadAll($xmlResourceType->resource);
        }
        return $resourceType;
    }
    
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
}