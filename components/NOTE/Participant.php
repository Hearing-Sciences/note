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

    public function __construct($module, $path, $options) 
    {
        $this->dbConnect($path);
        if (isset($options['mode']) && $options['mode'] !== 'data')
        if ($this->readUidFromSession($module) && $this->uidInDb()) {
            $this->isNew = false;
        } else {
            $this->isNew = true;
            $this->createUid($module);
            $this->save();
        }
    }

    /**
     * getUid: retrieve the UID of the participant
     * 
     * This is a getter function so that the property can be protected from outside changes.
     * 
     * @return string the UID of the participant
     */
    public function getUid(): string|null
    {
        return $this->uid;
    }

    public function setLastPageViewed($page)
    {
        $this->lastPage = $page;
        $this->save();
        return true;
    }

    public function getLastPageViewed(): int
    {
        return $this->lastPage;
    }

    /**
     * dbConnect: open a database connection
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
            require_once($path.DIRECTORY_SEPARATOR.'db.php');
            if (function_exists('db_create_tables')) {
                db_create_tables($this->db);
            }
        }
        catch(PDOException $e) {
          // Log error
          echo $e->getMessage();
          return false;
        }
    }

    private function readUidFromSession($module)
    {
        if (isset($_SESSION['uniqueID_'.$module])) {
            $this->uid = $_SESSION['uniqueID_'.$module];
            return true;
        } else {
            return false;
        }
    }

    private function uidInDb()
    {
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

    private function save()
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

    private function createUid($module)
    {
        $this->uid = uniqid($module.'_');
        $_SESSION['uniqueID_'.$module] = $this->uid;
    }

    public function refresh() {
        return $this->uidInDb();
    }

    public function closeDb()
    {
        $this->db = NULL;
    }
}