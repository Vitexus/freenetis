<?php defined('SYSPATH') or die('No direct script access.');
/*
 * This file is part of open source system FreenetIS
 * and it is release under GPLv3 licence.
 * 
 * More info about licence can be found:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * 
 * More info about project can be found:
 * http://www.freenetis.org/
 * 
 */

/**
 * Members domicile connect member with his domicile address point.
 * 
 * @author Michal Kliment
 * @package Model
 * 
 * @property integer $id
 * @property integer $member_id
 * @property Member_Model $member
 * @property integer $address_point_id
 * @property Address_point_Model $address_point
 */
class Members_domicile_Model extends ORM
{
	protected $belongs_to = array('address_point', 'member');
}
