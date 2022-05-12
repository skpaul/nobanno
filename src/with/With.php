<?php 

// $poppy = (new with(new stdClass()))->go("now")->endWith();
// echo $poppy->go;

class With {
    public function __construct($stdClass){
        $this->stdClass = $stdClass;
    }

    public function __call($function, $args) {
        $args = implode(', ', $args);
        $property = "$function";
        $this->stdClass->$property = $args;
        return $this;
    }

    public function endWith(){
        return $this->stdClass;
    }
}



?>