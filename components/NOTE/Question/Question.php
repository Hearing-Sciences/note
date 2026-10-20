<?php

namespace NOTE\Question;

class Question {

    public $name;

    public $questionText;

    public function __construct($options)
    {
        $this->name = $options['name'];
        $this->questionText = $options['questionText'];
    }

}