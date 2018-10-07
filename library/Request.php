<?php
class Request {
    private $root;
    private $requestParts;
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
        $requestSplit = explode('?', $request);
        $request = $requestSplit[0];
        if (isset($requestSplit[1])) {
            $this->query = $requestSplit[1];
        }
        $request = trim($request, '/');
        $this->requestParts = explode('/', $request);
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->setData();
    }

    public function getUriPart($nr) {
        if (isset($this->requestParts[$nr])) {
            return $this->requestParts[$nr];
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

    public function getFile($name) {
        if (isset($this->data[$name]["tmpName"])) {
            return $this->data[$name];
        } else {
            return null;
        }
    }

    private function setData() {
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

    public function getRoot() {
        return $this->root;
    }
}
