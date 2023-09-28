<?php

namespace App\Http\Controllers;

use App\Models\{User};
use Illuminate\Support\Facades\{Auth, Hash};
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Function for Edit User Profile
    public function editProfile($id)
    {
        $data['user'] = User::where('id',decrypt($id))->first();
        return view('auth.client-profile-edit',$data);
    }


    // Function for View User Profile
    public function myProfile($id)
    {
        $data['user'] = User::where('id',decrypt($id))->first();
        return view('auth.client-profile',$data);
    }


    // Function for Update User Profile
    public function updateProfile(Request $request)
    {
        $user  = User::find($request->user_id);

        $request->validate([
            'firstname'         =>      'required',
            'email'             =>      'required|email|unique:users,email,'.$request->user_id,
            'confirm_password'  =>      'same:password',
            'profile_picture'   =>      'mimes:png,jpg,svg,jpeg,PNG,SVG,JPG,JPEG',
            'address'           =>      'required',
            'mobile_no'         =>      'required',
            'gst_number'        => 'nullable|min:15',
        ]);

        $explode_emails = explode(',',str_replace(' ','',$request->contact_emails));
        $contact_emails = serialize($explode_emails);

        // User Update
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->email = $request->email;
        $user->contact_emails = $contact_emails;
        $user->mobile = $request->mobile_no;
        $user->gst_number = $request->gst_number;
        $user->sgst = $request->sgst;
        $user->cgst = $request->cgst;
        $user->vat_id = $request->vat_id;
        $user->gemi_id = $request->gemi_id;
        $user->address = $request->address;

        if(!empty($request->password))
        {
            $user->password = Hash::make($request->password);
        }

        if($request->hasFile('profile_picture'))
        {
            // Remove Old Image
            $old_image = isset($user->image) ? $user->image : '';
            if(!empty($old_image) && file_exists($old_image))
            {
                unlink($old_image);
            }

            // Insert New Image
            $imgname = time().".". $request->file('profile_picture')->getClientOriginalExtension();
            $request->file('profile_picture')->move(public_path('admin_uploads/users/'), $imgname);
            $imageurl = asset('/').'public/admin_uploads/users/'.$imgname;
            $user->image = $imageurl;
        }
        $user->update();

        return redirect()->route('client.profile.view',encrypt($request->user_id))->with('success','Profile has been Updated SuccessFully..');

    }


    // Function for Update User Profile Picture
    public function deleteProfilePicture()
    {
        $user_id = Auth::user()->id;
        $user = User::find($user_id);

        if($user)
        {
            $user_image = isset($user->image) ? $user->image : '';
            if(!empty($user_image))
            {
                $new_path = str_replace(asset('/public/'),public_path(),$user_image);
                if(file_exists($new_path))
                {
                    unlink($new_path);
                }
            }

            $user->image = "";
        }

        $user->update();

        return redirect()->back()->with('success', "Profile Picture has been Removed SuccessFully..");
    }


    // Verify Client Password
    function verifyClientPassword(Request $request)
    {
        try
        {
            $user_password = (isset(Auth::user()->password)) ? Auth::user()->password : '';
            $current_password = $request->password;

            if(Hash::check($current_password,$user_password))
            {
                return response()->json([
                    'success' => 1,
                    'matched' => 1,
                    'message' => 'Matched SuccessFully....',
                ]);
            }
            else
            {
                return response()->json([
                    'success' => 1,
                    'matched' => 0,
                    'message' => 'Password does not Match!',
                ]);
            }

        }
        catch (\Throwable $th)
        {
            return response()->json([
                'success' => 0,
                'message' => 'Internal Server Error!',
            ]);
        }
    }

}
