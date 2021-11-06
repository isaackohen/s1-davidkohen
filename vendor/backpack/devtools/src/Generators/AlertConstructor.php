<?php

namespace Backpack\DevTools\Generators;

trait AlertConstructor
{
    public function alertTitle($title)
    {
        return "<strong>$title</strong><br />";
    }

    public function alertLine($title, $message = '')
    {
        if (! $message) {
            $message = $title;
            $title = '';
        }

        return "<li><strong>$title</strong> $message</li>";
    }
}
