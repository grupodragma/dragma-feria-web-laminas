<?php

namespace Application\Helper;

class Tools
{
    private $serviceManager;

    public function __construct($serviceManager)
    {
        $this->serviceManager = $serviceManager;
    }

    public function toAscii($str, $delimiter = '-')
    {
        $str = trim($str);
        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]\?/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
        $clean = str_replace(["&","'","*",'"','#','?','(',')',',','!','`'], "", $clean);
        return $clean;
    }
}
