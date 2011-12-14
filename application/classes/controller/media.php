<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Media Controller
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
class Controller_Media extends Controller {

    /**
     * This allow plugins to have a media directory for static files accessible
     * with the below route:
     * 
     * /media/<file path> 
     *
     * Eg: /media/js/map.js
     *
     * @return void
     */
    public function action_index() {
		// Get the file path from the request
		$file = $this->request->param('file');

		// Find the file extension
		$ext = pathinfo($file, PATHINFO_EXTENSION);

		// Remove the extension from the filename
		$file = substr($file, 0, -(strlen($ext) + 1));

        if ($file = Kohana::find_file('media', $file, $ext)) 
        {
			// Check if the browser sent an "if-none-match: <etag>" header, and tell if the file hasn't changed
			$this->response->check_cache(sha1($this->request->uri()).filemtime($file), $this->request);

			// Send the file content as the response
			$this->response->body(file_get_contents($file));

			// Set the proper headers to allow caching
			$this->response->headers('content-type',  File::mime_by_ext($ext));
			$this->response->headers('last-modified', date('r', filemtime($file)));
        } 
        else 
        {
            // Return a 404 status
            $this->response->status(404);
        }       
    }    

	/**
	 * Header Javascript + Hook
	 *
	 * @return	void
	 */
	public function action_js()
	{
		echo View::factory('common/js/header')
			->bind('base_url', $base_url);

		$base_url = URL::site();

		// SwiftRiver Plugin Hook -- Add Custom JS
		Swiftriver_Event::run('swiftriver.header.js');
	}
	
	/**
	 * Header CSS + Hook
	 *
	 * @return	void
	 */
	public function action_css()
	{
		// SwiftRiver Plugin Hook -- Add Custom JS
		Swiftriver_Event::run('swiftriver.header.css');
	}
}