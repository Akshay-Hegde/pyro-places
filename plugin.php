<?php 

/**
 * This module was built specifically with Danish volleyball leagues in mind,
 * but the abstraction should be distant enough to allow for practically any
 * sport that has a generic tournament structure, different leagues (or series,
 * or .... you know), players, coaches and a team description.
 *
 * @author Johannes L. Borresen
 * @website http://the.homestead.dk
 * @package sports
 **/

if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Plugin part. It's very limited in what I could reasonably accomplish, and 
 * I'm thinking if maybe separate modules for each general model/controller
 * is better.
 * @package sports
 */
class Plugin_Sports extends Plugin
{
	/**
	 * Item List
	 * Usage:
	 * 
	 * {{ sample:items limit="5" order="asc" }}
	 *      {{ id }} {{ name }} {{ slug }}
	 * {{ /sample:items }}
	 *
	 * @return	array
	 */
	public function items()
	{
		$limit = $this->attribute('limit');
		$order = $this->attribute('order');
		
		return $this->db->order_by('name', $order)
						->limit($limit)
						->get('sample_items')
						->result_array();
	}
	
	/**
	 * Full team list (careful on those db resources, dude).
	 *
	 * Although the usage of tag pairs insinuates looping behaviour, your will
	 * actually only get one hit. This seems like it's such a common occurrence
	 * that we should have something proper for it.
	 * Use the ids to pull from leagues, training times and the internal
	 * PyroCMS users model.
	 *
	 * Usage:
	 * {{ sports:teams limit="5" order_by="name" order="asc" }}
	 *		{{ id }} {{ name }} {{ league_id }} {{ head_coach_id }} {{ description }}
	 * {{ /sports:teams }}
	 **/
	 public function teams()
	 {

	 	return $this->db->order_by('name', 'ASC')
	 					->get('sports_teams')
	 			        ->result();
	 }
	
	/**
	 * Retrieves a single team's data.
	 *
	 * Usage (partial):
	 * {{ sports:team_single id="3" }}
	 *		Team: {{ name }}
	 *		League: {{ sports:league_name id={league} }}
	 *		Head coach: {{ user:display_name id={head_coach} }}
	 * {{ /sports:team_single }}
	 **/
	public function team_single()
	{
		$id = $this->attribute('id');
		
		return $this->db
						->get_where('sports_teams', array('id' => $id))
						->result_array();
	}

	/**
	 * Function for the league tag.
	 * Usage:
	 * {{ sports:league id="<id>" [get="<field>"] }}
	 * Given the monolithic structure of the module, I can't simply do
	 * sports:league:field id="" - yet.
	 * @return The value of the requested field in the referenced league.
	 */
	public function league()
	{
		$data = $this->db->from('sports_leagues')
						 ->where('id', $this->attribute('id'))
						 ->get()
						 ->row_array();

		return $data[$this->attribute('get')];
	}

	/**
	 * Catch-all for unknown tags.
	 * @param string $name Name of the function that was attampted to be
	 * called.
	 * @param array $arguments  Array of function arguments.
	 * @return null
	 */
	public function _call($name, $arguments)
	{
		var_dump($name, $arguments);die();
	}
}

/* End of file plugin.php */
