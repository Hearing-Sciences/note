<?php

namespace NOTE;

class ErrorHandler extends \error {

    private $error;

    public function __construct(\error $error)
    {
        $this->error = $error;
    }

    public function pageNotFoundMessage()
    {
        //print_r($this->error);
        preg_match('/[a-zA-Z0-9_]+\.php/', $this->error->getMessage(), $filename);
        return "
            <div class='container border border-danger p-4'>
            <h3>Page not found</h3>
            <p>One of the survey pages could not be found. The page requested in <code>config.php</code> is:</p>
            <div class='h4 text-danger'>".
            $filename[0]
            ."</div>
            Please check the configuration file and update this survey.
            </div>
        ";
    }

    public function detailedMessage()
    {
        //print_r($this->error);
        if ($this->error->getMessage() === 'Call to a member function exec() on null') {
            return "
                <div class='container border border-primary p-4'>
                <h3>Error in <code>db.php</code></h3>
                <p>It is likely that your file contains one or more <code>&dollar;db->exec</code> calls not enclosed in a function. Check your survey's <code>db.php</code> file and group all of your <code>&dollar;db->exec</code> calls within a single function called <code>db_create_tables(&dollar;db)</code> i.e.</p>
                <code>
                    function db_create_tables(&dollar;db)
                    {
                        <em>all of your &dollar;db->exec calls</em>
                    }
                </code>
                </div>
            ";
        }    
    }

}