<?php

namespace App\Http\Controllers;

use App\Http\Resources\SaleResource;
use App\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return  SaleResource::collection(Sale::all());
    }
}
