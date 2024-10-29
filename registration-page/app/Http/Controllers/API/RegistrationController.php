<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Position;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RegistrationController extends Controller
{
    private $newPhotoPath;
    private $errors = [];
    public function index(Request $request) {
        $response = [];
        $this->validateFields($request);

        $status = 201;

        if(sizeof($this->errors)) {
            $response['success'] = false;
            $response['message'] = 'Validation failed';
            $response['fails'] = $this->errors;

            $status = 422;
        } else {
            $repeatedEmailOrPhoneUser = User::where('email', $request->email)->orWhere('phone', $request->phone)->first();

            if(!$repeatedEmailOrPhoneUser) {
                $newUser = new User();
                $newUser->name = $request->name;
                $newUser->email = $request->email;
                $newUser->phone = $request->phone;
                $newUser->position_id = $request->position_id;
                $newUser->photo = $this->newPhotoPath;
                $newUser->save();

                $response['success'] = true;
                $response['user_id'] = $newUser->id;
                $response['message'] = 'New user successfully registered';
            } else {
                $status = 409;
                $response['success'] = false;
                $response['message'] = 'User with this phone or email already exist';
            }
        }

        if($response['success']) {
            $token = $request->header('Authorization');
            $tokenId = explode(' ', $token)[1];
            $tokenId = explode('|', $tokenId)[0];

            DB::table('personal_access_tokens')->where('id', $tokenId)->delete();
        }

        return response()->json($response, $status);
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
            $this->errors['name'] = ['The name field is required.'];
        } else if(mb_strlen($name) < 2) {
            $this->errors['name'] = ['The name must be at least 2 characters.'];
        } else if(mb_strlen($name) > 60) {
            $this->errors['name'] = ['The name must be not longer than 60 characters.'];
        }
    }

    protected function isValidEmail($email)
    {
        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        if(empty($email)) {
            $this->errors['email'] = ['The email field is required.'];
        } else if(preg_match($pattern, $email) !== 1) {
            $this->errors['email'] = ['The email must be a valid email address.'];
        }
    }

    protected function isValidPhone($phone)
    {
        $pattern = '/^\+{0,1}380([0-9]{8})$/';
        if(empty($phone)) {
            $this->errors['phone'] = ['The phone field is required.'];
        } else if(preg_match($pattern, $phone) !== 1) {
            $this->errors['phone'] = ['The phone must be a valid.'];
        }
    }

    protected function isValidPositionId($positionId)
    {
        if(empty($positionId)) {
            $this->errors['position_id'] = ['The position id field is required.'];
        } if(!is_int($positionId)) {
            $this->errors['position_id'] = ['The position id must be an integer.'];
        } else if(!Position::where('id', $positionId)->first()) {
            $this->errors['position_id'] = ['The position id not found.'];
        }
    }

    protected function validateAndSavePhoto($photo) {
//      get extension
        if($photo) {
            $imageSizes = getimagesize($photo);
            if($imageSizes) {
                $mime = (explode('/', $imageSizes['mime']));
                $type = $mime[0];
                $extension = $mime[1];

                if ($type === 'image' && ($extension === 'jpg' || $extension === 'jpeg')) {
                    //          get photo size
                    $imageDecodedBase64 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photo));
                    $baseTempPath = rtrim(sys_get_temp_dir(), DIRECTORY_SEPARATOR);
                    $tempPath = $baseTempPath . '/' . time() . "." . $extension;
                    file_put_contents($tempPath, $imageDecodedBase64);
                    $imageSize = filesize($tempPath);

                    if ($imageSize <= 5 * 1024 * 1024) {
                        if ($imageSizes[0] >= 70 && $imageSizes[1] >= 70) {
                            $cropedPhoto = \App\Helpers\ImageHandler::cropAlign($tempPath, $imageSizes['mime'], 70, 70);

                            // save photo
                            $imageName = time() . '.' . $extension;
                            $imagePath = 'images/' . $imageName;
                            Storage::disk('public')->put($imagePath, $cropedPhoto);

//                            optimize photo
                            \Tinify\setKey(env('Tinify_API_KEY'));
                            $imageFullPath = Storage::disk('public')->path($imagePath);

                            $source = \Tinify\fromFile($imageFullPath);
                            $source->toFile($imageFullPath);

                            $this->newPhotoPath = '/image/' . $imageName;
                        } else {
                            $this->errors['photo'] = ['Minimum size of photo 70x70px.'];
                        }
                    } else {
                        $this->errors['photo'] = ['The photo size must not be greater than 5 Mb.'];
                    }
                } else {
                    $this->errors['photo'] = ['The photo format must be jpeg/jpg type.'];
                }
            } else {
                $this->errors['photo'] = ['The photo is required.'];
            }
        } else {
            $this->errors['photo'] = ['The photo is required.'];
        }
    }
}
