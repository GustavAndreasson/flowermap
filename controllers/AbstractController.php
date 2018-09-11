<?php
abstract class AbstractController {
    protected $request;
    protected $fm;

    public function __construct($request) {
        $this->request = $request;
    }

    public function execute($fm) {
        $this->fm = $fm;
        $action = isset($this->request[0]) ? $this->request[0] : "index";
        $action_name = $action . "_action";
        if (method_exists($this, $action_name)) {
            $this->$action_name();
            return true;
        } else {
            return false;
        }
    }
}
