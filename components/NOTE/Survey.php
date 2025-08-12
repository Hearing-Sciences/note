<?php

namespace NOTE;

class Survey {

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
	public $rawUri;
	
	/**
	 * Base URL, usually / or /dev
	 *
	 * @var string|null
	 */
	public $baseUri;
	
	/**
	 * Document root from $_SERVER
	 *
	 * @var string|null
	 */
	public $documentRoot;

    /**
	 * Request URI, usually the name of the survey folder
	 *
	 * @var string|null
	 */
	public $requestUri;

	/**
	 * The name of the survey folder
	 *
	 * @var string|boolean
	 */
	public $moduleId = false;
	
	/**
	 * Which parent page to display, usually none, data or preview
	 *
	 * @var string|null
	 */
	public $mode = NULL;
	
	/**
	 * A single extra URL component sometimes used to store extra data
	 *
	 * @var string|null
	 */
	public $otherId = NULL;
	
	/**
	 * Module directory, usually 'modules/<moduleId>
	 *
	 * @var string|null
	 */
	public $moduleDir;
	
	/**
	 * Module path from URL, usually '/modules/<moduleId>'
	 *
	 * @var string|null
	 */
	public $modulePath;
	
	/**
	 * Module path from the point of view of the file system
	 *
	 * @var string|null
	 */
	public $fsModulePath;
	
	/**
	 * The same as $otherId and DEPRECATED
	 *
	 * @var string|null
	 */
	public $partId;
	
	/**
	 * Current page number of survey
	 *
	 * @var int
	 */
	private $page = 0;
	
	/**
	 * Is survey valid? Correct URL etc.
	 *
	 * @var int
	 */
	public $isValid = true;

	/**
	 * Is survey in development, i.e. in the /dev/ folder? 
	 *
	 * @var boolean
	 */
	public $isInDevelopment = false;

    /**
     * The survey participant
     */
    public $participant;
	
    /**
     * If the server request was a POST (i.e. there might be data to save)
     */
    public $isPostRequest;

	/**
	 * Enable debug mode to show messages, reports etc.
	 */
	public $debug = false;

	/**
	 * Log - a log object for debug logging
	 */
	public $Log;

