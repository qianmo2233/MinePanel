<?php

namespace app\response;

class PanelInfo
{
    public $version;
    public $build;
    public $api;

    public function build($ver, $build, $api): PanelInfo
    {
        $this->version = $ver;
        $this->build = $build;
        $this->api = $api;
        return $this;
    }
}