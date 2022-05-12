<?php
    interface SwiftSessionInterface {
        public function start(string $owner);
        public function continue(int $sessionId);
        public function getSessionId();
        public function setData(string $key, mixed $value);
        public function getData(string $key);
        public function removeData(string $key); 
        public function close();
    }
?>