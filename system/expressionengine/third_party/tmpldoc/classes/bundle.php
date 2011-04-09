<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Bundle provides a class and configuration loading system. 
 *
 * Most of the code comes from Kohana. Just reworked some stuff so that it could be used
 * outside of Kohana.
 *
 * @package Bundle
 * @author Thomas Brewer
 **/
class Bundle {
	
	const DIRECTORY_SEPARATOR 				= '/';
	
	const EXT									= '.php';
	
	protected static $_initialized		= FALSE;
	
	protected static $_bundles				= array();
		
	protected static $cache_dir			= '';
	
	public static $caching					= FALSE;
	
	protected static $_files_changed		= FALSE;
	
	public $name 								= NULL;

	public $base_path							= array();
	
	protected $_files 						= array();	

	
	
	/**
	 * init
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public static function init($caching = FALSE, $cache_dir = '') 
	{
		Bundle::$caching = $caching;
		
		if (Bundle::$_initialized === FALSE)
		{
			if (Bundle::$caching === TRUE)
			{				
				Bundle::$cache_dir = $cache_dir;

				$bundles = Bundle::cache('Bundle::$_bundles');
				
				/*
					TODO use load here
				*/
				
				if ($bundles !== NULL)
				{
					Bundle::$_bundles = $bundles;
				}
			}
			
			spl_autoload_register(array('Bundle', 'auto_load'));

			register_shutdown_function(array('Bundle', 'shutdown_handler'));
		}
	}
	
	/**
	 * load
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public static function factory($name, $base_dir)
	{
		$bundle = new Bundle($name, $base_dir);
		return $bundle;
	}
	
	/**
	 * load
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public static function load($bundles = array()) 
	{			
		if (isset($bundles['main']))
		{
			$main_path = $bundles['main'];
			unset($bundles['main']);
		}
		
		$core_path = NULL;
		if (isset($bundles['core']))
		{
			$core_path = $bundles['core'];
			unset($bundles['core']);
		}
	
		if ($core_path !== NULL)
		{
			array_unshift(Bundle::$_bundles, Bundle::factory('core', $core_path));
		}
		
		foreach (array_reverse($bundles, TRUE) as $bundle_name => $base_path) 
		{
			array_unshift(Bundle::$_bundles, Bundle::factory($bundle_name, $base_path));
		}
		
		if ($main_path !== NULL)
		{
			array_unshift(Bundle::$_bundles, Bundle::factory('main', $main_path));
		}
		
		foreach (Bundle::$_bundles as $bundle) 
		{
			$init = $bundle->base_path.'init'.Bundle::EXT;
			if (is_file($init))
			{
				require_once $init;
			}
		}
		
	}
	
	/**
	 * find_file
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public static function find_file($dir, $file, $ext = NULL) 
	{
		foreach (Bundle::$_bundles as $bundle_name => $bundle) 
		{
			if ($path = $bundle->file($dir, $file, $ext))
			{
				return $path;
			}
		}
	}
	
	/**
	 * config
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public static function config($group) 
	{
		static $config;

		if (strpos($group, '.') !== FALSE)
		{
			// Split the config group and path
			list ($group, $keypath) = explode('.', $group, 2);
		}

		if ( ! isset($config[$group]))
		{
			$path = Bundle::find_file('config', $group);
			
			if ($path === NULL)
			{
				return NULL;
			}
			
			$config[$group] = Bundle::load_file($path);
		}

		if (isset($keypath))
		{
			/*
				TODO Fix the dependency on Kohana
			*/
			$config_array = Arr::path($config[$group], $keypath);
		}
		else
		{
			$config_array = $config[$group];
		}
		
		if (is_array($config_array))
		{
			return new ArrayObject($config_array, ArrayObject::ARRAY_AS_PROPS);
		}
		
	}
	
	/**
	 * load_file
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public static function load_file($path) 
	{
		return include $path;
	}
	
	/**
	 * cache
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public static function cache($name, $data = NULL, $lifetime = 60) 
	{
	
		// Cache file is a hash of the name
		$file = sha1($name).'.txt';

		// Cache directories are split by keys to prevent filesystem overload
		$dir = Bundle::$cache_dir.Bundle::DIRECTORY_SEPARATOR.$file[0].$file[1].Bundle::DIRECTORY_SEPARATOR;
		
		try
		{
			if ($data === NULL)
			{
				if (is_file($dir.$file))
				{
					if ((time() - filemtime($dir.$file)) < $lifetime)
					{
						// Return the cache
						return unserialize(file_get_contents($dir.$file));
					}
					else
					{
						// Cache has expired
						unlink($dir.$file);
					}
				}

				// Cache not found
				return NULL;
			}

			if ( ! is_dir($dir))
			{
				// Create the cache directory
				mkdir($dir, 0777, TRUE);

				// Set permissions (must be manually set to fix umask issues)
				chmod($dir, 0777);
			}

			// Write the cache
			return (bool) file_put_contents($dir.$file, serialize($data));
		}
		catch (Exception $e)
		{
			throw $e;
		}
	}
	

	
	/**
	 * __construct
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public function __construct($name, $base_path) 
	{
		$this->name = $name;
		
		$this->base_path = $base_path;
	}
			
	/**
	 * file
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public function file($dir, $file, $ext) 
	{
		// Use the defined extension by default
		$ext = ($ext === NULL) ? self::EXT : '.'.$ext;

		// Create a partial path of the filename
		$path = $this->base_path.$dir.self::DIRECTORY_SEPARATOR.$file.$ext;
		
		if (Bundle::$caching === TRUE AND isset($this->_files[$path]))
		{
			return $this->_files[$path];
		}
		
		// The file has not been found yet
		$found = FALSE;
				
		if (is_file($path))
		{
				// A path has been found
				$found = $path;
		}

		if (Bundle::$caching === TRUE)
		{
			// Add the path to the cache
			$this->_files[$path] = $found;

			// Files have been changed
			Bundle::$_files_changed = TRUE;
		}

		return $found;
	}
	
	
	/**
	 * auto_load
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public static function auto_load($class)
	{
		// Transform the class name into a path
		$file = str_replace('_', '/', strtolower($class));
		
		if ($path = Bundle::find_file('classes', $file))
		{
			// Load the class file
			require $path;

			// Class has been found
			return TRUE;
		}

		// Class is not on the filesystem
		return FALSE;
	}
	
	/**
	 * shutdown_handler
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public static function shutdown_handler() 
	{
		if (Bundle::$caching === TRUE AND Bundle::$_files_changed === TRUE)
		{
			
			/*
				TODO need to add the main and core to the bundles cached 
			*/
			Bundle::cache('Bundle::$_bundles', Bundle::$_bundles);
		}
	}
	
}