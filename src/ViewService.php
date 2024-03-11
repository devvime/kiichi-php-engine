<?php

namespace Devvime\Kiichi\Engine;

use Rain\Tpl;

class ViewService {

    private $tpl;
    private $options;
    private $defaults = [
        "header"=>true,
        "footer"=>true,
        "headerData"=>[
            "author"=>"Name here",
            "description"=>"Description here"
        ]
    ];

    public function __construct($opts = [])
    {
        $this->options = array_merge($this->defaults, $opts);
        $this->tpl = new Tpl();
        $config = array(
            "tpl_dir"       => VIEWS_DIR,
            "cache_dir"     => VIEWS_CACHE_DIR,
            "debug"         => false
        );
        Tpl::configure($config);
        $this->tpl = new Tpl;
        if ($this->options["header"]) $this->render("header", $this->options['headerData']);
    }

    private function setData($data = array())
    {
        foreach ($data as $key => $value) {
            $this->tpl->assign($key,$value);
        }
    }

    public function render($name, $data = array(), $returnHTML = false)
    {
        $this->setData($data);
        return $this->tpl->draw($name, $returnHTML);
    }

    public function __destruct()
    {
       if ($this->defaults["footer"] === true) $this->tpl->draw("footer");
    }

}