<?php 


class Template_Comment_Parser extends Template_Parser_Core {
	
	protected $template_comment;
	
	protected $eetag_params = array();
	
	/**
	 * __construct
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public function __construct($source, $tag) 
	{
		parent::__construct($source);
		
		$this->template_comment = new Template_Comment;
		
		$this->template_comment->tag = $tag;
		
		$eetag_params = array();
		
		if (preg_match_all('~\s(.*:?)="(.*)"~imxsU', $this->template_comment->tag, $eetag_params))
		{
			if (isset($eetag_params[1]))
			{	
				foreach ($eetag_params[1] as $index => $param) 
				{
					$this->eetag_params[$param] = $eetag_params[2][$index];
				}
			}
		}
		
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
		
		$found_tag = FALSE;
		
		$end_of_source = count($this->source) - 1;
		
		foreach ($this->source as $index => $char) 
		{	
			if ($end_of_source !== $index)
			{
				if ($char !== '@' AND $found_tag === FALSE)
				{
					//we are still collecting the comment text
					$this->buffer[] = $char;
				}
				else if ($char === '@' AND $found_tag === FALSE)
				{
					//we have found our first tag
					$found_tag = TRUE;

					$this->_add_comment();
					
					$this->buffer[] = $char;						
				}
				else if ($char === '@' AND $found_tag === TRUE) 
				{
					//we will be here after the first tag is found when a new tag is found
					$this->_add_tag();
					
					$this->buffer[] = $char;
				}
				else if ($char !== '@' AND $found_tag === TRUE) 
				{
					//we will collect tag data here
					$this->buffer[] = $char;
				}
				
			}
			else
			{
				if ($found_tag === TRUE) 
				{
					$this->_add_tag();
				}
				else 
				{
					$this->_add_comment();
				}
			}
		}
		
		
		//finish up by adding any params not in the comment tags
		foreach ($this->eetag_params as $param => $value) 
		{	
			if (array_key_exists($param, $this->template_comment->tags["@param"]))
			{
				$tag = $this->template_comment->tags["@param"][$param];

				if (empty($tag->value))
				{
					$tag->value = $value;
				}
			}
			else
			{

				$tag = new Template_Comment_Tag;
				$tag->name = '@param';
				$tag->param = $param;
				$tag->value = $value;
				
				$this->template_comment->tags[$tag->name][$param] = $tag;
			}
		}
		
		return $this->template_comment;
	}
	
	/**
	 * _add_comment
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public function _add_comment() 
	{
		$this->template_comment->comment = trim($this->buffer->as_string());
		
		$this->buffer->reset();
	}
	
	/**
	 * _add_tag
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public function _add_tag() 
	{
		$parser = new Template_Comment_Tag_Parser(new Template_Source_String(trim($this->buffer->as_string())));
		$tag = $parser->run();

		if ($tag)
		{			
			if (!in_array($tag->name, array('@tag', '@param', '@todo')))
			{
				$this->template_comment->tags['@unkown'][] = $tag;
			}
			else
			{
				$this->template_comment->tags[$tag->name][$tag->param] = $tag;
			}			
			
		}

		$this->buffer->reset();
	}
	
}