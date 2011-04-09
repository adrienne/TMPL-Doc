<?php 

class Template_Comment_Tag_Parser extends Template_Parser_Core {
	
	protected $comment_tag;
	
	/**
	 * __construct
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public function __construct($source) 
	{
		parent::__construct($source);
		
		$this->comment_tag = new Template_Comment_Tag;
		
		return $this;
	}
	
	/**
	 * run
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public function run() 
	{	
		$matches = array();
		
		if (preg_match_all('/^@(.*)\s/U', $this->source, $matches))
		{
			$type = $matches[1][0];
			$this->$type();
		}
		
		return $this->comment_tag;
	}
	
	/**
	 * tag
	 *
	 * @access protected
	 * @param  void	
	 * @return void
	 * 
	 **/
	protected function tag() 
	{
		$this->_parse_with_param();
	}
	
	/**
	 * param
	 *
	 * @access protected
	 * @param  void	
	 * @return void
	 * 
	 **/
	protected function param() 
	{
		$this->_parse_with_param();
	}
	
	/**
	 * todo
	 *
	 * @access protected
	 * @param  void	
	 * @return void
	 * 
	 **/
	protected function todo() 
	{
		$this->_default();
	}
	
	/**
	 * _has_param
	 *
	 * @access protected
	 * @param  void	
	 * @return void
	 * 
	 **/
	protected function _parse_with_param() 
	{
		$tag_name_found = FALSE;
		
		$first_param_found = FALSE;
		
		$end_of_source = count($this->source) - 1;
		
		$this->comment_tag->source = $this->source;
		
		foreach ($this->source as $index => $char) 
		{
			if ($end_of_source !== $index)
			{
				if ($char !== ' ' AND $tag_name_found === FALSE)
				{
					$this->buffer[] = $char;
				}
				else if ($char === ' ' AND $tag_name_found === FALSE)
				{
					$this->buffer[] = $char;
					$this->comment_tag->name = trim($this->buffer->as_string());
					$this->buffer->reset();
					$tag_name_found = TRUE;
				}
				else if ($char !== ' ' AND $first_param_found === FALSE)
				{
					$this->buffer[] = $char;	
				}
				else if ($char === ' ' AND $first_param_found === FALSE)
				{
					$this->buffer[] = $char;
					$this->_parse_param();
					$this->buffer->reset();
					$first_param_found = TRUE;
				}
				else
				{
					$this->buffer[] = $char;
				}
				
			}
			else
			{
				if ($first_param_found === FALSE) 
				{
					$this->buffer[] = $char;
					$this->_parse_param();
					$this->buffer->reset();
					$first_param_found = TRUE;
				}
				else
				{
					$this->buffer[] = $char;
					$this->comment_tag->comment = trim($this->buffer->as_string());
					$this->buffer->reset();
					
				}
				
			}
		}
	}
	
	/**
	 * _parse_param
	 *
	 * @access protected
	 * @param  void	
	 * @return void
	 * 
	 **/
	protected function _parse_param() 
	{
		$param = trim($this->buffer->as_string());
	
		$param_parts = array();
		
		if (preg_match_all('~(.*:?)="(.*)"~imxsU', $param, $param_parts))
		{
			$param = (isset($param_parts[1]) && isset($param_parts[1][0])) ? $param_parts[1][0] : '';
			$value = (isset($param_parts[2]) && isset($param_parts[2][0])) ? $param_parts[2][0] : '';
		}
		else
		{
			$value = '';
		}
		
		$this->comment_tag->value = $value;
		$this->comment_tag->param = $param;
	}
	
	/**
	 * _default
	 *
	 * @access protected
	 * @param  void	
	 * @return void
	 * 
	 **/
	protected function _default() 
	{
		$matches = array();
		
		if (preg_match_all('/^(@.*)\s(.*?)/Usi', $this->source, $matches))
		{	
			if (isset($matches[0]))
			{
				$this->comment_tag->source = $matches[0][0];
			}
		
			if (isset($matches[1]))
			{
				$this->comment_tag->name = $matches[1][0];
			}
						
			if (isset($matches[2]))
			{
				$this->comment_tag->comment = $matches[2][0];
			}
		}
	}
	
	/**
	 * handles all unknown tag types
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public function __call($name, $args) 
	{
		return $this->_default();
	}
	
}