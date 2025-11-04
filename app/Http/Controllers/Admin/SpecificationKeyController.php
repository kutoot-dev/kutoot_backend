<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\ProductSpecificationKey;
use Illuminate\Http\Request;

class SpecificationKeyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin-api');
    }

    public function index()
    {
        $SpecificationKeys = ProductSpecificationKey::with('productSpecifications')->get();
        return response()->json(['SpecificationKeys' => $SpecificationKeys], 200);
    }

    public function create()
    {
        return view('admin.create_specification_key');
    }

    public function store(Request $request)
    {
        $rules = [
            'key' => 'required|unique:product_specification_keys',
            'status' => 'required'
        ];
        $customMessages = [
            'key.required' => trans('Key is required'),
            'key.unique' => trans('Key already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $SpecificationKey = new ProductSpecificationKey();
        $SpecificationKey->key = $request->key;
        $SpecificationKey->status = $request->status;
        $SpecificationKey->save();

        $notification = trans('Created Successfully');
        return response()->json(['message' => $notification],200);
    }

    public function show($id)
    {
        $SpecificationKey = ProductSpecificationKey::find($id);
        return response()->json(['SpecificationKey' => $SpecificationKey], 200);
    }


    public function edit($id)
    {
        $SpecificationKey = ProductSpecificationKey::find($id);
        return view('admin.edit_specification_key',compact('SpecificationKey'));
    }


    public function update(Request $request,$id)
    {
        $SpecificationKey = ProductSpecificationKey::find($id);
        $rules = [
            'key' => 'required|unique:product_specification_keys,key,'.$SpecificationKey->id,
            'status' => 'required'
        ];
        $customMessages = [
            'key.required' => trans('Key is required'),
            'key.unique' => trans('Key already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $SpecificationKey->key = $request->key;
        $SpecificationKey->status = $request->status;
        $SpecificationKey->save();

        $notification = trans('Update Successfully');
        return response()->json(['message' => $notification],200);
    }

    public function destroy($id)
    {
        $SpecificationKey = ProductSpecificationKey::find($id);
        $SpecificationKey->delete();

        $notification=trans('Delete Successfully');
        return response()->json(['message' => $notification],200);
    }

    public function changeStatus($id){
        $SpecificationKey = ProductSpecificationKey::find($id);
        if($SpecificationKey->status == 1){
            $SpecificationKey->status = 0;
            $SpecificationKey->save();
            $message = trans('Inactive Successfully');
        }else{
            $SpecificationKey->status = 1;
            $SpecificationKey->save();
            $message = trans('Active Successfully');
        }
        return response()->json($message);
    }
}
