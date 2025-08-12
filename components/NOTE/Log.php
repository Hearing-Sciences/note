<?php

namespace NOTE;

use DateTime;

class Log {

    private $log;
    private $classes = [
        'notice' => 'info',
        'warning' => 'warning',
        'error' => 'danger',
        'debug' => 'dark',
        'good' => 'success'
    ];

    public function append($message, $class='notice')
    {
        $class = (in_array($class, array_keys($this->classes))) ? $class : 'notice';
        $ts = new DateTime();
        $this->log[] = [
            'class' => $class,
            'message' => $message,
            'timestamp' => $ts->format('Y-m-d H:i:s.v')
        ];
    }

    public function render($class = 'all')
    {
        $list = [];
        foreach($this->log as $entry) {
            if ($class == $entry['class'] || $class == 'all') {
                $list[] = '<li class="text-'.$this->classes[$entry['class']].'">('.$entry['timestamp'].') '.strtoupper($this->classes[$entry['class']]).': '.$entry['message'].'</li>';
            }
        }
        Callout::display('primary', ['heading'=>'Log', 'content' => ['list' => $list]]);
    }

}