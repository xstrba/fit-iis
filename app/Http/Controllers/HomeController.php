<?php

namespace App\Http\Controllers;

use App\Parents\FrontEndController;

/**
 * Class HomeController
 *
 * @package App\Http\Controllers
 */
final class HomeController extends FrontEndController
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index(): \Illuminate\Contracts\Support\Renderable
    {
        $this->setTitle('pages.dashboard');
        return $this->view('app.home');
    }

    /**
     * @inheritDoc
     */
    protected function initMiddleware(): void
    {
        $this->middleware('auth');
    }
}
