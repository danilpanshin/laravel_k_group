<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Item;

class SearchController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('search');
    }

    public function startImportView()
    {
        return view('imports');
    }

    public function startImport()
    {
        Item::startImport();
        return redirect()->route('search');
    }

    /**
     *
     * @return \Illuminate\Http\Response
     */
    public function autocomplete(Request $request)
    {
        $data = Item::select("name")
            ->where("name", "LIKE", "%{$request->input('query')}%")
            ->get();

        return response()->json($data);
    }

    public function showFromSearch(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);
        $data = Item::getData($request->input('name'));
        if ($data !== null) {
            return view('item', [
                'name' => $data->name,
                'price' => $data->price,
                'description' => $data->description,
                'url' => $data->url,
                'picture' => $data->picture
            ]);
        } else {
            return 'Товар не найден';
        }
    }

    public function showById($id)
    {
        $data = Item::getDataById($id);
        return view('item', [
            'name' => $data->name,
            'price' => $data->price,
            'description' => $data->description,
            'url' => $data->url,
            'picture' => $data->picture
        ]);
    }
}
