<?php
namespace WADL;

class WadlRepresentation extends WadlObject
{
	// by reference
    protected $href;
    
    // by definition
    protected $params = array();
    
    protected $id;
    protected $element;
    protected $mediaType;
    protected $profile;
    
    private $isChildrenBound = false;
    
    public static function loadAll($xmlRepresentations)
    {
        $representations = array();
        foreach ($xmlRepresentations as $xmlRepresentation) {
            $representations[] = self::load($xmlRepresentation);
        }
        return $representations;
    }
    
    public static function load($xmlRepresentation)
    {
        $representation = new self();
        $representation->href = (string)$xmlRepresentation['href'];
        if (!empty($representation->href)) {
            
            return $representation;
        }
        
        $representation->id = (string)$xmlRepresentation['id'];
        $representation->element = (string)$xmlRepresentation['element'];
        $representation->mediaType = (string)$xmlRepresentation['mediaType'];
        $representation->profile = (string)$xmlRepresentation['profile'];
        
        if (!empty($xmlRepresentation->param)) {
            $representation->params = WadlParam::loadAll($xmlRepresentation->param);
        }
        return $representation;
    }
    
    // override
    public function bindChildren(WadlApplication $application)
    {
    	if (!$this->isChildrenBound) {
            $this->params = WadlParam::bindAll($this->params, $application);
            $this->isChildrenBound = true;
        }
        
        return $this;
    }
        
    // override
    public function getReferenceId()
    {
        return $this->href;
    }
    
    public function toArray()
    {
        $toArrayFunc = create_function('$e', 'return $e->toArray();');
        $params = array_map($toArrayFunc, $this->params);
        return array(
            'id' => $this->id,
            'element' => $this->element,
            'mediaType' => $this->mediaType,
            'profile' => $this->profile,
            'params' => $params,
        );
    }
}