<?php
class Request {
    private $root;
    private $request_parts;
    private $query;
    private $data;
    private $files;
    public $method;

    public function __construct() {
        $request = str_replace("/flowermap", "", $_SERVER['REQUEST_URI']);
        if ($request !== $_SERVER['REQUEST_URI']) {
            $this->root = "/flowermap";
        } else {
            $this->root = "";
        }
        $request_split = explode('?', $request);
        $request = $request_split[0];
        if (isset($request_split[1])) {
            $this->query = $request_split[1];
        }
        $request = trim($request, '/');
        $this->request_parts = explode('/', $request);
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
        if ($this->method == "POST") {
            foreach ($_POST as $id => $value) {
                $this->data[$id] = $value;
            }
            foreach ($_FILES as $name => $value) {
                $this->files[$name] = $value;
            }
        } elseif ($this->method == "GET") {
            foreach ($_GET as $id => $value) {
                $this->data[$id] = $value;
            }
        }
    }

    public function get_root() {
        return $this->root;
    }
}
