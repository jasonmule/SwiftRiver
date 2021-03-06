<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Bucket Collaborator Settings Controller
 *
 * PHP version 5
 * LICENSE: This source file is subject to GPLv3 license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/gpl.html
 * @author	   Ushahidi Team <team@ushahidi.com> 
 * @package	   SwiftRiver - http://github.com/ushahidi/Swiftriver_v2
 * @subpackage Controllers
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license	   http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */
class Controller_Bucket_Collaborators extends Controller_Bucket_Settings {
	
	/**
	 * @return	void
	 */
	public function action_index()
	{
		$this->template->header->title = $this->bucket->bucket_name.' ~ '.__('Collaborator Settings');
		$this->template->header->js .= Html::script("themes/default/media/js/collaborators.js");
		
		$this->active = 'collaborators';
		$this->settings_content = View::factory('template/collaborators');
		$this->settings_content->fetch_url = $this->bucket_base_url.'/collaborators';
		$this->settings_content->collaborator_list = json_encode($this->bucket->get_collaborators());;
	}
	
}