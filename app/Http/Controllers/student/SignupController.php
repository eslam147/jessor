<?php

namespace App\Http\Controllers\student;

use App\Models\User;
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

        $classSections = ClassSection::with(['class', 'section'])->withOutTrashedRelations('class', 'section')->get();
        return view('auth.register', compact('classSections', 'category'));
    }

    public function create(){}

    public function store(Request $request)
    {
        //check student data
        $validator = Validator::make($request->all(), [
            //students
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => 'required|string|min:6',
            'mobile' => 'required|string|digits:11|unique:users,mobile',
            'email' => 'required|email|unique:users,email',
            'class_section_id' => 'required|exists:class_sections,id'
        ]);

        if ($validator->fails()) {
            Alert::warning('Warning', $validator->messages()->all()[0]);
            return back()->withErrors($validator)->withInput();
        } else {
            // Add student to users table
            $userModel = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $studentRole = Role::where('name', 'Student')->first();
            $userModel->assignRole($studentRole);

            // Add student to students table
            $student = Students::create([
                'user_id' => $userModel->id,
                'class_section_id' => $request->class_section_id,
                'category_id' => 1,
                'father_id' => null,
                'mother_id' => null,
                'guardian_id' => null,
            ]);

            $this->guard()->login($userModel);

            if ($response = $this->registered($request, $userModel)) {
                return $response;
            }
            Alert::success('Success', "Welcome " . $request->first_name . " " . $request->last_name);
            return redirect()->intended(route('home.index'));
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

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
