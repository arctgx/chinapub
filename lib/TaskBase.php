<?php

class TaskBase {

    private $_params = array();

    public function __construct($params=array()) {
        if (is_array($params) && !empty($params)) {
            foreach ($params as $key => $value) {
                $this->_params[$key] = (string)$value;
            }
        }
    }

    protected function getParam($key, $defaultValue='') {
        return isset($this->_params[$key]) ? $this->_params[$key] : $defaultValue;
    }

    public function beforTask() {
        return true;
    }

    public function afterTask() {
        return ;
    }
}

