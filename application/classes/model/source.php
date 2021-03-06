<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for Sources
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_Source extends ORM
{
	/**
	 * A source belongs to an account
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array(
		'account' => array()
		);

	/**
	 * A source has and belongs to many identities
	 *
	 * @var array Relationhips
	 */
	protected $_has_many = array(
		'identities' => array(
			'model' => 'identity',
			'through' => 'identities_sources'
			)
		);

	/**
	 * Overload saving to perform additional functions on the source
	 */
	public function save(Validation $validation = NULL)
	{
		// Do this for first time sources only
		if ($this->loaded() === FALSE)
		{
			// Save the date the source was first added
			$this->source_date_add = date("Y-m-d H:i:s", time());
		}
		else
		{
			$this->source_date_modified = date("Y-m-d H:i:s", time());
		}		

		return parent::save();
	}
}