	/**
	 * Initialise the object and variables
	 * 
	 * @param array $server Array of server parameters, usually the $_SERVER superglobal
	 * @return void
	 */
	public function __construct($server)
	{
		$this->Log = new Log();
		$this->url = parse_url($server['REQUEST_URI']);
		$this->rawUri = $server['REQUEST_URI'];
        $this->documentRoot = $server['DOCUMENT_ROOT'];
		$this->baseUri = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
		if ($this->baseUri[-1] != '/') {
			$this->baseUri = $this->baseUri.'/';
		}
		$this->requestUri = $this->url['path'];
		$this->processPath($this->splitPath($this->url['path']));
		$this->partId = $this->otherId; 
        if ($this->isValid) {
			$this->Log->append('Survey path <strong>'.$this->modulePath.'</strong> is valid.', 'good');
            $this->participant= new Participant($this->moduleId, $this->fsModulePath, ['mode' => $this->mode]);
			$this->Log->append('Participant object created.', 'good');
            $this->page = $this->participant->getLastPageViewed();
			$this->Log->append('Last page viewed by participant: '.$this->participant->getLastPageViewed(), 'notice');
        }
        $this->isPostRequest = ($server['REQUEST_METHOD'] === 'POST');
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
			'RAW_URI' => $this->rawUri,
			'BASE_URI' => $this->baseUri,
			'REQUEST_URI' => $this->requestUri,
			'MODULE_ID' => $this->moduleId,
			'DISPLAY_ID' => $this->mode,
			'OTHER_ID' => $this->otherId,
			'MODULE_DIR' => $this->moduleDir,
			'MODULE_PATH' => $this->modulePath,
			'FS_MODULE_PATH' => $this->fsModulePath,
			'PART_ID' => $this->partId,
			'PAGE' => $this->page,
			'UID' => (isset ($this->participant) ? $this->participant->getUid() : null),
			'DEBUG' => $this->debug
		];
	}

	/**
	 * splitPath: Split the URL path by slash character and dump initial value if blank
	 *
	 * @param mixed $path
	 * 
	 * @return array Path divided by directories
	 * 
	 */
	private function splitPath(string $path)
	{
		$arguments = explode("/",trim($path,"/"),4);
		if ($arguments[0] == '') {
			array_shift($arguments);
		}
		return $arguments;
	}

	/**
	 * processPath: Process the URL path and create display, module and 'other' IDs
	 *
	 * The URL could be in three formats:
	 * - / *module* / *other*
	 * - / *data|preview|run* / *module* / *other*
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
		if (empty($path)) {
            $this->isValid = false;
			return;
		}

        // Check for development 'mode'
		if (in_array($path[0], ['dev'])) {
			$this->isInDevelopment = true;
			array_shift($path);
		}

        // Look for a display ID. This might still be NULL thanks to old conventions
		if (!empty($path[0]) && in_array($path[0], ['data', 'preview', 'run'])) {
			$this->mode = array_shift($path);
        } else {
            $this->mode = 'run';
        }

        if (!empty($path[0])) {
            $this->moduleId = array_shift($path);
			if ($this->isInDevelopment) {
				$modDir = $this->documentRoot.DIRECTORY_SEPARATOR.'dev'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->moduleId;
			} else {
				$modDir = $this->documentRoot.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$this->moduleId;
			}

			$modDir = str_replace('\\', '/', $modDir);

            if (file_exists($modDir) && is_dir($modDir)) {
				$this->moduleDir = substr($this->baseUri, 1).'modules/'.$this->moduleId;
				$this->modulePath = $this->baseUri.'modules/'.$this->moduleId;
				$this->fsModulePath = $modDir.'/';
			} else {
                // Directory is invalid
                $this->isValid = false;
            }

			$this->otherId = isset($path[0]) ? $path[0] : NULL;
		} else {
            // No survey module - this is required
            $this->isValid = false;
        }
	}

	/**
	 * process_get: Take the server GET parameters and return specified variables as an array
	 * 
	 * @param Array $vars Variables to look for
	 * @return Array $returns Variables found
	 */
	public function process_get(array $vars) 
	{
		$return_vars = [];
		if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($this->url['query'])) {
			// Parse the query string into an array called $gets
			parse_str($this->url['query'], $gets);
			// For each of the variables requested, look for a GET parameter
			foreach ($vars as $var) {
				if (isset($gets[$var])) {
					// Sanitise the GET parameter and add it to the array of return values
					$return_vars[$var] = htmlspecialchars(trim($gets[$var]));
				} else {
					$return_vars[$var] = NULL;
				}
			}
		}
		return $return_vars;
	}	

	/**
	 * getPage: getter function for $this->page
	 * 
	 * @return Int the current page as stored in $this->page
	 */
	public function getPage(): int
	{
		return $this->page;
	}

	/**
	 * setPage: setter function for $this->page
	 * 
	 * @param Int $page the page number of the current page
	 */
	public function setPage(int $page)
	{
		$this->page = $page;
	}

	/**
	 * db_check_against_fields: check a combination of data config, table and page to check all fields match
	 * 
	 * @param Array $post the $_POST array
	 * @param Array $_NOTE_MODULE module definition
	 */
	public function db_check_against_fields(&$post, &$_NOTE_MODULE) 
	{
		$page_id = $this->getPage();
		$db = $this->participant->db;

		if ($_NOTE_MODULE['DEBUG'] === false) {
			return;
		}

		if (!is_array($_NOTE_MODULE['PAGES'][$page_id])) {
			return;
		}

		if (!isset($_NOTE_MODULE['PAGES'][$page_id]['FIELDS'])) {
				print '<div class="container p-3 border border-3 border-warning">';
				print '<h3>Debug Warning</h3><p>Data fields are not defined for the page <strong>page_'.$_NOTE_MODULE['PAGES'][$page_id]['PAGE'].'.php</strong> in <em>config.php</em>. Basic testing could not be completed.</p>';
				print '</div>';
			return;
		}

		if (!isset($_NOTE_MODULE['PAGES'][$page_id]['TABLE'])) {
				print '<div class="container p-3 border border-3 border-warning">';
				print '<h3>Debug Warning</h3><p>The target table is not defined for the page <strong>page_'.$_NOTE_MODULE['PAGES'][$page_id]['PAGE'].'.php</strong> in <em>config.php</em>. Basic testing could not be completed.</p>';
				print '</div>';
			return;
		}

		$query = 'PRAGMA table_info('.$_NOTE_MODULE['PAGES'][$page_id]['TABLE'].');';
		$result = $db->query($query)->fetchAll();
		$report = [];

		$db_fields = [];
		foreach($result as $record) {
			$db_fields[] = $record['name'];
		}

		if (count($db_fields) == 0) {
				print '<div class="container p-3 border border-3 border-warning">';
				print '<h3>Debug Warning</h3><p>The target table does not exist or contains no columns. Basic testing cannot be completed.</p>';
				print '</div>';
			return;
		}

		if (isset($_NOTE_MODULE['SPECIAL_FIELDS']['COMMON']) && is_array($_NOTE_MODULE['SPECIAL_FIELDS']['COMMON'])) {
			$php_fields = array_merge(array_keys($_NOTE_MODULE['PAGES'][$page_id]['FIELDS']), array_keys($_NOTE_MODULE['SPECIAL_FIELDS']['COMMON'])) ;
		}

		$post_fields = array_keys($post);

		//print_r($db_fields);
		//print_r($php_fields);
		//print_r($post_fields);

		$fields = array_unique(array_merge($db_fields, $php_fields, $post_fields));
		if (isset($_NOTE_MODULE['SPECIAL_FIELDS']['IGNORE']) && is_array($_NOTE_MODULE['SPECIAL_FIELDS']['IGNORE'])) {
			$fields = array_diff($fields, array_keys($_NOTE_MODULE['SPECIAL_FIELDS']['IGNORE']));
		}

		foreach($fields as $field) {
			$message = [];
			if (in_array($field, $db_fields) && !in_array($field, $php_fields)) {
				$message[] = 'Field <strong>'.$field.'</strong> is in the database but not in the configuration definition.';
			}
			if (!in_array($field, $db_fields) && in_array($field, $php_fields)) {
				$message[] = 'Field <strong>'.$field.'</strong> is in the configuration definition but no database column can be found.';
			}
			if (in_array($field, $db_fields) && !in_array($field, $post_fields)) {
				$message[] = 'Field <strong>'.$field.'</strong> is in the database but not in the submitted data.';
			}
			if (in_array($field, $post_fields) && !in_array($field, $db_fields)) {
				$message[] = 'Field <strong>'.$field.'</strong> is in the submitted data but no database column can be found.';
			}
			if (in_array($field, $post_fields) && !in_array($field, $php_fields)) {
				$message[] = 'Field <strong>'.$field.'</strong> is in the submitted data but not in the configuration definition.';
			}
			if (in_array($field, $php_fields) && !in_array($field, $post_fields)) {
				$message[] = 'Field <strong>'.$field.'</strong> is in the configuration definition but not the submitted data.';
			}
			if (count($message) > 0) {
				foreach($message as $text) {
					Callout::display(
					'danger',
					[
						'heading' => 'ALERT: possible missing data',
						'content' => $text
					]
					);
				}
			}
		}

	}

}