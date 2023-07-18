<?php

/**
 * Creates and manages NOTE environment variables (not system wide environment)
 * 
 */
class Env {

	/**
	 * Old NOTE array used for compatibility with earlier versions
	 *
	 * @var array
	 */
	public $_NOTE = [];

	/**
	 * URL of page visited parsed as query and path
	 *
	 * @var array
	 */
	private $url = [];
	
	/**
	 * URL of page visited included for compatibility
	 *
	 * @var string|null
	 */
	public $raw_uri;
	
	/**
	 * Base URL, usually / or /dev
	 *
	 * @var string|null
	 */
	public $base_uri;
	
	/**
	 * Request URI, usually the name of the survey folder
	 *
	 * @var string|null
	 */
	public $request_uri;
	
	/**
	 * Query string included for compatibility
	 *
	 * @var string|null
	 */
	public $query_string;
	
	/**
	 * The name of the survey folder
	 *
	 * @var string|null
	 */
	public $module_id = NULL;
	
	/**
	 * Which parent page to display, usually none, data or preview
	 *
	 * @var string|null
	 */
	public $display_id = NULL;
	
	/**
	 * A single extra URL component sometimes used to store extra data
	 *
	 * @var string|null
	 */
	public $other_id = NULL;
	
	/**
	 * Module directory, usually 'modules/<module_id>
	 *
	 * @var string|null
	 */
	public $module_dir;
	
	/**
	 * Module path from URL, usually '/modules/<module_id>'
	 *
	 * @var string|null
	 */
	public $module_path;
	
	/**
	 * Module path from the point of view of the file system
	 *
	 * @var string|null
	 */
	public $fs_module_path;
	
	/**
	 * The same as $other_id and DEPRECATED
	 *
	 * @var string|null
	 */
	public $part_id;
	
	/**
	 * Current page number of survey
	 *
	 * @var int
	 */
	public $page = 0;
	
	/**
	 * UID of participant
	 *
	 * @var string|null
	 */
	public $uid = NULL;

	/**
	 * Initialise the object and variables
	 * 
	 * @param array $server Array of server parameters, usually the $_SERVER superglobal
	 * @return void
	 */
	public function __construct()
	{
		$this->url = parse_url($_SERVER['REQUEST_URI']);
		$this->raw_uri = $_SERVER['REQUEST_URI'];
		$this->base_uri = substr($_SERVER['SCRIPT_NAME'],0,-strlen("index.php"));
		$this->request_uri = $this->url['path'];
		$this->query_string = $this->url['query'];
		$this->processPath($this->split_path($this->url['path']));
		$this->part_id = $this->other_id; 
	}

	/**
	 * Split the URL path by slash character and dump initial value if blank
	 *
	 * @param mixed $path
	 * 
	 * @return array Path divided by directories
	 * 
	 */
	private function split_path(string $path)
	{
		$arguments = explode("/",trim($path,"/"),4);
		if ($arguments[0] == '') {
			array_shift($arguments);
		}
		return $arguments;
	}

	/**
	 * Create an array of NOTE parameters for compatibility with earlier versions
	 *
	 * @return array Array of old-style NOTE parameters
	 * 
	 */
	public function toArray()
	{
		return [
			'RAW_URI' => $this->raw_uri,
			'BASE_URI' => $this->base_uri,
			'REQUEST_URI' => $this->request_uri,
			'QUERY_STRING' => $this->query_string,
			'MODULE_ID' => $this->module_id,
			'DISPLAY_ID' => $this->display_id,
			'OTHER_ID' => $this->other_id,
			'MODULE_DIR' => $this->module_dir,
			'MODULE_PATH' => $this->module_path,
			'FS_MODULE_PATH' => $this->fs_module_path,
			'PART_ID' => $this->part_id,
			'PAGE' => $this->page,
			'UID' => $this->uid,		
		];
	}

	/**
	 * Process the URL path and create display, module and 'other' IDs
	 *
	 * The URL could be in three formats:
	 * - / *module* / *other*
	 * - / *data|preview* / *module* / *other*
	 * - /dev/ *module* / *other*
	 * 
	 * The code must determine which format is in use and then parse the sections of the URL.
	 * 
	 * @param array $path
	 * 
	 * @return void
	 * 
	 */
	private function processPath(array $path)
	{
		if (in_array($path[0], ['dev'])) {
			array_shift($path);
		}
		if (!empty($path[0])) {
			if (in_array($path[0], ['data', 'preview'])) {
				$this->display_id = array_shift($path);
			} else {
				$this->display_id = 'exp';
			}

			$this->module_id = array_shift($path);
			$_mod_dir = __DIR__.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->module_id;
			//print_r($_mod_dir);
			if (file_exists($_mod_dir) && is_dir($_mod_dir)) {
				$this->module_dir = 'modules/'.$this->module_id;
				$this->module_path = $this->base_uri.'modules/'.$this->module_id;
				$this->fs_module_path = $_mod_dir.'/';
			}

			$this->other_id = isset($path[0]) ? $path[0] : NULL;
		}
	}
}


?>
