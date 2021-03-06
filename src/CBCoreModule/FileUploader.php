<?php

namespace crocodicstudio\crudbooster\CBCoreModule;

use crocodicstudio\crudbooster\Modules\ModuleGenerator\ControllerGenerator\FieldDetector;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Request;

class FileUploader
{
    public function uploadFile($name)
    {
        if (! Request::hasFile($name)) {
            return null;
        }
        $file = Request::file($name);
        $ext = $file->getClientOriginalExtension();
        $this->validateSize($file);
        $this->validateExtension($ext);
        //Create Directory Monthly
        Storage::makeDirectory(date('Y-m'));

        //Move file to storage
        $filename = md5(str_random(5)).'.'.$ext;
        $filePath = 'uploads'.DIRECTORY_SEPARATOR.date('Y-m');
        Storage::putFileAs($filePath, $file, $filename);

        return 'uploads/'.date('Y-m').'/'.$filename;
    }

    /**
     * @param $ext
     */
    private function validateExtension($ext)
    {
        if (! FieldDetector::isUploadField($ext)) {
            sendAndTerminate(response()->json("The filetype is not allowed!"));
        }
    }

    /**
     * @param $file
     */
    private function validateSize($file)
    {
        $fileSize = $file->getClientSize() / 1024;
        if ($fileSize > cbConfig('UPLOAD_MAX_SIZE', 5000)) {
            sendAndTerminate(response()->json("The file Size is too large!"));
        }
    }
}