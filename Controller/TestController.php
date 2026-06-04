<?php

namespace Controller;

use App\Core\Controller;
use App\Repository\TestModel;

class TestController extends Controller
{
    public function index(){
        $testModel = new TestModel();
        $tests = $testModel->getAllTest();

        $this->render('test/test', [
            'tests' => $tests,
        ]);
    }
}
