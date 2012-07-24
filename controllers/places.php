<?

/**
 * This module allows for the simple display of maps from Google's API. It
 * allows for run-time tag display (for one-offs) as well as storing locations
 * in more long-term scenarios for easy retrieval.
 * @package places
 */

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Public controller for places.
 * @package places
 */
class Places extends Public_Controller
{
	/**
	 * Standard-issue constructor.
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->model('place_m');

        // We use this to get coaches for the teams rather than just numbers.
		$this->lang->load('places');
	}

	/**
	 * Single-item view. We don't need anything else atm.
	 * @param int $id Location id.
	 */
	public function view($id)
    {
    	$this->data = $this->place_m->get($id);

    	$this->data->settings->zoom_level = $this->settings_m->get('zoom_level');
    	$this->data->settings->api_key = $this->settings_m->get('api_key');
    	$this->data->settings->image_size = $this->settings_m->get('image_size');

        $this->template->title($this->module_details['name'])
                       ->build('place_view', $this->data);
    }
}
