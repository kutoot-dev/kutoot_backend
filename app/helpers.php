<?php

if (!function_exists('handleImageUpload')) {
    function handleImageUpload($request, $key, $folder = 'uploads')
    {
        if ($request->hasFile($key)) {
            $file = $request->file($key); // UploadedFile instance
            $filename = time().'_'.$file->getClientOriginalName();
            $path = 'uploads/'.$folder;
            $file->move(public_path($path), $filename);

            return $path . '/' . $filename;
        }

        return null; // no file uploaded
    }
}


