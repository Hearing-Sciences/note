<?php

namespace NOTE;

class Callout {

    public static function display(
        $class = 'primary',
        $options,
        $deferred = false
    )
    {
        $output = [];
        $defaults = [
            'heading' => 'Heading is missing',
            'content' => [
                'No content found.'
            ]
        ];
        $config = array_merge($defaults, $options);

        $output[] = '<div class="container">';
        $output[] = '<div class="p-2 m-2 border border-3 border-'.$class.'">';
        $output[] = '<h3 class="text-'.$class.'">'.$config['heading'].'</h3>';

        if (!is_array($config['content'])) {
            $output[] = '<p>'.$config['content'].'</p>';
        } else {
            foreach($config['content'] as $tag => $text) {
                switch($tag) {
                    case 'list':
                        $output[] = '<ul>';
                        foreach($text as $line) {
                            $output[] = $line;
                        }
                        $output[] = '</ul>';
                    break;

                    default:
                        $output[] = '<p>'.$text.'</p>';
                    break;
                }
            }
        }
        
        $output[] = '</div>';
        $output[] = '</div>';

        foreach($output as $line) {
            print $line."\n";
        }

    }
    
}