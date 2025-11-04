<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index()
    {
        $faqs=Faq::all();
        return response()->json(['faqs' => $faqs], 200);
    }

    public function store(Request $request)
    {
        $rules = [
            'question'=>'required|unique:faqs',
            'answer'=>'required',
            'status'=>'required',
        ];
        $customMessages = [
            'question.required' => trans('Question is required'),
            'question.unique' => trans('Question already exist'),
            'answer.required' => trans('Answer is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $faq = new Faq();
        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->status = $request->status;
        $faq->save();

        $notification= trans('Created Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function show($id)
    {
        $faq = Faq::find($id);
        return response()->json(['faq' => $faq], 200);
    }

    public function update(Request $request,$id)
    {
        $faq = Faq::find($id);
        $rules = [
            'question'=>'required|unique:faqs,question,'.$faq->id,
            'answer'=>'required',
            'status'=>'required',
        ];
        $customMessages = [
            'question.required' => trans('Question is required'),
            'question.unique' => trans('Question already exist'),
            'answer.required' => trans('Answer is required'),
        ];
        $this->validate($request, $rules,$customMessages);

        $faq->question = $request->question;
        $faq->answer = $request->answer;
        $faq->status = $request->status;
        $faq->save();

        $notification= trans('Update Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function destroy($id)
    {
        $faq = Faq::find($id);
        $faq->delete();

        $notification= trans('Delete Successfully');
        return response()->json(['message' => $notification], 200);
    }

    public function changeStatus($id){
        $faq = Faq::find($id);
        if($faq->status==1){
            $faq->status=0;
            $faq->save();
            $message= trans('Inactive Successfully');
        }else{
            $faq->status=1;
            $faq->save();
            $message= trans('Active Successfully');
        }
        return response()->json($message);
    }
}
