<?php 

class Template_Data {
	
	public $group;
	
	public $name;
	
	public $comments = array();
	
	public $globals = array();
	
	public $snippets = array();
	
	/**
	 * has_commnets
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public function has_commnets() 
	{
		return (count($this->comments) > 0) ? TRUE : FALSE;
	}
}