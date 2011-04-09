<?php 

/**
 * This is a model class for the template comment data
 *
 * @package TMPLDOC
 * @author Thomas Brewer
 **/
class Template_Comment {
	
	public $comment 			= '';
	
	public $tags				= array('@tag' => array(), '@param' => array(), '@unknown' => array());
		
	/**
	 * tags
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public function tags($name = '') 
	{
		return (isset($this->tags[$name])) ? $this->tags[$name] : array();	
	}	
		
}