<?php
class Request {
    private $root;
    private $request_parts;
    private $data;
    private $files;
    public $method;

    public function __construct() {
        $remove_fm = str_replace("/flowermap", "", $_SERVER['REQUEST_URI']);
        if ($remove_fm !== $_SERVER['REQUEST_URI']) {
            $this->root = "/flowermap";
        } else {
            $this->root = "";
        }
        $request_uri = trim($remove_fm, '/');
        $this->request_parts = explode('/', $request_uri);
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->set_data();
    }

    public function get_uri_part($nr) {
        if (isset($this->request_parts[$nr])) {
            return $this->request_parts[$nr];
        } else {
            return false;
        }
    }

    public function get($var) {
        if (isset($this->data[$var])) {
            return $this->data[$var];
        } else {
            return null;
        }
    }

    public function get_file($name) {
        if (isset($this->data[$name]["tmp_name"])) {
            return $this->data[$name];
        } else {
            return null;
        }
    }

    private function set_data() {
        if ($this->method = "POST") {
            foreach ($_POST as $id => $value) {
                $this->data[$id] = $value;
            }
            foreach ($_FILES as $name => $value) {
                $this->files[$name] = $value;
            }
        }
    }

    public function get_root() {
        return $this->root;
    }
}
