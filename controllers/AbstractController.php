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
	$actionName = preg_replace_callback('/-([a-z])/', function($m) { return strtoupper($m[1]); }, $action);
        $actionMethod = $actionName . "Action";
        if (method_exists($this, $actionMethod)) {
            $this->$actionMethod();
        } else {
            throw new Exception("$actionMethod does not exist in " . get_class($this), 404);
        }
    }
}
