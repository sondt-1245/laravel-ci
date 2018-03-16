<?php

namespace App\Http\Controllers;

use App\Repo\StreetRepositoryInterface;
use App\Street;
use Illuminate\Http\Request;

class StreetController extends Controller
{
    /**
     * @var \App\Repo\StreetRepositoryInterface
     */
    protected $repo;

    /**
     * StreetController constructor.
     *
     * @param \App\Repo\StreetRepositoryInterface $repo
     */
    public function __construct(StreetRepositoryInterface $repo)
    {
        $this->repo = $repo;
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $streets = $this->repo->paginateList($request->query('page', 1));

        return view('streets.list', [
            'streets' => $streets,
        ]);
    }
}
