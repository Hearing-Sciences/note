<?php

class Env {

	public $_NOTE = [];
	private $url;
	public $raw_uri;
	public $base_uri;
	public $request_uri;
	public $query_string;
	public $module_id = NULL;
	public $display_id = NULL;
	public $other_id = NULL;
	public $module_dir;
	public $module_path;
	public $fs_module_path;
	public $part_id;
	public $page = 0;
	public $uid = NULL;

	public function __construct()
	{
		$this->url = parse_url($_SERVER['REQUEST_URI']);
		// Define global state array
		$this->raw_uri = $_SERVER['REQUEST_URI'];
		$this->base_uri = substr($_SERVER['SCRIPT_NAME'],0,-strlen("index.php"));
		//$this->request_uri = substr($_SERVER['REQUEST_URI'],strlen($this->base_uri));
		$this->request_uri = $this->url['path'];
		$this->query_string = $this->url['query'];

		$this->processPath();
		$this->part_id = $this->other_id; // PART_ID is deprecated but set to OTHER_ID for now.
	}

	private function split_path($path)
	{
		$arguments = explode("/",trim($path,"/"),4);
		if ($arguments[0] == '') {
			array_shift($arguments);
		}
		return $arguments;
	}

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

	private function processPath()
	{
		/**
		 * The URL could be in three formats:
		 * /<MODULE>/<OTHER>
		 * /<data|preview>/<MODULE>/<OTHER>
		 * /dev/<MODULE><OTHER>
		 * The code must determine which format is in use and then parse the sections of the URL.
		 */
		$path = $this->split_path($this->url['path']);
		$dev = NULL;
		if (!empty($path[0])) {
			if (in_array($path[0], ['data', 'preview'])) {
				$this->display_id = array_shift($path);
			} else {
				$this->display_id = 'exp';
				if (in_array($path[0], ['dev'])) {
					//$dev = 'dev'.DIRECTORY_SEPARATOR;
					array_shift($path);
				} 
			}

			$this->module_id = array_shift($path);
			$_mod_dir = __DIR__.DIRECTORY_SEPARATOR.$dev.'modules'.DIRECTORY_SEPARATOR.$this->module_id;
			//print_r($_mod_dir);
			if (file_exists($_mod_dir) && is_dir($_mod_dir)) {
				$this->module_dir = $dev.'modules/'.$this->module_id;
				$this->module_path = $this->base_uri.$dev.'modules/'.$this->module_id;
				$this->fs_module_path = $_mod_dir.'/';
			}

			$this->other_id = isset($path[0]) ? $path[0] : NULL;
		}
	}
}


?>
