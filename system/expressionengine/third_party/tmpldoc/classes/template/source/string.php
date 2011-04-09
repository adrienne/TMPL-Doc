<?php

class Template_Source_String extends Template_Parser_Buffer {
	
	/**
	 * __construct
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public function __construct($string) 
	{
		$this->buffer = preg_split('//', $string, -1, PREG_SPLIT_NO_EMPTY);
	}
	
}