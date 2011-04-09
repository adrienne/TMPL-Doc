<?php

define('SYSPATH', TRUE);
require PATH_THIRD.'tmpldoc/classes/bundle.php';

Bundle::init();

Bundle::load(array(
	'main' => PATH_THIRD.'tmpldoc/',
));

class Tmpldoc_mcp {
	
	public $base_url;
	
	function __construct()
	{
		$this->base_url = AMP.'C=addons_modules'.AMP.'M=show_module_cp'.AMP.'module=tmpldoc';
		
		$this->EE =& get_instance();

		$this->EE->cp->set_right_nav(array(
			//'name'	=> 'url'
		));
		
		$this->theme_folder_url = $this->EE->config->item('theme_folder_url');
		
		$style_url = $this->theme_folder_url . 'third_party/tmpldoc/assets/styles/base.css';
		
		$js_url = $this->theme_folder_url . 'third_party/tmpldoc/assets/scripts/base.js';
		
		$this->EE->cp->add_to_foot('<link href="'.$style_url.'" type="text/css" rel="stylesheet" />');
		$this->EE->cp->add_to_foot('<script type="text/javascript" src="'.$js_url.'"></script>');
	}
	
	
	function index() 
	{
		$this->EE->load->helper('form');
		
		$this->EE->cp->set_variable('cp_page_title', $this->EE->lang->line('tmpldoc_module_name'));
		
		$template_file_basepath = $this->EE->config->item('tmpl_file_basepath');

		$sites_query = $this->EE->db->select('site_label, site_name')
			->from('sites')
			->get();
		
		$sites = array('' => 'Select One');
		
		foreach ($sites_query->result_array() as $site) 
		{
			$sites[$site['site_name']] = $site['site_label'];
		}
		
		$site_name = ($this->EE->input->post('site_name')) ? $this->EE->input->post('site_name') : 'default_site';
			
		$templates_path = $template_file_basepath . $site_name . '/';
		
		if (is_dir($templates_path))
		{
			$templates = Template_Source::load_templates_from_dir($templates_path);
			
		}
		else
		{
			$templates = array();
		}
		
		$view = Template_View::factory('index', array(
			'template_data' => $templates, 
			'sites' => $sites, 
			'mcp' => $this,
		));
		
		return $view->render();
	}
	
}
