<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Model for User Followers
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category   Models
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Model_User_Follower extends ORM
{
	/**
	 * An belongs to a user
	 *
	 * @var array Relationhips
	 */
	protected $_belongs_to = array('user' => array());	
		
	/**
	 * Overload saving to perform additional functions on the follower
	 */
	public function save(Validation $validation = NULL)
	{
		// Do this for first time followers only
		if ($this->loaded() === FALSE)
		{
			// Save the date the follower was first added
			$this->follower_date_add = date("Y-m-d H:i:s", time());
		}

		return parent::save();
	}
}
