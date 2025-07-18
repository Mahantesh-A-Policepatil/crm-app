<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 *
 */
class CustomFieldController extends Controller
{

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        $fields = CustomField::all();
        return view('custom_fields.index', compact('fields'));
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
        ]);

        CustomField::create($request->only('name', 'type'));

        return redirect()->back()->with('success', 'Custom field added!');
    }

    /**
     * @param CustomField $customField
     * @return RedirectResponse
     */
    public function destroy(CustomField $customField)
    {
        $customField->delete();
        return redirect()->back()->with('success', 'Custom field deleted!');
    }
}
