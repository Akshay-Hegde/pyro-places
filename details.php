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

    /**
     * We load up the settings model to have insert_many for easy, efficient
     * insertion of settings. 
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('settings_m');
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
     */
    private function insert_sample_data()
    {
        // A couple of well-used locations in Aalborg.
        $kat = $this->insert_location('The White House', 'The White House', 'Because Google is smart.');
        $stad = $this->insert_location('Den Lille Havfrue', 'Den Lille Havfrue', 'Because Google is VERY smart.');
        $skip = $this->insert_location('Institute of Computer Sciences, Aalborg University', 'Selma Lagerløfs Vej 300, Aalborg Øst, Danmark', 'Because I like to get myself on the maps.');

        return true;
    }

    /**
     * Inserts all the various settings into th CP. If you want to add a new possible
     * parameter to the Google Maps tags, add a setting with a slug prefixed with
     * 'places_tag_' for automatic detection.
     */
    private function install_settings()
    {
        $success  =$this->settings_m->insert_many(array(
            array(
                'slug' => 'places_tag_api_key',
                'title' => 'Google Maps API key',
                'description' => 'Can be obtained from <a href="http://code.google.com/apis/console">Google Code</a>',
                '`default`' => '',
                '`value`' => '',
                'type' => 'text',
                'is_required' => 0,
                'is_gui' => 1,
                'options' => '',
                'module' => 'places',
            ),
            array(
                'slug' => 'places_tag_zoom',
                'title' => 'Default zoom level',
                'description' => 'Defines the zoom level of the map, which determines the magnification level of the map. This parameter takes a numerical value corresponding to the zoom level of the region desired.',
                '`default`' => '16',
                '`value`' => '16',
                'type' => 'text',
                'is_required' => 1,
                'is_gui' => 1,
                'options' => '',
                'module' => 'places',
            ),
            array(
                'slug' => 'places_tag_size',
                'title' => 'Image size',
                'description' => 'Defines the rectangular dimensions of the map image. This parameter takes a string of the form {horizontal_value}x{vertical_value}. For example, 500x400 defines a map 500 pixels wide by 400 pixels high. Maps smaller than 180 pixels in width will display a reduced-size Google logo. This parameter is affected by the scale parameter, described below; the final output size is the product of the size and scale values.',
                '`default`' => '640x480',
                '`value`' => '640x480',
                'type' => 'text',
                'is_required' => 1,
                'is_gui' => 1,
                'options' => '',
                'module' => 'places',
            ),
            array(
                'slug' => 'places_tag_scale',
                'title' => 'Scale',
                'description' => 'Affects the number of pixels that are returned. scale=2 returns twice as many pixels as scale=1 while retaining the same coverage area and level of detail (i.e. the contents of the map don\'t change). This is useful when developing for high-resolution displays, or when generating a map for printing. The default value is 1. Accepted values are 2 and 4 (4 is only available to Maps API for Business customers.)',
                '`default`' => '1',
                '`value`' => '1',
                'type' => 'select',
                'is_required' => 0,
                'is_gui' => 1,
                '`options`' => '1=1|2=2|4=4 (Unsupported)',
                'module' => 'places',
            ),
            array(
                'slug' => 'places_tag_format',
                'title' => 'Image format',
                'description' => 'Defines the format of the resulting image. By default, the Static Maps API creates PNG images. There are several possible formats including GIF, JPEG and PNG types. Which format you use depends on how you intend to present the image. JPEG typically provides greater compression, while GIF and PNG provide greater detail.',
                '`default`' => 'png',
                '`value`' => 'png',
                'type' => 'select',
                'is_required' => 1,
                'is_gui' => 1,
                'options' => 'png=PNG|jpeg=JPEG|gif=GIF',
                'module' => 'places',
            ),
            array(
                'slug' => 'places_tag_maptype',
                'title' => 'Map type',
                'description' => 'Defines the type of map to construct. There are several possible maptype values, including roadmap, satellite, hybrid, and terrain.',
                '`default`' => 'roadmap',
                '`value`' => 'roadmap',
                'type' => 'select',
                'is_required' => 0,
                'is_gui' => 1,
                'options' => 'roadmap=Roadmap (default)|satellite=Satellite|terrain=Terrain|hybrid=Hybrid',
                'module' => 'places',
            ),
            array(
                'slug' => 'places_tag_language',
                'title' => 'Language',
                'description' => 'Defines the language to use for display of labels on map tiles. Note that this parameter is only supported for some country tiles; if the specific language requested is not supported for the tile set, then the default language for that tileset will be used.',
                '`default`' => 'en',
                '`value`' => 'en',
                'type' => 'text',
                'is_required' => 0,
                'is_gui' => 1,
                'options' => '',
                'module' => 'places',
            ),
            array(
                'slug' => 'places_tag_region',
                'title' => 'Region',
                'description' => 'Defines the appropriate borders to display, based on geo-political sensitivities. Accepts a region code specified as a two-character ccTLD (\'top-level domain\') value.',
                '`default`' => '',
                '`value`' => '',
                'type' => 'text',
                'is_required' => 0,
                'is_gui' => 1,
                'options' => '',
                'module' => 'places',
            ),
            array(
                'slug' => 'places_tag_style',
                'title' => 'Custom Style',
                'description' => 'Defines a custom style to alter the presentation of a specific feature (road, park, etc.) of the map. This parameter takes feature and element arguments identifying the features to select and a set of style operations to apply to that selection. You may supply multiple styles by adding additional style parameters. ',
                '`default`' => '',
                '`value`' => '',
                'type' => 'text',
                'is_required' => 0,
                'is_gui' => 1,
                'options' => '',
                'module' => 'places',
            ),
        ));

        if (!$success) return false;
        else return true;
    // Tags that make no sense when defaulted: visible, markers, center, path, 
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

        if (! $this->install_places()) die("Table install failed.");
        if (! $this->insert_sample_data()) die ("Sample data could not be inserted.");
        if (! $this->install_settings()) die ("Failed to install settings.");
        
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
        return "See the Github repository at http://www.github.com/Tellus/pyro-places" for usage information.";
    }
}
