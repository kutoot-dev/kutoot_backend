<?php

namespace App\Http\Controllers\WEB\Admin;
use App\Http\Controllers\Controller;
use App\Models\ProductSpecificationKey;
use Illuminate\Http\Request;

class SpecificationKeyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:admin');
    }

    public function index()
    {
        $SpecificationKeys = ProductSpecificationKey::with('productSpecifications')->get();
        return view('admin.specification_key',compact('SpecificationKeys'));
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
            'key.required' => trans('admin_validation.Key is required'),
            'key.unique' => trans('admin_validation.Key already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $SpecificationKey = new ProductSpecificationKey();
        $SpecificationKey->key = $request->key;
        $SpecificationKey->status = $request->status;
        $SpecificationKey->save();

        $notification = trans('admin_validation.Created Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->back()->with($notification);
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
            'key.required' => trans('admin_validation.Key is required'),
            'key.unique' => trans('admin_validation.Key already exist'),
        ];
        $this->validate($request, $rules,$customMessages);

        $SpecificationKey->key = $request->key;
        $SpecificationKey->status = $request->status;
        $SpecificationKey->save();

        $notification = trans('admin_validation.Update Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.specification-key.index')->with($notification);
    }

    public function destroy($id)
    {
        $SpecificationKey = ProductSpecificationKey::find($id);
        $SpecificationKey->delete();

        $notification=trans('admin_validation.Delete Successfully');
        $notification = array('messege'=>$notification,'alert-type'=>'success');
        return redirect()->route('admin.specification-key.index')->with($notification);
    }

    public function changeStatus($id){
        $SpecificationKey = ProductSpecificationKey::find($id);
        if($SpecificationKey->status == 1){
            $SpecificationKey->status = 0;
            $SpecificationKey->save();
            $message = trans('admin_validation.Inactive Successfully');
        }else{
            $SpecificationKey->status = 1;
            $SpecificationKey->save();
            $message = trans('admin_validation.Active Successfully');
        }
        return response()->json($message);
    }
}
