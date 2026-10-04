<?php

namespace App\Http\Controllers;
use App\Services\DolarService;

class HomeController extends Controller
{
   public function index()
{
    $dolar = DolarService::cotizacionOficial();
    return view('home', ['dolar' => $dolar]);
}

    public function perfil()
{
    return view('perfil');
}
}