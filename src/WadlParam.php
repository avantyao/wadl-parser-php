<?php
namespace WADL;

class WadlParam extends WadlObject
{
	const STYLE_PLAIN = 'plain';
	const STYLE_QUERY = 'query';
	const STYLE_MATRIX = 'matrix';
	const STYLE_HEADER = 'header';
	const STYLE_TEMPLATE = 'template';
	
	// by reference
	protected $href;
	
	// by definition
    protected $options = array();
    protected $link = null; // todo: support link
	
	protected $name;
	protected $style;
	protected $id;
	protected $type;
	protected $default;
	protected $path;
	protected $required;
	protected $repeating;
	protected $fixed;
	
    public static function loadAll($xmlParams)
    {
    	$params = array();
    	foreach ($xmlParams as $xmlParam) {
    		$params[] = self::load($xmlParam);
    	}
    	return $params;
    }
    
    public static function load($xmlParam)
    {
    	$param = new self();
    	$param->href = (string)$xmlParam['href'];
    	if (!empty($param->href)) {
    		return $param;
    	}
    	
    	$param->name = (string)$xmlParam['name'];
    	if (empty($param->name)) {
    		throw new Exception('No name provided for param');
    	}
    	$param->style = (string)$xmlParam['style'];
    	$param->id = (string)$xmlParam['id'];
    	$param->type = (string)$xmlParam['type'];
    	$param->default = (string)$xmlParam['default'];
    	$param->path = (string)$xmlParam['path'];
    	$param->required = (string)$xmlParam['required'] === 'true';
    	$param->repeating = (string)$xmlParam['repeating'] === 'true';
    	$param->fixed = (string)$xmlParam['fixed'];
    	
    	if (!empty($xmlParam->option)) {
    		$options = array();
    		foreach ($xmlParam->option as $xmlOption) {
                $options[] = (string)$xmlOption['value']; 
    		}
    		$param->options = $options;
    	}
    	return $param;
    }
    
    // override
    public static function bindAll($params, WadlApplication $application)
    {
        $result = array();
        foreach ($params as $param) {
            $param = $param->bind($application);
            $result[$param->name] = $param;
        }
        return $result;
    }
    
    // override
    public function getReferenceId()
    {
        return $this->href;
    }

    public function toArray()
    {
        return array(
            'options' => $this->options,
            'name' => $this->name,
            'style' => $this->style,
            'id' => $this->id,
            'type' => $this->type,
            'default' => $this->default,
            'path' => $this->path,
            'required' => $this->required,
            'repeating' => $this->repeating,
            'fixed' => $this->fixed
        );
    }
}