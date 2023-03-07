<?php

namespace Devvime\KiichiPhpEngine;

use Rain\Tpl;

class ViewService {

    private $tpl;
    private $options;
    private $defaults = [
        "header"=>true,
        "footer"=>true,
        "headerData"=>[]
    ];

    public function __construct($opts = [])
    {
        $tpl_dir = VIEWS_DIR;
        $this->options = array_merge($this->defaults, $opts);
        $config = array(
            "tpl_dir"       => $tpl_dir,
            "cache_dir"     => VIEWS_CACHE_DIR,
            "debug"         => false
        );
        Tpl::configure($config);
        $this->tpl = new Tpl;
        $this->setData($this->options["headerData"]);
        if ($this->options["header"] === true) $this->render("header", $this->defaults['headerData']);
    }

    private function setData($data = array())
    {
        foreach ($data as $key => $value) {
            $this->tpl->assign($key,$value);
        }
    }

    public function render($name, $data = array(), $retunrHTML = false)
    {
        $this->setData($data);
        return $this->tpl->draw($name, $retunrHTML);
    }

    public function __destruct()
    {
       if ($this->options["footer"] === true) $this->tpl->draw("footer");
    }

}