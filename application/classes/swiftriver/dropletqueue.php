<?php defined('SYSPATH') or die('No direct script access');

/**
 * DropletQueue
 *
 * @author      Ushahidi Team
 * @package     Swiftriver <http://www.swiftly.org>
 * @category    Helpers
 * @copyright   (c) 2008-2011 Ushahidi Inc <http://www.ushahidi.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU General Public License v3 (GPLv3) 
 */

class Swiftriver_Dropletqueue {
	
	/**
	 * Maintains the list of droplets to be passed on for processing
	 * @var array
	 */
	private static $_queue = array();
	
	/**
	 * List of processed droplets
	 * @var array
	 */
	private static $_processed = array();
	
	/**
	 * Processes the droplet queue.
	 * The queue processing involves extracting metadata from the droplets
	 * saving these to the database. The extracted metadata could be links,
	 * named entities, places
	 */
	public static function process()
	{
		// Reverse the ordering of items in the array
		// NOTE: Necessary evil because we'll be popping items; possible bottleneck
		self::$_queue = array_reverse(self::$_queue);
		
		// Process the items in the queue
		while (count(self::$_queue) > 0)
		{
			// Pop that droplet!
			$droplet = array_pop(self::$_queue);
			
			// Submit the droplet to an extraction plugin
			Swiftriver_Event::run('swiftriver.droplet.extract_metadata', $droplet);
			
			// Save the droplet - with the extracted metadata
			Model_Droplet::create_from_array($droplet);
			
			// Add the droplet to the list of processed items
			self::$_processed[] =  $droplet;
		}
	}
	
	/**
	 * Adds a droplet to the processing queue.
	 * Crawlers that siphon content from the various channels should call
	 * this method once the content has been "atomized" i.e. converted into a droplet
	 * The template for the droplets may be obtained by invoking get_droplet_template
	 * from within the crawler.
	 *
	 * @param array $droplet
	 */
	public static function add(array & $droplet)
	{
		// Set the SHA-256 hash value for the droplet
		$droplet['droplet_hash'] = hash('sha256', $droplet['droplet_orig_id']);
		
		// Check if the droplet has already been added to the queue
		if (Model_Droplet::is_duplicate_droplet($droplet['channel_filter_id'], $droplet['droplet_hash']))
		{
			// Delete the droplet from memory
			unset($droplet);
			return;
		}
		
		// Validate the droplet
		$validation = Validation::factory($droplet)
					->rule('channel', 'not_empty')
					->rule('droplet_content', 'not_empty')
					->rule('droplet_raw', 'not_empty')
					->rule('droplet_date_pub', 'not_empty')
					->rule('identity_orig_id', 'not_empty')
					->rule('identity_username', 'not_empty');
		
		if ($validation->check())
		{
			// Create the identity
			Model_Identity::create_from_droplet($droplet);
		
			// Create droplet from the array
			Model_Droplet::create_from_array($droplet)
		
			// Add new proprties to the droplet
			$droplet['tags'] = array();
			$droplet['links'] = array();
			$droplet['places'] = array();
	
			// Add the droplet to the queue
			self::$_queue[] = $droplet;
		}
	}
	
	/**
	 * Generates and returns the template for a droplet. The template
	 * is a key=>value array which the crawlers use for constructing 
	 * an actual droplet
	 *
	 * @return array
	 */
	public static function get_droplet_template()
	{
		return array(
			'channel' => '',
			'channel_filter_id' => '',
			'identity_orig_id' => '',
			'identity_username' => '',
			'identity_name' => '',
			'droplet_orig_id' => '',
			'droplet_type' => '',
			'droplet_title' => '',
			'droplet_content' => '',
			'droplet_raw' => '',
			'droplet_locale' => '',
			'droplet_date_pub' => '',
		);
	}
	
	/**
	 * Gets the list of droplets that have already undergone processing
	 * This method should be called by the controller that is responsible
	 * for rendering the processed droplets on the UI
	 *
	 * @return array
	 */
	public static function get_processed_droplets()
	{
		// Fetch the processed droplets
		$result = self::$_processed;
		
		// Reset the processed queue
		self::$_processed = array();
		
		return $result;
	}
}
?>