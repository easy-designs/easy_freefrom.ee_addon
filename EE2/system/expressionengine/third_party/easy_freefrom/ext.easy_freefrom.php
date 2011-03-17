<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Easy_freefrom_ext Class
 *
 * @package			ExpressionEngine
 * @category		Extension
 * @author			Aaron Gustafson
 * @copyright		Copyright (c) Easy! Designs, LLC
 * @link			http://www.easy-designs.net/
 */

class Easy_freefrom_ext
{
	public $name			= 'Easy Freefrom';
	public $version			= '1.0';
	public $description		= 'Sets the email address of the Freeform submitter as the from address (as appropriate).';
	public $settings_exist	= FALSE;
	public $docs_url		= 'https://github.com/easy-designs/easy_freefrom.ee_addon';

	/**
	 * Constructor
	 *
	 * @return	void
	 */
	function __construct()
	{
		# Get global instance
		$this->EE =& get_instance();
	}

	// --------------------------------------------------------------------

	/**
	 * Optionally adds variables to Global Vars for early parsing
	 *
	 * @return	null
	 */
	function change_from( $fields, $entry_id, &$msg )
	{
		
		# get the message contents and make into a useful array
		$submission = preg_split( '/(\r\n?|\n)/', $msg['msg'] );
		foreach ( $submission as $k => $v )
		{
			if ( strpos( $v, ':' ) !== FALSE )
			{
				$v = explode( ':', $v );
				$v[0] = trim( $v[0] );
				$v[1] = trim( $v[1] );
				if ( ! empty( $v[1] ) )
				{
					$submission[$v[0]] = $v[1];
				}
			}
			unset( $submission[$k] );
		}
		
		# the all-important email
		if ( isset($fields['email']) )
		{
			$msg['from_email'] = $submission[$fields['email']];
		}

		# the less-important name
		if ( isset($fields['name']) )
		{
			$msg['from_name'] = $submission[$fields['name']];
		}
		elseif ( isset($fields['first_name']) &&  isset($fields['last_name']) )
		{
			$msg['from_name'] = $submission[$fields['first_name']] . ' ' . $submission[$fields['last_name']];
		}
		
		return $msg;
		
	}

	// --------------------------------------------------------------------

	/**
	* Activate Extension
	*
	* @param	bool	$install_mod
	* @return	null
	*/	
	function activate_extension()
	{
		$this->EE->db->insert(
			'exp_extensions',
			array(
				'class'    => __CLASS__,
				'method'   => 'change_from',
				'hook'     => 'freeform_recipient_email',
				'settings' => '',
				'priority' => 10,
				'version'  => $this->version,
				'enabled'  => 'y'
			)
		); // end db->query
	}

	// --------------------------------------------------------------------

	/**
	* Disable Extension
	*
	* @return	null
	*/
	function disable_extension()
	{
		$this->EE->db->where('class', __CLASS__);
    	$this->EE->db->delete('extensions');
	}

	// --------------------------------------------------------------------

	/**
	* Update Extension
	*
	* @param	string	$current
	* @return	null
	*/
	function update_extension($current=FALSE){}

	// --------------------------------------------------------------------

} # end Easy_freefrom_ext

/* End of file ext.easy_freefrom.php */ 
/* Location: ./system/expressionengine/third_party/easy_freefrom/ext.easy_freefrom.php */