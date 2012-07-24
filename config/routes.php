<?

/**
 * This module allows for the simple display of maps from Google's API. It
 * allows for run-time tag display (for one-offs) as well as storing locations
 * in more long-term scenarios for easy retrieval.
 * @package places
 */

if (!defined('BASEPATH')) exit('No direct script access allowed');

$route['places/(:num)']			= 'places/$1';