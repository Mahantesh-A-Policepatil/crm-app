<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use Illuminate\Http\Request;

/**
 *
 */
class CustomFieldController extends Controller
{

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $fields = CustomField::all();
        return view('custom_fields.index', compact('fields'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
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
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(CustomField $customField)
    {
        $customField->delete();
        return redirect()->back()->with('success', 'Custom field deleted!');
    }
}
