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

    public function index()
    {
        $category = Category::where('status', 1)->get();
        $class_section = ClassSection::with('class', 'section', 'streams')->get();
        return view('auth.register', compact('class_section', 'category'));
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
        //dd($request->all());
        //check student data
        $validator = Validator::make($request->all(), [
            //students
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => 'required|string|min:6',
            'mobile'   => 'required|string',
            'email' => 'required|email|unique:users,email',
            'class_section_id' => 'required|exists:class_sections,id'
        ]);

        if ($validator->fails()) {
            Alert::warning('Warning', $validator->messages()->all()[0]);
            return back()->withErrors($validator);
        } else {
            $category_id = 1;
            // Add student to users table
            $studentUser = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'mobile' => $request->mobile,
                'class_section_id' => $request->class_section_id,
                'category_id' => 1,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $studentRole = Role::where('name', 'Student')->first();
            $studentUser->assignRole($studentRole);

            // Add student to students table
            $student = Students::create([
                'user_id' => $studentUser->id,
                'class_section_id' => $request->class_section_id,
                'category_id' => 1,
                'father_id' => null,
                'mother_id' => null,
                'guardian_id' => null,
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
