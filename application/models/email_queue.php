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
 * Email que represents que of unsended emails and list of sended emails.
 * 
 * @author Michal Kliment
 * @package Model
 * 
 * @property integer $id
 * @property string $from
 * @property string $to
 * @property string $subject
 * @property string $body
 * @property integer $state
 * @property timestamp $access_time
 */
class Email_queue_Model	extends ORM
{
	/**
	 * New e-mail in queue
	 */
	const STATE_NEW		= 0;
	
	/**
	 * Successfully sent e-mail
	 */
	const STATE_OK		= 1;
	
	/**
	 * Unsuccessfully sent e-mail, almost same as new
	 */
	const STATE_FAIL	= 2;
	
	/**
	 * Returns current email queue, by default 10 e-mails to send
	 * 
	 * @author Michal Kliment
	 * @param integer $count
	 * @return Mysql_Result
	 */
	public function get_current_queue($count = 10)
	{		
		return $this->where('state <> ',self::STATE_OK)
			->orderby('access_time')
			->limit($count,0)
			->find_all();
	}
	
	/**
	 * Returns all sent e-mails
	 * 
	 * @author Michal Kliment
	 * @param integer $limit_from
	 * @param integer $limit_results
	 * @param string $order_by
	 * @param string $order_by_direction
	 * @param string $filter_sql
	 * @return Mysql_Result 
	 */
	public function get_all_sent_emails(
			$limit_from = 0, $limit_results = 50,
			$order_by = 'id', $order_by_direction = 'ASC', $filter_sql='')
	{
		// filter
		$where = "";
		if ($filter_sql != '')
			$where = " AND $filter_sql";
		
		return $this->db->query("
			SELECT * FROM
			(
				SELECT eq.*,
				fuc.user_id AS from_user_id,
				CONCAT(fu.name,' ',fu.surname) AS from_user_name,
				tuc.user_id AS to_user_id,
				CONCAT(tu.name,' ',tu.surname) AS to_user_name
				FROM email_queues eq
				LEFT JOIN contacts fc ON eq.from = fc.value AND fc.type = ?
				LEFT JOIN users_contacts fuc ON fc.id = fuc.contact_id
				LEFT JOIN users fu ON fuc.user_id = fu.id
				LEFT JOIN contacts tc ON eq.to = tc.value AND tc.type = ?
				LEFT JOIN users_contacts tuc ON tc.id = tuc.contact_id
				LEFT JOIN users tu ON tuc.user_id = tu.id
			) eq
			WHERE eq.state = ? $where
			GROUP BY eq.id
			ORDER BY ".$this->db->escape_column($order_by)." $order_by_direction
			LIMIT " . intval($limit_from) . "," . intval($limit_results) . "
		", Contact_Model::TYPE_EMAIL, Contact_Model::TYPE_EMAIL, self::STATE_OK);
	}
	
	/**
	 * Counts all sent e-mails
	 * 
	 * @author Michal Kliment
	 * @param string $filter_sql
	 * @return integer 
	 */
	public function count_all_sent_emails($filter_sql='')
	{
		// filter
		$where = "";
		if ($filter_sql != '')
			$where = " AND $filter_sql";
		
		return $this->db->query("
			SELECT COUNT(*) AS total FROM
			(
				SELECT eq.id FROM
				(
					SELECT eq.*,
					fuc.user_id AS from_user_id,
					CONCAT(fu.name,' ',fu.surname) AS from_user_name,
					tuc.user_id AS to_user_id,
					CONCAT(tu.name,' ',tu.surname) AS to_user_name
					FROM email_queues eq
					LEFT JOIN contacts fc ON eq.from = fc.value AND fc.type = ?
					LEFT JOIN users_contacts fuc ON fc.id = fuc.contact_id
					LEFT JOIN users fu ON fuc.user_id = fu.id
					LEFT JOIN contacts tc ON eq.to = tc.value AND tc.type = ?
					LEFT JOIN users_contacts tuc ON tc.id = tuc.contact_id
					LEFT JOIN users tu ON tuc.user_id = tu.id
				) eq
				WHERE eq.state = ? $where
				GROUP BY eq.id
			) eq
		", Contact_Model::TYPE_EMAIL, Contact_Model::TYPE_EMAIL, self::STATE_OK)
		->current()->total;
	}
	
	/**
	 * Returns all unsent e-mails
	 * 
	 * @author Michal Kliment
	 * @param integer $limit_from
	 * @param integer $limit_results
	 * @param string $order_by
	 * @param string $order_by_direction
	 * @param string $filter_sql
	 * @return Mysql_Result 
	 */
	public function get_all_unsent_emails(
			$limit_from = 0, $limit_results = 50,
			$order_by = 'id', $order_by_direction = 'ASC', $filter_sql='')
	{
		// filter
		$where = "";
		if ($filter_sql != '')
			$where = " AND $filter_sql";
		
		return $this->db->query("
			SELECT * FROM
			(
				SELECT eq.*,
				fuc.user_id AS from_user_id,
				CONCAT(fu.name,' ',fu.surname) AS from_user_name,
				tuc.user_id AS to_user_id,
				CONCAT(tu.name,' ',tu.surname) AS to_user_name
				FROM email_queues eq
				LEFT JOIN contacts fc ON eq.from = fc.value AND fc.type = ?
				LEFT JOIN users_contacts fuc ON fc.id = fuc.contact_id
				LEFT JOIN users fu ON fuc.user_id = fu.id
				LEFT JOIN contacts tc ON eq.to = tc.value AND tc.type = ?
				LEFT JOIN users_contacts tuc ON tc.id = tuc.contact_id
				LEFT JOIN users tu ON tuc.user_id = tu.id
			) eq
			WHERE eq.state <> ? $where
			GROUP BY eq.id
			ORDER BY ".$this->db->escape_column($order_by)." $order_by_direction
			LIMIT " . intval($limit_from) . "," . intval($limit_results) . "
		", Contact_Model::TYPE_EMAIL, Contact_Model::TYPE_EMAIL, self::STATE_OK);
	}
	
	/**
	 * Counts all unsent e-mails
	 * 
	 * @author Michal Kliment
	 * @param string $filter_sql
	 * @return integer 
	 */
	public function count_all_unsent_emails($filter_sql='')
	{
		// filter
		$where = "";
		if ($filter_sql != '')
			$where = " AND $filter_sql";
		
		return $this->db->query("
			SELECT COUNT(*) AS total FROM
			(
				SELECT eq.id FROM
				(
					SELECT eq.*,
					fuc.user_id AS from_user_id,
					CONCAT(fu.name,' ',fu.surname) AS from_user_name,
					tuc.user_id AS to_user_id,
					CONCAT(tu.name,' ',tu.surname) AS to_user_name
					FROM email_queues eq
					LEFT JOIN contacts fc ON eq.from = fc.value AND fc.type = ?
					LEFT JOIN users_contacts fuc ON fc.id = fuc.contact_id
					LEFT JOIN users fu ON fuc.user_id = fu.id
					LEFT JOIN contacts tc ON eq.to = tc.value AND tc.type = ?
					LEFT JOIN users_contacts tuc ON tc.id = tuc.contact_id
					LEFT JOIN users tu ON tuc.user_id = tu.id
				) eq
				WHERE eq.state <> ? $where
				GROUP BY eq.id
			) eq
		", Contact_Model::TYPE_EMAIL, Contact_Model::TYPE_EMAIL, self::STATE_OK)
		->current()->total;
	}
	
	/**
	 * Adds message to the beginning of queue (will be send first)
	 * 
	 * @author Michal Kliment
	 * @param type $from
	 * @param type $to
	 * @param type $subject
	 * @param type $body
	 * @return type 
	 */
	public function push($from, $to, $subject, $body)
	{
		return $this->db->query("
			INSERT INTO email_queues
			SELECT
				NULL, ?, ?, ?, ?, ?,
				FROM_UNIXTIME(UNIX_TIMESTAMP(MIN(access_time))-1)
			FROM email_queues
		", array($from, $to, $subject, $body, self::STATE_NEW));
	}
}