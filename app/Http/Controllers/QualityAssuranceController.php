<?php

namespace App\Http\Controllers;

use App\Form;
use Illuminate\Http\Request;
use App\File;
use Illuminate\Support\Facades\Auth;

class QualityAssuranceController extends Controller
{
    public function quality_assurance()
    {
        $review_file = File::where('file_status', '=', '4')
        ->orWhere('file_status', '=', '5')
        ->orWhere('file_status', '=', '7')
        ->orderBy('id', 'DESC')
        ->get();
        return view('qms.quality_assurance', compact('review_file'));
    }

    public function qa_approved(Request $request)
    {
        $s_file = File::find($request->file_id);
        $s_file->file_status = '5';
        $s_file->reviewer_id = '1';
        $s_file->reviewed_date = date('Y-m-d H:i:s');
        $s_file->save();

        $form = Form::find($request->form_id);
        $form->form_status = '5';
        $form->crf_number_id = $request->crfNumber;
        $form->reviewer_id = '1';
        $form->save();

        return redirect('quality_assurance')->with('Success', 'File is for review');
    }

    public function qa_revision(Request $request)
    {
        /* The form will be mark as for revision so the form_status will become 1
         *
         */

        $form = Form::find($request->form_id);
        $form->form_status = '1';
        $form->file_version = $form->file_version++;
        $form->reviewer_id = Auth::user()->id;
        $form->save();

        /* This file will be mark as rejected. so the file status will become 7
         *
         * */
        $crf_declined = File::find($request->file_id);
        $crf_declined->file_status = '7';
        $crf_declined->reviewer_id = Auth::user()->id;
        $crf_declined->reviewed_date = date('Y-m-d H:i:s');
        $crf_declined->save();

        return redirect('quality_assurance')->with('Success', 'File is for revision');
    }
}