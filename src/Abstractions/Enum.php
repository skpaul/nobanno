<?php
    // namespace Nobanno\Abstractions;

    //This is a base class for all enum.
    abstract class Enum {
        protected $val;

        protected function __construct($arg) {
            $this->val = $arg;
        }

        public function __toString() {
            return $this->val;
        }

        public function __set($arg1, $arg2) {
            throw new \Exception("enum does not have property");
        }

        public function __get($arg1) {
            throw new \Exception("enum does not have property");
        }

        // not really needed
        public function __call($arg1, $arg2) {
            throw new \Exception("enum does not have method");
        }

        // not really needed
        static public function __callStatic($arg1, $arg2) {
            throw new \Exception("enum does not have static method");
        }
    }
?>