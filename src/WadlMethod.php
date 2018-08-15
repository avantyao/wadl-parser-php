<?php
namespace WADL;

class WadlMethod extends WadlObject
{
	const NAME_DELETE = 'DELETE';
	const NAME_GET = 'GET';
	const NAME_HEAD = 'HEAD';
	const NAME_POST = 'POST';
	const NAME_PUT = 'PUT';

	protected $application; 
	
	// by reference
    protected $href = '';
	
    // by definition
	protected $request;
	protected $responses = array();
	
    protected $id;
	protected $name;
	
	private $isChildrenBound = false;
	
    public static function loadAll($xmlMethods)
    {
        $methods = array();
        foreach ($xmlMethods as $xmlMethod) {
        	$method = self::load($xmlMethod);
            $methods[] = $method;
        }
        return $methods;
    }
    
    public static function load($xmlMethod)
    {
        $method = new self();
        $method->href = (string)$xmlMethod['href'];
        if (!empty($method->href)) {
            return $method;
        }
        
        $method->id = (string)$xmlMethod['id'];
        $method->name = (string)$xmlMethod['name'];
        
        if (!empty($xmlMethod->request)) {
        	$requests = WadlRequest::loadAll($xmlMethod->request);
        	if (count($requests) !== 1) {
        		throw new Exception('Bad request size: ' . count($requests));
        	}
            $method->request = $requests[0];
        }
        if (!empty($xmlMethod->response)) {
            $method->responses = WadlResponse::loadAll($xmlMethod->response);
        }
        return $method;
    }
    
    // override
    public function bindChildren(WadlApplication $application)
    {
    	if (!$this->isChildrenBound) {
	    	if ($this->request) {
	            $this->request->bindChildren($application);
	        }
	        foreach ($this->responses as $response) {
	            $response->bindChildren($application);
	        }
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
        $responses = array_map($toArrayFunc, $this->responses);
        if ($this->request) {
        	$request = $this->request->toArray();
        } else {
        	$request = null;
        }
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'request' => $request,
            'responses' => $responses
        );
    }
}