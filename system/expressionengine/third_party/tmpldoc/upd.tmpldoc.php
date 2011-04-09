<?php 

class Tmpldoc_upd {
	
	public $version = '0.1';
	
	protected $module_name = 'Tmpldoc';
	
	/**
	 * install
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public function __construct()
	{
		$this->EE =& get_instance();
	}
	
	/**
	 * install
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	public function install() 
	{
		$this->EE->load->dbforge();

		$data = array(
			'module_name' 			=> $this->module_name,
			'module_version' 		=> $this->version,
			'has_cp_backend' 		=> 'y',
			'has_publish_fields' => 'y'
		);

		$this->EE->db->insert('modules', $data);		
		
		return TRUE;
	}
	
	/**
	 * uninstall
	 *
	 * @access public
	 * @param  void	
	 * @return void
	 * 
	 **/
	function uninstall()
	{
		$this->EE->load->dbforge();

		$this->EE->db->select('module_id');
		$query = $this->EE->db->get_where('modules', array('module_name' => $this->module_name));

		$this->EE->db->where('module_id', $query->row('module_id'));
		$this->EE->db->delete('module_member_groups');

		$this->EE->db->where('module_name', $this->module_name);
		$this->EE->db->delete('modules');

		$this->EE->db->where('class', $this->module_name);
		$this->EE->db->delete('actions');

		return TRUE;
	}
	
}