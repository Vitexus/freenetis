<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Loads and displays Kohana view files. Can also handle output of some binary
 * files, such as image, Javascript, and CSS files.
 *
 * $Id: View.php 1911 2008-02-04 16:13:16Z PugFish $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class View {

	// The view file name and type
	protected $kohana_filename = FALSE;
	protected $kohana_filetype = FALSE;

	// Set variables
	protected $data = array();
	protected static $global_data = array();
	
	/**
	 * Allowed filetypes
	 *
	 * @var array
	 */
	private static $allowed_filetypes = array
	(
		'gif',
		'jpg', 'jpeg',
		'png',
		'tif', 'tiff',
		'swf',
		'htm', 'html',
		'css',
		'js'
	);

	/**
	 * Creates a new View using the given parameters.
	 *
	 * @param   string  view name
	 * @param   array   pre-load data
	 * @param   string  type of file: html, css, js, etc.
	 */
	public static function factory($name, $data = NULL, $type = NULL)
	{
		return new View($name, $data, $type);
	}

	/**
	 * Attempts to load a view and pre-load view data.
	 *
	 * @throws  Kohana_Exception  if the requested view cannot be found
	 * @param   string  view name
	 * @param   array   pre-load data
	 * @param   string  type of file: html, css, js, etc.
	 * @return  void
	 */
	public function __construct($name, $data = NULL, $type = NULL)
	{
		if (empty($type))
		{
			// Load the filename and set the content type
			$this->kohana_filename = Kohana::find_file('views', $name, TRUE);
			$this->kohana_filetype = EXT;
		}
		else
		{
			// Check if the filetype is allowed by the configuration
			if ( ! in_array($type, self::$allowed_filetypes))
				throw new Kohana_Exception('core.invalid_filetype', $type);

			// Load the filename and set the content type
			$this->kohana_filename = Kohana::find_file('views', $name.'.'.$type, TRUE, $type);
			$this->kohana_filetype = upload::$mimes[$type];
			$this->kohana_filetype = empty($this->kohana_filetype) ? $type : $this->kohana_filetype;
		}

		if (is_array($data) AND ! empty($data))
		{
			// Preload data using array_merge, to allow user extensions
			$this->data = array_merge($this->data, $data);
		}

		Log::add('debug', 'View Class Initialized ['.$name.']');
	}

	/**
	 * Sets a view variable.
	 *
	 * @param   string|array  name of variable or an array of variables
	 * @param   mixed         value when using a named variable
	 * @return  object
	 */
	public function set($name, $value = NULL)
	{
		if (func_num_args() === 1 AND is_array($name))
		{
			foreach($name as $key => $value)
			{
				$this->__set($key, $value);
			}
		}
		else
		{
			$this->__set($name, $value);
		}
		return $this;
	}

	/**
	 * Sets a bound variable by reference.
	 *
	 * @param   string   name of variable
	 * @param   mixed    variable to assign by reference
	 * @return  object
	 */
	public function bind($name, & $var)
	{
		$this->data[$name] =& $var;

		return $this;
	}

	/**
	 * Sets a view global variable.
	 *
	 * @param   string|array  name of variable or an array of variables
	 * @param   mixed         value when using a named variable
	 * @return  object
	 */
	public function set_global($name, $value = NULL)
	{
		if ( ! is_array($name))
		{
			$name = array($name => $value);
		}

		foreach ($name as $key => $value)
		{
			self::$global_data[$key] = $value;
		}

		return $this;
	}

	/**
	 * Magically sets a view variable.
	 *
	 * @param   string   variable key
	 * @param   string   variable value
	 * @return  void
	 */
	public function __set($key, $value)
	{
		if ( ! isset($this->$key))
		{
			$this->data[$key] = $value;
		}
	}

	/**
	 * Magically gets a view variable.
	 *
	 * @param  string  variable key
	 * @return mixed   variable value if the key is found
	 * @return void    if the key is not found
	 */
	public function __get($key)
	{
		if (isset($this->data[$key]))
		{
			return $this->data[$key];
		}
	}

	/**
	 * Magically converts view object to string.
	 *
	 * @return  string
	 */
	public function __toString()
	{
		return $this->render();
	}

	/**
	 * Renders a view.
	 *
	 * @param   boolean   set to TRUE to echo the output instead of returning it
	 * @param   callback  special renderer to pass the output through
	 * @return  string    if print is FALSE
	 * @return  void      if print is TRUE
	 */
	public function render($print = FALSE, $renderer = FALSE)
	{
		if (is_string($this->kohana_filetype))
		{
			// Merge global and local data, local overrides global with the same name
			$data = array_merge(self::$global_data, $this->data);

			// Load the view in the controller for access to $this
			$output = Kohana::instance()->_kohana_load_view($this->kohana_filename, $data);

			if ($renderer == TRUE AND is_callable($renderer, TRUE))
			{
				// Pass the output through the user defined renderer
				$output = call_user_func($renderer, $output);
			}

			if ($print == TRUE)
			{
				// Display the output
				echo $output;
				return;
			}
		}
		else
		{
			// Set the content type and size
			header('Content-Type: '.$this->kohana_filetype[0]);

			if ($print == TRUE)
			{
				if ($file = fopen($this->kohana_filename, 'rb'))
				{
					// Display the output
					fpassthru($file);
					fclose($file);
				}
				return;
			}

			// Fetch the file contents
			$output = file_get_contents($this->kohana_filename);
		}

		return $output;
	}

} // End View