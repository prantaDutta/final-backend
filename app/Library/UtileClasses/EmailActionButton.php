<?php


namespace App\Library\UtileClasses;


class EmailActionButton
{
    // Properties
    public $url;
    public $name;

    // creating the class
    public function __construct($name, $url) {
        $this->name = $name;
        $this->url = $url;
    }

    // Methods
    public function set_name($name)
    {
        $this->name = $name;
    }

    public function get_name()
    {
        return $this->name;
    }

}
