<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

/**
 *
 */
class ContactController extends Controller
{

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $contacts = Contact::query();

            return DataTables::of($contacts)
                ->addColumn('action', function ($row) {
                    $editUrl = route('contacts.edit', $row->id);
                    $deleteUrl = route('contacts.destroy', $row->id);

                    return '
                    <a href="' . $editUrl . '" class="btn btn-sm btn-warning">Edit</a>
                    <button class="btn btn-sm btn-danger" onclick="confirmDelete(' . $row->id . ')">Delete</button>
                ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('contacts.index');
    }


    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('contacts.create');
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'gender' => 'nullable|in:male,female,other',
            'profile_image' => 'nullable|image',
            'additional_file' => 'nullable|file',
        ]);

        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        if ($request->hasFile('additional_file')) {
            $data['additional_file'] = $request->file('additional_file')->store('documents', 'public');
        }

        Contact::create($data);

        return response()->json(['success' => true]);
    }

    /**
     * @param Contact $contact
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Contact $contact)
    {
        return view('contacts.edit', compact('contact'));
    }

    /**
     * @param Request $request
     * @param Contact $contact
     * @return void
     */
    /**
     * @param Request $request
     * @param Contact $contact
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Contact $contact)
    {
        $data = $request->validate([
            'name' => 'required',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'gender' => 'nullable|in:male,female,other',
            'profile_image' => 'nullable|image',
            'additional_file' => 'nullable|file',
        ]);

        // Update profile image if uploaded
        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($contact->profile_image && \Storage::disk('public')->exists($contact->profile_image)) {
                \Storage::disk('public')->delete($contact->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')->store('profiles', 'public');
        }

        // Update additional file if uploaded
        if ($request->hasFile('additional_file')) {
            if ($contact->additional_file && \Storage::disk('public')->exists($contact->additional_file)) {
                \Storage::disk('public')->delete($contact->additional_file);
            }
            $data['additional_file'] = $request->file('additional_file')->store('documents', 'public');
        }

        $contact->update($data);

        return response()->json(['success' => true]);
    }


    /**
     * @param Contact $contact
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return response()->json(['success' => true]);
    }

    /**
     * @param Request $request
     * @return void
     */
    public function merge(Request $request)
    {
        // Handle merging logic here
    }
}
