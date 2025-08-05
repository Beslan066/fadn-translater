<?php

namespace App\Http\Controllers;

use App\Http\Requests\Region\StoreRequest;
use App\Models\Region;
use App\Models\User;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::paginate(10);

        return view('pages.region.index', [
            'regions' => $regions
        ]);
    }

    public function create() {

        $regionAdmins = User::where('role', 'region_admin')->get();

        return view('pages.region.create', [
            'regionAdmins' => $regionAdmins
        ]);
    }

    public  function store(StoreRequest $request) {

        $data = $request->validated();

        $data['is_active'] = $request->has('is_active') ? 1 : 0;


        $region = Region::firstOrCreate($data);

        $region->save();

        return redirect()->route('regions.index');
    }
}
