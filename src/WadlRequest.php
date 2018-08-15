<?php
namespace WADL;

class WadlRequest extends WadlObject
{
	protected $params = array();
	protected $representations = array();

	private $isChildrenBound = false;
	
    public static function loadAll($xmlRequests)
    {
        $requests = array();
        foreach ($xmlRequests as $xmlRequest) {
            $requests[] = self::load($xmlRequest);
        }
        return $requests;
    }
    public static function load($xmlRequest)
    {
        $request = new self();
            
        if (!empty($xmlRequest->param)) {
            $request->params = WadlParam::loadAll($xmlRequest->param);
        }
        if (!empty($xmlRequest->representation)) {
            $request->representations = WadlRepresentation::loadAll($xmlRequest->representation);
        }
        return $request;
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
            'params' => $params,
            'representations' => $representations
        );
    }
}