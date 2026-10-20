<?php

namespace NOTE;

use \PDO;
use \PDOException;

class Participant {
   	/**
	 * UID of participant
	 *
	 * @var string|null
	 */
	private $uid = NULL;

   	/**
	 * If participant is new
	 *
	 * @var boolean
	 */
    private $isNew = false;

    private $lastPage = 0;

    public $db;

    public $dbInfo;

    /**
     * __construct: create the object
     * 
     * @param String $module The name of the module (also the folder name)
     * @param String $path The file system path of the module
     * @param Array $options An array of options to build the participant
     * 
     * @return Void
     */
    public function __construct($module, $path, $options) 
    {
        $this->dbConnect($path);
        $this->createCoreTables();
        $this->createUserTables($path);
        if (isset($options['mode']) && $options['mode'] !== 'data') {
            if ($this->getUidFromSession($module) && $this->load()) {
                $this->isNew = false;
            } else {
                $this->isNew = true;
                $this->createUid($module);
                $this->save();
            }
        }
    }

    /**
     * getUid: retrieve the UID of the participant
     * 
     * This is a getter function so that the property can be protected from outside changes.
     * 
     * @return String the UID of the participant
     */
    public function getUid(): string|null
    {
        return $this->uid;
    }

    /**
     * setLastPageViewed: set the index of the last page viewed by the participant
     * 
     * @return Boolean the UID of the participant
     */
    public function setLastPageViewed($page)
    {
        $this->lastPage = $page;
        return true;
    }

    /**
     * getLastPageViewed: retrieve the index of the last page viewed by the participant
     * 
     * @return Integer the index of the last page viewed by the participant
     */
    public function getLastPageViewed(): int
    {
        return $this->lastPage;
    }

    /**
     * dbConnect: open a database connection and create tables if necessary*
     * 
     * The table creation scripts are run every time a page is loaded. *The 'if 
     * necessary' part is handled by the queries themselves. The SQL *must* contain 
     * 'IF NOT EXISTS' to avoid errors or wiping out existing tables.
     * 
     * @param string $path the path of the folder containing the database
     * @return null 
     */
    private function dbConnect($path)
    {
        try {
            // Connect and initialise if necessary
            $this->db = new PDO('sqlite:'.$path.DIRECTORY_SEPARATOR.'database.sqlite3');
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            //$this->db->query('pragma journal_mode = WAL');
            $this->db->query('pragma synchronous = normal');
            $this->db->query('pragma temp_store = memory');
            $this->db->query('pragma mmap_size = 30000000000');
            $this->db->query('pragma page_size = 32768');
        }
        catch(PDOException $e) {
          // Log error
          echo $e->getMessage();
          return false;
        }
    }

    /**
     * createUserTables: create user-defined tables
     * 
     * The table creation scripts are run every time a page is loaded. 
     * The SQL *must* contain 'IF NOT EXISTS' to avoid errors 
     * or wiping out existing tables.
     * 
     * @return null 
     */
    private function createUserTables($path) {
        require_once($path.DIRECTORY_SEPARATOR.'db.php');
        if (function_exists('db_create_tables')) {
            db_create_tables($this->db);
        }
    }

    /**
     * createCoreTables: create NOTE tables
     * 
     * This function creates the visitors table.
     * 
     * The table creation scripts are run every time a page is loaded. 
     * The SQL *must* contain 'IF NOT EXISTS' to avoid errors 
     * or wiping out existing tables.
     * 
     * @return null 
     */
    private function createCoreTables() {
        $this->db->query('CREATE TABLE IF NOT EXISTS "visitors" (
            "id"	INTEGER AUTO_INCREMENT PRIMARY KEY,
            "uniqueID"	TEXT,
            "participant_id"	TEXT,
            "study_id"	TEXT,
            "session_id"	TEXT,
            "ipaddr"	TEXT,
            "lastPage"	INTEGER,
            "timestamp"	DATETIME DEFAULT CURRENT_TIMESTAMP
        )');
    }

    /**
     * getUidFromSession: Retrieve the user's UID from the session
     * 
     * The module name is appended to the UID
     * 
     * @param String $module The name of the module (also the folder name)
     * 
     * @return Bool 
     */
    private function getUidFromSession($module)
    {
        if (isset($_SESSION['uniqueID_'.$module])) {
            $this->uid = $_SESSION['uniqueID_'.$module];
            return true;
        } else {
            return false;
        }
    }

    /**
     * load: Populate object data from the database
     * 
     * @return Bool 
     */
    private function load()
    {
        if (!$this->uid) {
            return false;
        }
        $qry = $this->db->prepare('SELECT * FROM visitors WHERE uniqueID=:uid LIMIT 1');
        $qry->execute([':uid' => $this->uid]);
        $result = $qry->fetch();
        if (!empty($result)) { // Maybe more checks in future
            $this->lastPage = $result['lastPage'];
            $this->dbInfo = $result;
            return true;
        } else {
            return false;
        }
    }

    /**
     * save: Save the participant information into the visitors table
     * 
     * @return Bool
     */
    public function save()
    {
        if ($this->isNew) {
            $qry = $this->db->prepare('INSERT INTO visitors (uniqueID, lastPage, timestamp) VALUES (:uid, :lastPage, :timestamp);');
        } else {
            $qry = $this->db->prepare('UPDATE visitors SET lastPage=:lastPage, timestamp=:timestamp WHERE uniqueID=:uid');
        }
        $result = $qry->execute([
            ':uid' => $this->uid,
            ':lastPage' => $this->lastPage,
            ':timestamp' => date('Y-m-d H:i:s')
        ]);
        return $result;
    }

    /**
     * createUid: make a UID for the participant and write it to the session
     * 
     * @param String $module module name (also the survey folder name)
     */
    private function createUid($module)
    {
        $this->uid = uniqid($module.'_');
        $_SESSION['uniqueID_'.$module] = $this->uid;
    }

    /**
     * refresh: Update the participant information 
     * 
     * @return Bool
     */
    public function refresh() {
        return $this->load();
    }

    /**
     * closeDb: close and remove the database connection
     * 
     * For SQLite nullifying the connection variable is enough to close the connection.
     */
    public function closeDb()
    {
        $this->db = NULL;
    }
}