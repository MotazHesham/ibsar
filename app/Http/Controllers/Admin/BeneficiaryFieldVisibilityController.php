<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BeneficiaryFieldVisibilitySetting;
use App\Services\BeneficiaryFieldVisibilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class BeneficiaryFieldVisibilityController extends Controller
{
    public function index()
    { 

        $fieldGroups = BeneficiaryFieldVisibilityService::getFieldGroupsWithFields();
        
        return view('admin.beneficiary-field-visibility.index', compact('fieldGroups'));
    }

    public function edit($id)
    { 

        $field = BeneficiaryFieldVisibilitySetting::findOrFail($id);
        
        return view('admin.beneficiary-field-visibility.edit', compact('field'));
    }

    public function update(Request $request, $id)
    { 

        $field = BeneficiaryFieldVisibilitySetting::findOrFail($id);
        
        $request->validate([
            'is_visible' => 'required|boolean',
            'is_required' => 'required|boolean',
            'field_label' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'required|integer|min:0',
        ]);

        $field->update([
            'is_visible' => $request->is_visible,
            'is_required' => $request->is_required,
            'field_label' => $request->field_label,
            'description' => $request->description,
            'sort_order' => $request->sort_order,
        ]);

        return redirect()->route('admin.beneficiary-field-visibility.index')
            ->with('success', 'Field visibility settings updated successfully.');
    }

    public function bulkUpdate(Request $request)
    { 

        $request->validate([
            'fields' => 'required|array',
            'fields.*.id' => 'required|exists:beneficiary_field_visibility_settings,id',
            'fields.*.is_visible' => 'required|boolean',
            'fields.*.is_required' => 'required|boolean',
        ]);

        foreach ($request->fields as $fieldData) {
            BeneficiaryFieldVisibilitySetting::where('id', $fieldData['id'])
                ->update([
                    'is_visible' => $fieldData['is_visible'],
                    'is_required' => $fieldData['is_required'],
                ]);
        }

        return response()->json(['success' => true, 'message' => 'Field visibility settings updated successfully.']);
    }

    public function toggleVisibility($id)
    { 

        $field = BeneficiaryFieldVisibilitySetting::findOrFail($id);
        $field->update(['is_visible' => !$field->is_visible]);

        return response()->json([
            'success' => true,
            'is_visible' => $field->is_visible,
            'message' => 'Field visibility toggled successfully.'
        ]);
    }

    public function toggleRequired($id)
    { 

        $field = BeneficiaryFieldVisibilitySetting::findOrFail($id);
        $field->update(['is_required' => !$field->is_required]);

        return response()->json([
            'success' => true,
            'is_required' => $field->is_required,
            'message' => 'Field requirement toggled successfully.'
        ]);
    }
}