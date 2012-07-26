<?php 

/**
 * This module allows for the simple display of maps from Google's API. It
 * allows for run-time tag display (for one-offs) as well as storing locations
 * in more long-term scenarios for easy retrieval.
 * @package places
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Plugin part. The philosophy is that I want to offer up everything Google
 * Maps Static API does while being sensible about the structure. Thus you will
 * often find that you must wrap another PyroCMS tag around these tags - one
 * clear example is the case with linking to a map. places:anchor does not
 * exist because url:anchor segments="URL" would do this just fine.
 * @package sports
 */
class Plugin_Places extends Plugin
{

	/**
	 * The base Google API url. Should not be changed, but it's nice to have it
	 * somewhere by itself.
	 */
	public $base_url = 'http://maps.googleapis.com/maps/api/staticmap';

    /**
     * We need to read settings and locations when reacting to tags. The prior
     * because it contains sitewide defaults, the latter to react to places
     * tags that refer to places by URL segments or direct primary keys.
     */
	public function __construct()
	{
		// parent::__construct();
		$this->load->model(array(
			'settings_m',
			'place_m'
		));

        $this->load->helper('html');
	}

	/**
	 * Constructs a full Google Maps URL from the attributes passed and module
	 * settings saved as defaults.
	 * @return string
	 */
	protected function construct_url()
	{
		$url = $this->base_url . '?';

		$url_segments = array('sensor' => 'false');

		$location = $this->get_place();

		// If a location was passed through attributes, one set of conditions pass.
		if (isset($location))
		{
			// If a center was also passed, the location's address becomes a marker.
			if ($this->attribute('center'))
				$seg = 'markers';
			else
				$seg = 'center';

			// We place the address in whichever of the two we decided on.
			$url_segments[$seg] = $location->address;

			// If markers were defined. If the address was a marker, append the others.
			// If the address was the center, set the markers.
			if ($this->attribute('markers'))
			{
				switch($seg)
				{
					case 'markers':
					{
						// Append markers, set possible center.
						$url_segments['markers'] .= '|'.$this->attribute('markers', '');
						break;
					}
					case 'center':
					{
						// Set markers.
						$url_segments['markers'] = $this->attribute('markers');
						break;
					}
				}
			}
		}
		else // If no valid location was passed, we check that either markers or center was set.
		{
			// At least one of either marker or center must be set.
			if (!($this->attribute('markers') or $this->attribute('center')))
				return "<pre>Plugin error: either center or markers must be added as an attribute.</pre>"; // Die instantly if missing key.
			else
			{
				if ($add = $this->attribute('markers'))
					$url_segments['markers'] = $add;
				if ($add2 = $this->attribute('center'))
					$url_segments['center'] = $add2;
			}
		}

		// Everything else is (in our plugin) either optional or pre-defined in
		// the settings.
		$settings = $this->settings_m->get_many_by(array('module' => 'places'));

		foreach($settings as $setting)
		{
			// Our slug conversion here is to easily 1-to-1 map the API parameters
			// to the tag attributes.
			if (empty($setting->value)) continue;
			$slug = substr($setting->slug, strlen('places_tag_'));
			$new_val = ($val = $this->attribute($slug) ? $val : $setting->value);
			$url_segments[$slug] = $new_val;
		}

		// Hardcode add sensor.
		$url_segments['sensor'] = 'false';

		foreach($url_segments as $uk=>$uv)
		{
			// $url = $url.urlencode($uk).'='.urlencode($uv).'&';
			$url .= htmlentities("$uk=$uv&");
		}

		return $url;
	}

    /**
     * Determines if a place ID was passed directly (id attribute) or indirectly
     * (segment attribute). If so, extract the id.
     */
	protected function get_place()
	{
        if ($id = $this->attribute('id') and
            $segment = $this->uri->segment($this->attribute('segment')))
                return "Plugin error: id and segment cannot be combined.";

        $id = $id ? : $id : $segment;

		// If neither id or segment was passed, we cannot get a location.
		if (!$id) return null;
		else
		{
			$p = $this->place_m->get($id);
			return $p[0];
		}
	}

    /**
     * Returns an URL for a Google Maps API call/image.
     * Usage:
     * {{ places:url (id="<id>"|segment="<segment>"|markers="<marker_1>|<marker_n>"|center="<center">) [, opt=val]
     * At MOST one of id or segment.
     * If neither id or segment is passed, at LEAST one of markers or center must be
     * passed.
     * @return string
     */
	public function url()
	{
		return $this->construct_url();
	}

    /**
     * Returns an anchor element, <a>, that links to a Google Map. See url() in this
     * file for a list of places-specific attributes. Furthermore, this function
     * is otherwise a direct proxy to url:anchor - pass title and class into this
     * tag as you would that tag.
     */
	public function anchor()
	{
		$title = $this->attribute('title', '');
		$class = $this->attribute('class', '');

		$class = !empty($class) ? 'class="'.$class.'"' : '';

		return anchor($this->construct_url(), $title, $class);
	}

    /**
     * Returns an image element, <img>, where the source has been set to the places
     * attributes passed. You may add a class via the class attribute.
     */
    public function image()
    {
        $class = $this->attribute('class', '');

        return img(array(
            'src' => $this->url(),
            'class' => !empty($class) ? $class : '',
        ));
    }
}

/* End of file plugin.php */
