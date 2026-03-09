<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Models\Form;
use Illuminate\Http\Request;

class OwnerFormController extends Controller
{
    public function setting()
    {
        $pageTitle = 'Vendor Document Verification Form';
        $form = Form::where('act', 'owner_form')->first();
        return view('admin.owner_form.setting', compact('pageTitle', 'form'));
    }

    public function settingUpdate(Request $request)
    {
        $formProcessor = new FormProcessor();
        $generatorValidation = $formProcessor->generatorValidation();
        $request->validate($generatorValidation['rules'], $generatorValidation['messages']);
        $exist = Form::where('act', 'owner_form')->first();
        if ($exist) {
            $isUpdate = true;
        } else {
            $isUpdate = false;
        }
        $formProcessor->generate('owner_form', $isUpdate, 'act');

        $notify[] = ['success', 'Owner form data updated successfully'];
        return back()->withNotify($notify);
    }
}
