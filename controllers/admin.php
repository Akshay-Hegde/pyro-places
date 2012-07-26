<?php

/**
 * This module allows for the simple display of maps from Google's API. It
 * allows for run-time tag display (for one-offs) as well as storing locations
 * in more long-term scenarios for easy retrieval.
 * @package places
 */

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Admin controller for places.
 * @package places
 */
class Admin extends Admin_Controller
{
    /**
     * Used by the PyroCMS admin code to match shortcuts in details.php to
     * controllers' admin views.
     */
    protected $section = 'places';

    /**
     * Basic controller constructor. Pulls in models, validation rules, assets.
     */
	public function __construct()
	{
		parent::__construct();

        // Load the required classes
        $this->load->model(array(
            'settings_m',
            'place_m',
        ));

        $this->lang->load('places');
		
		// Set the validation rules
        $this->item_validation_rules = array(
            array(
                'field' => 'name',
                'label' => 'sports:location:name',
                'rules' => 'trim|max_length[100]|required|is_unique[places.address'),
            array(
                'field' => 'address',
                'label' => 'sports:location:address',
                'rules' => 'trim|max_length[100]|is_unique[places.address'),
        );

        $this->data->settings = $this->settings_m->get_all();
        $this->data->default_params = "";

        $default = $this->get_defaults();
        // Urlify
        foreach($default as $set_n=>$set_v)
        {
            $this->data->default_params .= "&$set_n=$set_v";
        }
        // Remove first ampersand.
        $this->data->default_params = substr($this->data->default_params, 1);

        /*
        $this->data->settings->zoom_level = $this->settings_m->get('zoom_level');
        $this->data->settings->api_key = $this->settings_m->get('api_key');
        $this->data->settings->image_size = $this->settings_m->get('image_size');
        */
	}

    protected function get_defaults()
    {
        $dp = array();

        foreach ($this->settings_m->get_many_by(array('module' => 'places')) as $setting)
        {
            if (!empty($setting->value))
            {
                $dp[substr($setting->slug, strlen('places_tag_'))] = $setting->value;
            }
        }

        // Sensor is always false for these pages.
        $dp['sensor'] = 'false';

        return $dp;
    }

	/**
	 * Displays all known places, allowing for some CRUD of place_m.
	 */
	public function index()
	{
		$this->data->places =& $this->place_m->get_all();
		$this->template->title($this->module_details['name'])
						->build('admin/places', $this->data);
	}

    /**
     * Renders create view and handles submission from the same form.
     * @todo Improve the view (and perhaps the controller) to support preview
     * images of addresses through Google maps.
     */
    public function create()
    {
        // Secure values.
        $this->form_validation->set_rules($this->item_validation_rules);

        if ($_POST) // Did someone even post? Slight runtime optimisation.
        {
            // Did the form submit correctly?
            if ($this->form_validation->run())
            {
                unset($_POST['btnAction']);

                if ($this->place_m->create($this->input->post()))
                {
                    $this->session->set_flashdata('success', lang('success_label'));
                    redirect('admin/places');
                }
                else
                {
                    $this->session->set_flashdata('error', lang('general_error_label'));
                    redirect('admin/place_create');
                }
            }
        }

        // Assuming validation failed, 
        foreach ($this->item_validation_rules as $rule)
        {
            $this->data->{$rule['field']} = $this->input->post($rule['field']);
        }

        $this->template->title($this->module_details['name'], lang('global:add'))
                       ->build('admin/place_create', $this->data);
    }

    /**
     * Renders the edit view for locations and handles submission of the forms.
     * @todo As with create, see if we can't make some kind of preview
     * mechanism for addresses.
     * @param int $id Id of the location to edit.
     * @return ?
     */
    public function edit($id)
    {
        $this->data = $this->place_m->get($id);

        $this->form_validation->set_rules($this->item_validation_rules);

        // check if the form validation passed
        if($this->form_validation->run())
        {
            // get rid of the btnAction item that tells us which button was clicked.
            // If we don't unset it MY_Model will try to insert it
            unset($_POST['btnAction']);
            
            // See if the model can create the record
            if($this->place_m->update($id, $this->input->post()))
            {
                // All good...
                $this->session->set_flashdata('success', lang('success_label'));
                redirect('admin/places');
            }
            // Something went wrong. Show them an error
            else
            {
                $this->session->set_flashdata('error', lang('general_error_label'));
                redirect('admin/place_create');
            }
        }

        // Build the view using places/views/admin/place_create
        $this->template->title($this->module_details['name'], lang('global:add'))
                       ->build('admin/place_create', $this->data);
    }

    /**
     * Deletes a places entry without any large amount of fanfare.
     * @param int $id Id of the place to delete.
     * @return ?
     */
    public function delete($id)
    {
        // make sure the button was clicked and that there is an array of ids
        if (isset($_POST['btnAction']) AND is_array($_POST['action_to']))
        {
            // pass the ids and let MY_Model delete the items
            $this->place_m->delete_many($this->input->post('action_to'));
        }
        elseif (is_numeric($id))
        {
            // they just clicked the link so we'll delete that one
            $this->place_m->delete($id);
        }
        redirect('admin/places');
    }
}
