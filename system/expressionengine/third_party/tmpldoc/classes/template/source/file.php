<?php

class Template_Source_File extends Template_Source_String {
	
	/**
	 * __construct
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public function __construct($file_path) 
	{
		if (is_file($file_path))
		{
			$str = file_get_contents($file_path);
		}
		else
		{
			throw new Expection('Unable to load file: '.$file_path);
		}
		
		parent::__construct($str);
	}
	
}