<?php
    namespace Nobanno;
class JSON
{
    private $data;
    public function __construct()
    {
        $this->data = new \stdClass();
    }

    public function __call($name, $arguments)
    {
        $this->data->$name = $arguments[0];
        return $this;
    }

    public function success()
    {
        $this->data->issuccess = true;
        return $this;
    }

    public function fail()
    {
        $this->data->issuccess = false;
        return $this;
    }

    /**
     * create()
     * 
     * It modifies header as 'Content-type: application/json; charset=utf-8'.
     * 
     * @param bool $forceObject
     * 
     * @return string json encoded string.
     */
    public function create($forceObject = true)
    {
        header("Content-type: application/json; charset=utf-8");
        if ($forceObject)
            $string = json_encode($this->data, JSON_FORCE_OBJECT);
        else
            $string = json_encode($this->data);

        // Optionally handle json_encode failure
        if ($string === false) {
            $string = '{"issuccess":false,"error":"JSON encoding failed"}';
        }

        $this->data = new \stdClass(); //reset 
        return $string;
    }
}
