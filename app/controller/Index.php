<?php
namespace app\controller;

use app\BaseController;
use app\response\PanelInfo;
use think\Response;

class Index extends BaseController
{
    public function index(): Response
    {
        $response = new PanelInfo();
        return json($response->build('0.0.1', 'e7fe3h73', '0.0.1'));
    }
}
