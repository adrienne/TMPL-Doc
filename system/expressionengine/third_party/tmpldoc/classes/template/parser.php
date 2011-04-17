<?php 

class Template_Parser extends Template_Parser_Core {
	
	protected $template_data;
	
	/**
	 * __construct
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public function __construct($source, $template_group, $template_name) 
	{
		parent::__construct($source);
		
		$this->template_data = new Template_Data;
		
		$this->template_data->group = $template_group;
		
		$this->template_data->name = $template_name;
		
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
		$source = "$this->source";
		
		$comments = array();
		
		if (preg_match_all('/{!--#(.*)#--}\n.?{(.*)}/Uis', $source, $comments))
		{	
			foreach ($comments[1] as $index => $comment) 
			{
				//get rid of the tag comments in the template
				$source = str_replace($comments[0][$index], '', $source);
				
				$tag = (isset($comments[2]) && isset($comments[2][$index])) ? "{".$comments[2][$index]."}" : '';
				
				$comment_parser = new Template_Comment_Parser(new Template_Source_String($comment), $tag);
				
				$this->template_data->comments[] = $comment_parser->run();
			}
		}
		
		//pass through again and grab the regular comments
		if (preg_match_all('/{!--:(.*?):--}/is', $source, $comments))
		{	
			foreach ($comments[1] as $index => $comment) 
			{
				$tag = (isset($comments[2]) && isset($comments[2][$index])) ? "{".$comments[2][$index]."}" : '';
				
				$comment_parser = new Template_Comment_Parser(new Template_Source_String($comment), $tag);
				
				$this->template_data->comments[] = $comment_parser->run();
			}
		}
		
		$this->template_data->comments = array_reverse($this->template_data->comments);
		
		$globals = array();
		
		if (preg_match_all('/{gbl(.*?)}/is', $source, $globals))
		{	
			foreach ($globals[0] as $global) 
			{
				$this->template_data->globals[] = $global;
			}
		}
		
		$snippets = array();
		
		if (preg_match_all('/{snip(.*?)}/is', $source, $snippets))
		{	
			foreach ($snippets[0] as $snippet) 
			{
				$this->template_data->snippets[] = $snippet;
			}
		}
		
		return $this->template_data;
	}

	
}