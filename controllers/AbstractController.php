<?php
abstract class AbstractController {
    protected $request;
    protected $fm;

    public function __construct($request) {
        $this->request = $request;
    }

    public function execute($fm) {
        $this->fm = $fm;
        $action = $this->request->getUriPart(1) ? $this->request->getUriPart(1) : "index";
        $actionName = $action . "Action";
        if (method_exists($this, $actionName)) {
            $this->$actionName();
        } else {
            throw new Exception("$actionName does not exist in " . get_class($this), 404);
        }
    }
}
