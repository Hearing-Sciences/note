<?php

namespace NOTE;

class ErrorHandler extends \error {

    private $error;
    private $filepath;

    public function __construct(\error $error)
    {
        $this->error = $error;
        $this->filepath = explode('/', $this->error->file);
    }

    public function detailedMessage()
    {
        //print_r($this->error);
        if ($this->error->message === 'Call to a member function exec() on null') {
            $details = [
                'title' => 'Error in <code>db.php</code>',
                'info' => "
                    <p>It is likely that your file contains one or more <code>&dollar;db->exec</code> calls not enclosed in a function. Check your survey's <code>db.php</code> file and group all of your <code>&dollar;db->exec</code> calls within a single function called <code>db_create_tables(&dollar;db)</code> i.e.</p>
                    <code>
                        function db_create_tables(&dollar;db)
                        {
                            <em>all of your &dollar;db->exec calls</em>
                        }
                    </code>
                    <p class='text-danger h3'>While this error message is visible your survey data will <strong>not</strong> be saved. You have been warned.</p>
                "
            ];
            return $this->showMessage($details);
        }    

        if ($this->error->message === 'Class "Utils" not found') {
            $details = [
                'title' => 'Error calling <code>Utils</code> class',
                'info' => "
                <h4>File name: ".$this->filepath[array_key_last($this->filepath)].", line ".$this->error->line."</h4>
                <p>The <code>Utils</code> class has been removed from NOTE. If you are looking for <code>process_get</code> use the call <code>&dollar;noteSurvey->process_get</code> instead.</p>
                "
            ];
            return $this->showMessage($details);
        }    

        if (substr($this->error->message, 0, 23) === 'Failed opening required') {
            preg_match('/[a-zA-Z0-9_]+\.php/', $this->error->message, $filename);
            $details = [
                'title' => 'Page not found',
                'info' => "
                    <p>One of the survey pages could not be found. The page requested in <code>config.php</code> is:</p>
                    <div class='h4 text-danger'>".
                    $filename[0]
                    ."</div>
                    Please check the configuration file and update this survey.
                "
            ];
            return $this->showMessage($details);
        }    

    }

    private function showMessage($details = [])
    {
        return 
            "<div class='container border border-danger p-4 mt-2'>".
            "<h3>".$details['title']."</h3>".
            $details['info'].
            "</div>"
        ;
    }

}