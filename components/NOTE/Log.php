<?php

namespace NOTE;

class Log {

    private $log;
    private $classes = [
        'notice' => 'info',
        'warning' => 'warning',
        'error' => 'danger',
        'debug' => 'dark'
    ];

    public function append($message, $class='notice')
    {
        $class = (in_array($class, array_keys($this->classes))) ? $class : 'notice';
        $this->log[] = [
            'class' => $class,
            'message' => $message
        ];
    }

    public function render()
    {
        $list = [];
        foreach($this->log as $class=>$message) {
            $list[] = '<li class="text-'.$this->classes[$message].'">'.$message.'</li>';
        }
        Callout::display('primary', ['heading'=>'Log', 'content' => ['list' => $list]]);
    }

}