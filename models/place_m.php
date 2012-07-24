<?php

/**
 * This module allows for the simple display of maps from Google's API. It
 * allows for run-time tag display (for one-offs) as well as storing locations
 * in more long-term scenarios for easy retrieval.
 * @package places
 */

/**
 * A generic location.
 * @package places
 */
class Place_m extends MY_Model
{
	/**
	 * Wrapper for get_all, making sure the results are ordered.
	 * @return type
	 */
	public function get_all()
	{
		$ret = $this->db->order_by('name', 'ASC')
						->get($this->_table)
						->result();
		return $ret;
	}

	/**
	 * Pretty much a stub for Create, but it allows
	 * non-breaking overrides later on.
	 * @param Array $data name[, description, address]
	 * @return int New id.
	 */
	public function create($data)
	{
		return $this->insert($data);
	}
}
