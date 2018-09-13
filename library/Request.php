<?php
class Request {
    private $request_uri;

    public function __construct() {
        $this->request_uri = trim(str_replace("/flowermap", "", $_SERVER['REQUEST_URI']), '/');
    }

    public function get_part($nr) {
        $parts = explode('/', $this->request_uri);
        if (isset($parts[$nr])) {
            return $parts[$nr];
        } else {
            return false;
        }
    }
}
