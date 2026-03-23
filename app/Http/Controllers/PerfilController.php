<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Inertia\Inertia;
use Inertia\Response;

class PerfilController extends Controller
{

    public function index(Request $request): Response
    {
        return Inertia::render('Perfil/Index', [
            'status' => session('status'),
        ]);
    }
}
