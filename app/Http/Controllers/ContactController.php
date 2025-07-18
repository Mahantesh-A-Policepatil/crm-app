<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

/**
 *
 */
class ContactController extends Controller
{

    /**
     * @param Request $request
     * @return Application|Factory|View|JsonResponse
     * @throws Exception
     */

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $contacts = Contact::all();

            return DataTables::of($contacts)
                ->addColumn('checkbox', function ($contact) {
                    return $contact->is_merged
                        ? ''
                        : '<input type="checkbox" class="merge-checkbox" value="' . $contact->id . '">';
                })
                ->addColumn('name', function ($contact) {
                    $icon = $contact->is_merged
                        ? '<i class="bi bi-person-dash-fill text-danger me-1" title="Merged"></i>'
                        : '<i class="bi bi-person-check-fill text-success me-1" title="Active"></i>';

                    return '<span class="contact-name" id="contact-name-' . $contact->id . '">' . $icon . e($contact->name) . '</span>';
                })
                ->addColumn('status', function ($contact) {
                    return $contact->is_merged
                        ? '<span class="badge bg-danger">Merged</span>'
                        : '<span class="badge bg-success">Active</span>';
                })
                ->addColumn('action', function ($contact) {
                    $edit = '<a href="' . route('contacts.edit', $contact->id) . '" class="btn btn-sm btn-primary me-1">Edit</a>';
                    $delete = '<button onclick="confirmDelete(' . $contact->id . ')" class="btn btn-sm btn-danger">Delete</button>';
                    return $edit . $delete;
                })
                ->rawColumns(['checkbox', 'name', 'status', 'action'])
                ->make(true);
        }

        return view('contacts.index');
    }

    /**
     * @param $id
     * @return Application|Factory|View
     */
    public function show($id)
    {
        $contact = Contact::findOrFail($id);

        return view('contacts.show', compact('contact'));
    }

    /**
     * @return Application|Factory|View
     */
    public function create(): Factory|View|Application
    {
        return view('contacts.create');
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
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
     * @return Application|Factory|View
     */
    public function edit(Contact $contact): Factory|View|Application
    {
        return view('contacts.edit', compact('contact'));
    }

    /**
     * @param Request $request
     * @param Contact $contact
     * @return JsonResponse
     */
    public function update(Request $request, Contact $contact): JsonResponse
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
     * @return JsonResponse
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return response()->json(['success' => true]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function merge(Request $request): JsonResponse
    {
        $request->validate([
            'master_id' => 'required|exists:contacts,id',
            'secondary_id' => 'required|different:master_id|exists:contacts,id',
        ]);

        $master = Contact::findOrFail($request->master_id);
        $secondary = Contact::findOrFail($request->secondary_id);

        // Basic field merging logic (example)
        if (!$master->email && $secondary->email) $master->email = $secondary->email;
        if (!$master->phone && $secondary->phone) $master->phone = $secondary->phone;
        $master->save();

        // Merge custom fields (only if not set in master)
        foreach ($secondary->customFieldValues as $value) {
            $exists = $master->customFieldValues()
                ->where('custom_field_id', $value->custom_field_id)
                ->exists();

            if (!$exists) {
                $master->customFieldValues()->create([
                    'custom_field_id' => $value->custom_field_id,
                    'value' => $value->value,
                ]);
            }
        }

        $secondary->update([
            'is_merged' => true,
            'merged_into' => $master->id,
        ]);

        return response()->json(['success' => true]);
    }

}
