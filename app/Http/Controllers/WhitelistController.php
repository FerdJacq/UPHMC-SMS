<?php

namespace App\Http\Controllers;

use App\Models\Whitelist;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WhitelistController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('whitelist/index');
    }

    public function list(Request $request)
    {
        $data = Whitelist::when($request->sort_by, function ($query, $value) {
                $query->orderBy($value, request('order_by', 'asc'));
            })
            ->when(!isset($request->sort_by), function ($query) {
                $query->latest();
            })
            ->when($request->search, function ($query, $value) {
                $query->where('name', 'LIKE', '%'.$value.'%')
                ->orWhere('ip_address', 'LIKE', '%'.$value.'%');
            })
            ->paginate($request->page_size ?? 10);
        
        return response()->json(["status"=>"success","data"=>$data], 200);
    }

    public function get($id)
    {
        $data = Whitelist::where("id",$id)->first();
        return response()->json(["status"=>"success","data"=>$data], 200);
    }

    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|string',
            'ip_address' => 'required|string|unique:whitelists',
            'status' => 'required|string',
        ]);
        Whitelist::create($data);
        return response()->json(['success' => 1,"message"=>"Success!"]);
    }

    public function update(Whitelist $Whitelist, Request $request)
    {
        $data = $this->validate($request, [
            'name' => 'required|string',
            'ip_address' => 'required|string|unique:whitelists,id,'.$request->id,
            'status' => 'required|string',
        ]);
        $Whitelist->find($request->id)->update($data);
        return response()->json(['success' => 1,"message"=>"Success!"]);
    }

    public function destroy(Request $request)
    {
        $item = Whitelist::where("id",$request->id)->delete();
        return response()->json(['success' => 1,"message"=>"Success!"]);
    }
}
