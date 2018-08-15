<?php
namespace WADL;

class WadlResponse extends WadlObject
{
    protected $params = array();
    protected $representations = array();
    
    protected $status;
    
    private $isChildrenBound = false;
    
    public static function loadAll($xmlResponses)
    {
        $responses = array();
        foreach ($xmlResponses as $xmlResponse) {
            $responses[] = self::load($xmlResponse);
        }
        return $responses;
    }
    public static function load($xmlResponse)
    {
        $response = new self();
        $response->status = (int)$xmlResponse['status'];
        
        if (!empty($xmlResponse->param)) {
            $response->params = WadlParam::loadAll($xmlResponse->param);
        }
        if (!empty($xmlResponse->representation)) {
            $response->representations = WadlRepresentation::loadAll($xmlResponse->representation);
        }
        return $response;
    }
    
    // override
    public function bindChildren(WadlApplication $application)
    {
        if (!$this->isChildrenBound) {
            $this->params = WadlParam::bindAll($this->params, $application);
            $this->representations = WadlRepresentation::bindAll($this->representations, $application);
            $this->isChildrenBound = true;
        }
        return $this;
    }
    
    public function toArray()
    {
        $toArrayFunc = create_function('$e', 'return $e->toArray();');
        $params = array_map($toArrayFunc, $this->params);
        $representations = array_map($toArrayFunc, $this->representations);
        return array(
            'status' => $this->status,
            'params' => $params,
            'representations' => $representations
        );
    }
}