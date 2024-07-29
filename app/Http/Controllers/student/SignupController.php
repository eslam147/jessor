<?php

namespace App\Http\Controllers\student;

use App\Models\User;
use App\Models\Parents;
use App\Models\Category;
use App\Models\Students;
use App\Models\ClassSection;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Validator;

class SignupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::where('status', 1)->get();
        $class_section = ClassSection::with('class', 'section', 'streams')->get();
        return view('auth.register',compact('class_section','category'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }


    public function store(Request $request)
    {

        #check if admin has permission

        //Add Father in User and Parent table data
        //check if isset parent
        // if (isset($request->parent)) {
        //     //validate parent's data
        //     $validator = Validator::make($request->all(), [
        //         //father
        //         'father_email' => 'required|email',
        //         'father_first_name' => 'required|string',
        //         'father_last_name' => 'required|string',
        //         'father_mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15',
        //         'father_password' => 'required|string|min:6',
        //         //mother
        //         'mother_email' => 'required|email',
        //         'mother_first_name' => 'required|string',
        //         'mother_last_name' => 'required|string',
        //         'mother_mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15',
        //         'mother_password' => 'required|string|min:6',
        //     ]);

        //     //add Parents
        //     if ($validator->fails()) {
        //         $response = array(
        //             'error' => true,
        //             'message' => $validator->messages()->all()[0],
        //         );
        //         return response()->json($response);
        //     } else {
        //         //check if the email is exist
        //         $fatherExists = User::where('email', $request->father_email)->exists();
        //         if ($fatherExists) {
        //             $response = array(
        //                 'error' => true,
        //                 'message' => 'the father email you are using is alredy exist',
        //             );
        //             return response()->json($response);
        //         } else {
        //             $father = User::create([
        //                 'first_name' => $request->father_first_name,
        //                 'last_name' => $request->father_last_name,
        //                 'gender' => 'Male',
        //                 'email' => $request->father_email,
        //                 'password' => Hash::make($request->password),
        //                 'mobile' => $request->father_mobile,

        //             ]);

        //             //add father to parent table
        //             Parents::create([
        //                 'user_id' => $father->id,
        //                 'first_name' => $request->father_first_name,
        //                 'last_name' => $request->father_last_name,
        //                 'gender' => 'Male',
        //                 'email' => $request->father_email,
        //                 'password' => Hash::make($request->password),
        //                 'mobile' => $request->father_mobile,
        //             ]);
        //         }
        //         //add mother to user table
        //         //check if the email is exist
        //         $motherExists = User::where('email', $request->mother_email)->exists();
        //         if ($motherExists) {
        //             $response = array(
        //                 'error' => true,
        //                 'message' => 'the mother email you are using is alredy exist',
        //             );
        //             return response()->json($response);
        //         } else {
        //             $mother = User::create([
        //                 'first_name' => $request->mother_first_name,
        //                 'last_name' => $request->mother_last_name,
        //                 'gender' => 'Male',
        //                 'email' => $request->mother_email,
        //                 'password' => Hash::make($request->password),
        //                 'mobile' => $request->mother_mobile,

        //             ]);

        //             //add Mother to parent table
        //             Parents::create([
        //                 'user_id' => $mother->id,
        //                 'first_name' => $request->mother_first_name,
        //                 'last_name' => $request->mother_last_name,
        //                 'gender' => 'Male',
        //                 'email' => $request->mother_email,
        //                 'mobile' => $request->mother_mobile,
        //             ]);
        //         }

        //     }



        // }
        //check if isset guardian
        // if (isset($request->guardian)) {
        //     $validate = Validator::make($request->all(), [
        //         //father
        //         'guardian_email' => 'required|email',
        //         'guardian_first_name' => 'required|string',
        //         'guardian_last_name' => 'required|string',
        //         'guardian_mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:15',
        //         'guardian_password' => 'required|string|min:6',
        //     ]);

        //     if ($validator->fails()) {
        //         $response = array(
        //             'error' => true,
        //             'message' => $validator->messages()->all()[0],
        //         );
        //         return response()->json($response);
        //     } else {
        //         $guardian = User::create([
        //             'first_name' => $request->guardian_first_name,
        //             'last_name' => $request->guardian_last_name,
        //             'gender' => $request->guardian_gender,
        //             'email' => $request->guardian_email,
        //             'password' => Hash::make($request->password),
        //             'mobile' => $request->guardian_mobile,
        //         ]);

        //         //add Mother to parent table
        //         Parents::create([
        //             'user_id' => $guardian->id,
        //             'first_name' => $request->guardian_first_name,
        //             'last_name' => $request->guardian_last_name,
        //             'gender' => 'Male',
        //             'email' => $request->guardian_email,
        //             'password' => Hash::make($request->password),
        //             'mobile' => $request->guardian_mobile,
        //         ]);
        //     }
        // }


        //check student data
        $validator = Validator::make($request->all(), [
            //students
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => 'required|string|min:6',
            'email' => 'required|email|unique:users,email',
        ]);

        if ($validator->fails()) {
            Alert::warning('Warning',$validator->messages()->all()[0]);
            return redirect()->back();
        } else {
            // Add student to users table
            $studentUser = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'class_section_id' => $request->class_section_id,
                'category_id' => $request->category_id,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $studentRole = Role::where('name', 'Student')->first();
            $studentUser->assignRole($studentRole);

            // Add student to students table
            $student = Students::create([
                'user_id' => $studentUser->id,
                'class_section_id' => $request->class_section_id,
                'category_id' => $request->category_id,
                'father_id' => isset($father) ? $father->id : null,
                'mother_id' => isset($mother) ? $mother->id : null,
                'guardian_id' => isset($guardian) ? $guardian->id : null,
            ]);

            $this->guard()->login($studentUser);

            if ($response = $this->registered($request, $studentUser)) {
                return $response;
            }

            return redirect()->route('home.index');
        }
    }

    protected function guard()
    {
        return Auth::guard('web'); // Use the appropriate guard
    }

    protected function registered(Request $request, $user)
    {
        //
    }

    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
