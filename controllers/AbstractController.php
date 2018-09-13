<?php
abstract class AbstractController {
    protected $request;
    protected $fm;

    public function __construct($request) {
        $this->request = $request;
    }

    public function execute($fm) {
        $this->fm = $fm;
        $action = $this->request->get_part(1) ? $this->request->get_part(1) : "index";
        $action_name = $action . "_action";
        if (method_exists($this, $action_name)) {
            $this->$action_name();
        } else {
            throw new Exception("$action_name does not exist in " . get_class($this), 404);
        }
    }
}
