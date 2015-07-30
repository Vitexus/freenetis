<?php defined('SYSPATH') or die('No direct script access.');
/*
 * This file is part of open source system FreenetIS
 * and it is released under GPLv3 licence.
 * 
 * More info about licence can be found:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * 
 * More info about project can be found:
 * http://www.freenetis.org/
 * 
 */

require dirname(__FILE__) . '/Fio_Bank_Statement_File_Importer.php';
require dirname(__FILE__) . '/Fio/FioParser.php';

/**
 * FIO importer for statements in CSV format that are obtained from the FIO
 * e-banking portal.
 * 
 * It uses old version of driver for parsing.
 * 
 * @author Ondrej Fibich
 * @since 1.1
 */
class Csv_Fio_Bank_Statement_File_Importer extends Fio_Bank_Statement_File_Importer
{
	
	/**
	 * Data reprezentation of import.
	 *
	 * @var array
	 */
	private $data = NULL;
	
	/*
	 * @Override
	 */
	protected function check_file_data_format()
	{
		// reset
		$this->data = NULL;
		// convert encoding
		$this->set_file_data(iconv('cp1250', 'UTF-8', $this->get_file_data()));
		// parse (we have no function for checking)
		try
		{
			// parse
			$this->data = FioParser::parseCSV($this->get_file_data());
			
			// correct data
			foreach ($this->data as &$row)
			{
				if ($row['mena'] != 'CZK')
				{
					throw new Exception(__('Unknown currency %s!', $row['mena']));
				}

				// convert date
				if (preg_match('/^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{4}\.$/', $row['datum']) !== FALSE)
				{
					$date_arr = explode('.', $row['datum']);
					$timestamp = mktime(0, 0, 0, $date_arr[1], $date_arr[0], $date_arr[2]);
					$row['datum'] = date('Y-m-d', $timestamp);
				}

				// only transfer from Fio to Fio have 'nazev_protiuctu'
				// for accounts in other banks we have to derive account name
				if (!$row['nazev_protiuctu'] && $row['identifikace'])
				{
					$row['nazev_protiuctu'] = $row['identifikace'];
				}

				// convert from cents
				$row['castka'] /= 100; 

			}
			
			// ok
			return TRUE;
		}
		catch (Exception $e)
		{
			$this->data = NULL;
			$this->add_exception_error($e, FALSE);
			return FALSE;
		}
	}

	/*
	 * @Override
	 */
	protected function get_header_data()
	{
		if (empty($this->data))
			throw new InvalidArgumentException('Check CSV first');
		
		$fio_ph = FioParser::getListingHeader();
		
		$hd = new Header_Data($fio_ph['account_nr'], $fio_ph['bank_nr']);
		
		$hd->currency = 'CZK';
		$hd->openingBalance = $fio_ph['opening_balance'];
		$hd->closingBalance = $fio_ph['closing_balance'];
		$hd->dateStart = $fio_ph['from'];
		$hd->dateEnd = $fio_ph['to'];
		
		return $hd;
	}

	/*
	 * @Override
	 */
	protected function parse_file_data()
	{
		// already parsed
		return !empty($this->data);
	}

	/*
	 * @Override
	 */
	protected function get_parsed_transactions()
	{
		return $this->data;
	}
	
}