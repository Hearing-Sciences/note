<?php

namespace NOTE\Question;

class Afc extends Question {

    public $choices = [];

    public function __construct($options)
    {
        parent::__construct($options);
        $this->choices = $options['choices'];
    }

    public function render()
    {
        $return[] = '<p class="lead">'.$this->questionText.'</p>';
        foreach($this->choices as $choice=>$text) {
            $return[] = '<div class="col-sm-3">';
            $return[] = '<input type="radio" name="'.$this->name.'" id="'.$this->name.'_'.$choice.'" value="'.$choice.'" />';
            $return[] = '<label for="'.$this->name.'_'.$choice.'">'.$text.'</label>';
            $return[] = '</div>';
        }

        return implode("\n", $return);
    }

}