<?php


abstract class Template_Parser_Core {
	
	protected $buffer;
	
	protected $source;

	/**
	 * __construct
	 *
	 * @access public
	 * @param  Template_Source $source	
	 * @return void
	 * 
	 **/
	public function __construct($source) 
	{
		$this->source = $source;
		$this->buffer = new Template_Parser_Buffer;
		
		return $this;
	}
	
	/**
	 * run
	 *
	 * @abstract
	 * @access public	
	 * @return string
	 * 
	 **/
	abstract public function run();
	
}