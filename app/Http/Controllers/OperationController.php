<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OperationController extends Controller
{
    public function pasangSurut()
    {
        return view('operation.pasangSurut', [
            'breadcrumbs' => [
                'operation/dashboard' => 'Operation',
                '#' => 'Hourly Monitoring Barging',
                'pasangSurut' => 'Prediksi Pasang Surut'
            ]
        ]);
    }

    public function productivityJetty()
    {
        return view('operation.pasangSurut', [
            'breadcrumbs' => [
                'operation/dashboard' => 'Operation',
                '#' => 'Hourly Monitoring Barging',
                'pasangSurut' => 'Prediksi Pasang Surut'
            ]
        ]);
    }

    public function index()
    {
        return view('operation.dashboard', [
            'breadcrumbs' => [
                'operation/dashboard' => 'Operation',
                'dashboard' => 'Dashboard'
            ]
        ]);
    }

    public function dashboard()
    {
        return view('operation.game', [
            'breadcrumbs' => [
                'operation/dashboard' => 'Operation',
                'dashboard' => 'Game'
            ]
        ]);
    }

    public function test()
    {
        return view('operation.test', [
            'breadcrumbs' => [
                'Test' => 'Test',
            ]
        ]);
    }
}
