<?php 

/**
 * This module allows for the simple display of maps from Google's API. It
 * allows for run-time tag display (for one-offs) as well as storing locations
 * in more long-term scenarios for easy retrieval.
 * @package places
 */

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Primary module code. Version, table installations, sample data, and such.
 * @package places
 */
class Module_Places extends Module {

    /**
     * Version of the module. Follows semantic versioning when beyond major 1.
     */
    public $version = '1.0.0';

    /**
     * Const reminder of the module's name. Can thus be referenced later.
     */
    const MODULE_NAME='places';

    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        // $this->lang->load('places');
    }

    /**
     * Produces the information that is visible throughout PyroCMS about the
     * module. 
     * @return Array of information. The code is almost self-explanatory.
     */
    public function info()
    {
		return array(
			'name' => array(
				'en' => 'Places',
				'da' => 'Steder',
			),
			'description' => array(
				'en' => 'A small module and plugin for easy display of maps through Google\'s API.',
				'da' => 'Et lille modul til fremvisning af adresser fra Googles API.',
			),
			'frontend' => true,
			'backend' => true,
			'menu' => 'content',
            'roles' => array('create_location', 'edit_location', 'delete_location'),
			'sections' => array(
                'places' => array(
                    'name'  => 'places:places', // These are translated from your language file
                    'uri'   => 'admin/places',
                    'shortcuts' => array(
                        array(
                            'name'  => 'places:create',
                            'uri'   => 'admin/places/create',
                            'class' => 'add'
                        ),
                    ),
                ),
            ),
		);
    }

    /**
     * Code re-use. Drops the named table and creates a new one
     * with the passed fields and expecting an 'id' field for
     * key.
     * @param string $name Name of the table. Needn't exist.
     * @param array $fields dbforge-compatible array of field arrays.
     * @return boolean True if succesful, false otherwise.
     */
    protected function install_table($name, $fields)
    {
        $this->dbforge->drop_table($name);

        $this->dbforge->add_field($fields);

        $this->dbforge->add_key('id', true);

        if ( ! $this->dbforge->create_table($name)) return false;
        else return true;
    }

    /**
     * Installs the locations table.
     * Primary key  : id(int)
     * name         : name of the location. A nick could be used.
     * description  : description or perhaps driving guide.
     * address      : Google maps friendly address of the location.
     * @return boolean True on success. False otherwise.
     * */
    public function install_places()
    {
        $table = array(
            'id' => array(
                'type' => 'INT',
                'constraint' => '11',
                'auto_increment' => true,
                ),
            'name' => array(
                'type' => 'TEXT',
                ),
            'description' => array(
                'type' => 'TEXT',
                'null' => true,
                ),
            'address' => array(
                'type' => 'TEXT',
                'null' => true,
                ),
        );

        return $this->install_table('places', $table);
    }

    /**
     * Quick insert of a new location.
     * @param string $name Name of the location, recognisable.
     * @param string $add Address location. Should be unique to a Google maps
     * search.
     * @param string $desc Arbitrary long-winded description of the location.
     * @return int New location id.
     */
    private function insert_location($name, $add = null, $desc = null)
    {
        $this->db->insert('places', array(
            'name' => $name,
            'address' => $add,
            'description' => $desc,
        ));
        return $this->db->insert_id();
    }

    /**
     * Installs a bunch of sample data to work with.
     * @return boolean True on success, false otherwise.
     * @todo Before a true public release, this should be a bit more sanitised.
     */
    private function insert_sample_data()
    {
        // A couple of well-used locations in Aalborg.
        $kat = $this->insert_location('The White House', 'The White House', 'Because Google is smart.');
        $stad = $this->insert_location('Den Lille Havfrue', 'Den Lille Havfrue', 'Because Google is VERY smart.');
        $skip = $this->insert_location('Institute of Computer Sciences, Aalborg University', 'Selma Lagerløfs Vej 300, Aalborg Øst, Danmark', 'Because I like to get myself on the maps.');
    }

    private function install_settings()
    {
        $api_key = array(
            'slug' => 'api_key',
            'title' => 'Google Maps API key',
            'description' => 'Can be obtained from <a href="http://code.google.com/apis/console">Google Code</a>',
            '`default`' => '',
            '`value`' => '',
            'type' => 'text',
            'is_required' => 0,
            'is_gui' => 1,
            'options' => '',
            'module' => 'places'
        );
        $zoom = array(
            'slug' => 'zoom_level',
            'title' => 'Default zoom level',
            'description' => 'The zoom level (1-21) to use by default on displayed google maps.',
            '`default`' => '16',
            '`value`' => '16',
            'type' => 'text',
            'is_required' => 1,
            'is_gui' => 1,
            'options' => '',
            'module' => 'places'
        );
        $size = array(
            'slug' => 'image_size',
            'title' => 'Image size',
            'description' => 'Size of the image to be displayed. Maximum recommended is the default (640x480).',
            '`default`' => '640x480',
            '`value`' => '640x480',
            'type' => 'text',
            'is_required' => 1,
            'is_gui' => 1,
            'options' => '',
            'module' => 'places',
        );

        $this->db->insert('settings', $api_key);
        $this->db->insert('settings', $zoom);
        $this->db->insert('settings', $size);

    }

    /**
     * Complete module installation function. Installs all tables, some sample
     * data to play around with and module settings.
     * @return boolean True on success, false otherwise.
     */
    public function install()
    {
    	// Remove any previous settings.
        $this->db->delete('settings', array('module' => self::MODULE_NAME));

        if (! $this->install_places())
        {
            $this->session->set_flashdata('error', lang('table_install_failed'));
            return false;
        }

        $this->insert_sample_data();
        $this->install_settings();

        return true;
    }

    /**
     * Uninstalls the module. This is basically a question of removing tables,
     * and that's exactly what this thing does.
     * @return boolean True on success. False otherwise.
     */
    public function uninstall()
    {
        $this->dbforge->drop_table('places');

        $this->db->delete('settings', array('module' => self::MODULE_NAME));

        return true;
    }

    /**
     * Upgrade procedure. Should handle *any* upgrade after 1.0.0 up to, but not
     * including, 2.0.0.
     * @param type $old_version The previous version installed. Allows us to
     * diff our way into a proper upgrade mechanism.
     * @return boolean True on success. False otherwise.
     */
    public function upgrade($old_version)
    {
        // Your Upgrade Logic
        $version_a = explode('.', $this->version);
        if ($version_a[0] >= 1 and ($version_a[1] > 0 or $version_a[2] > 0))
            throw new Exception("Big explosion error! We haven't set up any upgrade procedures yet!");
        return true;
    }

    /**
     * The help function determines what should pop up when requesting help from
     * the module.
     * @todo Write some useful documentation.
     * @return string The help text, either as a link, or as pure text.
     */
    public function help()
    {
        // Return a string containing help info
        return "Here you can enter HTML with paragrpah tags or whatever you like";
    }
}
