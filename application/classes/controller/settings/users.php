<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * User management controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @category Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Settings_Users extends Controller_Settings_Main {
	
	
	/**
	 * List all the users
	 *
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->header->title = __('Users');
		$this->settings_content = View::factory('pages/settings/users')
		    ->bind('fetch_url', $fetch_url)
		    ->bind('users_list', $users_list);

		$this->active = 'users';
		$fetch_url = URL::site().'settings/users/manage';
		$users = array();
		foreach (ORM::factory('user')->where('username', '<>', 'admin')->find_all() as $user)
		{
			$users[] = array(
				"id" => $user->id, 
				"name" => $user->name, 
				"user_avatar" => Swiftriver_Users::gravatar($user->email, 45),
				"user_url" => URL::site().$user->account->account_path
			);
		}
		$users_list = json_encode($users);
	}

	public function action_manage()
	{
		$this->template = '';
		$this->auto_render = FALSE;

		switch ($this->request->method())
		{
			case "DELETE":
				// When an existing user is deleted
				$user_orm = ORM::factory('user', $this->request->param('id', 0));
				if ( ! $user_orm->loaded())
				{
					throw new HTTP_Exception_404("The selected user does not exist");
				}

				// Delete user from the system - rivers, buckets, accounts
				$user_orm->delete();

			break;

			case "POST":
				// When an invite is sent
			break;
		}
	}
}
