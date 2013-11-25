<?php

class WadlTest extends PHPUnit_Framework_TestCase
{
	public function setUp() {
        parent::setUp();
    }
    public function tearDown() {
        parent::tearDown();
    }
    
    public function testParseWadl()
    {
        $test = WadlApplication::load(dirname(__FILE__) . '/resources/test.wadl');
        $test->import(Config::getInstance()->imcWadl)->bind();
        $resources = $test->resources;
        //var_dump($resources);
        $this->assertEquals(count($resources), 3);
        $this->assertEquals($resources[0]->path, 'keyword/rank');
        $this->assertEquals($resources[0]->uri, 'http://github.com/avantyao/wadl-parser-php/keyword/rank');
        $this->assertEquals(count($resources[0]->methods), 2);
        $this->assertEquals($resources[0]->methods[0]->name, 'GET');
        
        $params = $resources[0]->methods[0]->request->params;
        $this->assertEquals(count($params), 5);
        // alt param from imc.wadl
        $this->assertEquals($params['alt']->style, 'query');
        $this->assertEquals($params['alt']->default, 'rss');
        $this->assertEquals(count($params['alt']->options), 2);
        $this->assertEquals($params['alt']->options[0], 'rss');
        $this->assertEquals($params['alt']->options[1], 'json');
        // clientid param from test.wadl
        $this->assertEquals($params['clientid']->style, 'query');
        $this->assertEquals($params['clientid']->default, 'allclientid');
        $this->assertEquals(count($params['clientid']->options), 4);
        $this->assertEquals($params['clientid']->options[0], 'allclientid');
        $this->assertEquals($params['clientid']->options[3], 'yahoo');
        
        $this->assertEquals($resources[0]->methods[0]->responses[0]->status, 200);
        
        $this->assertEquals($resources[0]->resources[0]->path, 'unpublished');
        $this->assertEquals($resources[0]->resources[0]->uri, 'http://github.com/avantyao/wadl-parser-php/keyword/rank/unpublished');
        $this->assertEquals($resources[0]->resources[0]->resources[0]->uri, 'http://github.com/avantyao/wadl-parser-php/keyword/rank/unpublished/{itemId}');
        
        $this->assertEquals($resources[1]->path, 'selfdefine');
        $this->assertEquals($resources[1]->uri, 'http://github.com/avantyao/wadl-parser-php/selfdefine');
        $this->assertEquals($resources[1]->resources[0]->path, '{itemId}');
        $this->assertEquals($resources[1]->resources[0]->uri, 'http://github.com/avantyao/wadl-parser-php/selfdefine/{itemId}');
        $this->assertEquals($resources[1]->methods[0]->name, 'GET');
        
        $this->assertEquals($resources[1]->resources[1]->path, 'subreference');
        $this->assertEquals($resources[1]->resources[1]->uri, 'http://github.com/avantyao/wadl-parser-php/selfdefine/subreference');
        $this->assertEquals($resources[1]->resources[1]->resources[0]->uri, 'http://github.com/avantyao/wadl-parser-php/selfdefine/subreference/unpublished');
        $this->assertEquals($resources[1]->resources[1]->resources[0]->resources[0]->uri, 'http://github.com/avantyao/wadl-parser-php/selfdefine/subreference/unpublished/{itemId}');
        
        $this->assertEquals($resources[2]->path, 'keyword/rise');
        $this->assertEquals($resources[2]->resources[0]->path, 'unpublished');
        $this->assertEquals($resources[2]->resources[0]->uri, 'http://github.com/avantyao/wadl-parser-php/keyword/rise/unpublished');
        $this->assertEquals($resources[2]->resources[0]->resources[0]->uri, 'http://github.com/avantyao/wadl-parser-php/keyword/rise/unpublished/{itemId}');
    }
	
}