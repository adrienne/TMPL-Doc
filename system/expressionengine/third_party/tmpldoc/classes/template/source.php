<?php 

class Template_Source {
	
	protected static $ignored_files = array('.DS_Store');
	
	/**
	 * factory
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public static function factory($type, $file_path) 
	{
		$class = self::class_name($type);
		return new $class($file_path);
	}
	
	/**
	 * class_name
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public static function class_name($type) 
	{
		return 'Template_Source_'.ucfirst(strtolower($type));
	}
	
	
	/**
	 * load_templates_from_dir
	 *
	 * @static
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public static function load_templates_from_dir($template_dir) 
	{
		$templates = array();
		
		foreach(new DirectoryIterator($template_dir) as $dir) 
		{      
			if ($dir->isDir() AND !$dir->isDot())
			{
				$template_group_folder = $dir->getFilename();
				
				$template_group_folder_parts = explode('.', $template_group_folder);
				
				$template_group = $template_group_folder_parts[0];
							
				foreach(new DirectoryIterator($template_dir.'/'.$template_group_folder.'/') as $file) 
				{
					if (!$file->isDir() AND !$file->isDot() AND !in_array($file->getBasename(), self::$ignored_files))
					{	
						$source = Template_Source::factory('file', $file->getPathName());

						$file_name = $file->getFileName();

						$base_name = pathinfo($file_name, PATHINFO_FILENAME);
						
						$parser = new Template_Parser($source, $template_group, $base_name);
						
						$templates[$template_group][$base_name] = $parser->run();
					}
				}
			}
		}
		
		return $templates;
	}
	
	/**
	 * load_view
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public static function load_view() 
	{
		
	}
	
}