<?php
namespace Controller;
use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        echo "HomeController index";
        $this->render('partials/home', []);
    }
}
