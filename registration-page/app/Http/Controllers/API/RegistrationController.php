<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Nette\Utils\Image;
use Symfony\Component\HttpFoundation\File\File;

class RegistrationController extends Controller
{
    private $newPhotoPath;
    private $errors = [];
    public function index(Request $request) {
        $response = [];
        $this->validateFields($request);


        if(sizeof($this->errors)) {
            $response['errors'] = $this->errors;
        }
//        else {
//
//        }

        return response()->json($response);
    }

    protected function validateFields(Request $request) {
        $this->isValidName($request->name);
        $this->isValidEmail($request->email);
        $this->isValidPhone($request->phone);
        $this->isValidPositionId($request->position_id);
        $this->validateAndSavePhoto($request->photo);
    }

    protected function isValidName($name)
    {
        if(empty($name)) {
            $this->errors[] = 'The name field is required.';
        } else if(mb_strlen($name) < 2) {
            $this->errors[] = 'The name must be at least 2 characters.';
        } else if(mb_strlen($name) > 60) {
            $this->errors[] = 'The name must be not longer than 60 characters.';
        }
    }

    protected function isValidEmail($email)
    {
        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        if(empty($email)) {
            $this->errors[] = 'The email field is required.';
        } else if(preg_match($pattern, $email) !== 1) {
            $this->errors[] = 'The email must be a valid email address.';
        }
    }

    protected function isValidPhone($phone)
    {
        $pattern = '/^\+{0,1}380([0-9]{8})$/';
        if(empty($phone)) {
            $this->errors[] = 'The phone field is required.';
        } else if(preg_match($pattern, $phone) !== 1) {
            $this->errors[] = 'The phone must be a valid.';
        }
    }

    protected function isValidPositionId($positionId)
    {
        if(empty($positionId)) {
            $this->errors[] = 'The position id field is required.';
        }if(!is_int($positionId)) {
            $this->errors[] = 'The position id must be an integer.';
        } else if(!Position::where('id', $positionId)->first()) {
            $this->errors[] = 'The position id not found.';
        }
    }

    protected function validateAndSavePhoto($photo) {
//      get extension
        $imageSizes = getimagesize($photo);
        if($imageSizes) {
            $mime = (explode('/', $imageSizes['mime']));
            $type = $mime[0];
            $extension = $mime[1];

            if ($type === 'image' && ($extension === 'jpg' || $extension === 'jpeg')) {
//          get photo size
                $imageDecodedBase64 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photo));
                $baseTempPath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);
                $tempPath = $baseTempPath . time() . "." . $extension;
                file_put_contents($tempPath, $imageDecodedBase64);
                $imageSize = filesize($tempPath);

                if ($imageSize <= 5 * 1024 * 1024) {
                    if ($imageSizes[0] >= 70 && $imageSizes[1] >= 70) {
//                  save photo
                        $imageName = 'images/' . time() . '.' . $extension;
                        Storage::disk('public')->put($imageName, $imageDecodedBase64);

                        $this->newPhotoPath = Storage::disk('public')->path($imageName);
                    } else {
                        $this->errors[] = 'Minimum size of photo 70x70px.';
                    }
                } else {
                    $this->errors[] = 'The photo size must not be greater than 5 Mb.';
                }
            } else {
                $this->errors[] = 'The photo format must be jpeg/jpg type.';
            }
        } else {
            $this->errors[] = 'The photo is required.';
        }
    }

}