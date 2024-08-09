<?php
namespace App\Services\Auth;

use App\Models\{
    FeesPaid,
    User,
    Parents,
    Students,
    FormField,
    SessionYear,
};

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Storage;

class RegisterAuthService
{
    private static $parentRole;
    const FEMALE = 'female';
    const MALE = 'male'; 
    public function __construct()
    {
        self::$parentRole = Role::where('name', 'Parent')->first();
    }

    public function storeParents($request)
    {
        if(! intval($request->father_email)) {
            $father = $this->storeFather($request);
        }else{
            $father = $this->findExistingParent($request->father_email);
        }

        //Add Mother in User and Parent table data
        if (! intval($request->mother_email)) {
            $mother = $this->storeMother($request);
        } else {
            $mother = $this->findExistingParent($request->mother_email);
        }

        return compact('father', 'mother');
    }



    private function findExistingParent($parentId)
    {
        return Parents::whereId($parentId)->first();
    }
    protected function createUser($firstName, $lastName, $mobile, $email, $password, $gender): User
    {
        return User::create([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'mobile' => $mobile,
            // ------------------------------------------------ \\
            'password' => bcrypt($password),
            'email' => $email,
            // ------------------------------------------------ \\
            'gender' => $gender,
            // ------------------------------------------------ \\
        ]);
    }
    private function storeFather($request)
    {
        #TODO Use Password Of Father Request
        $father_plaintext_password = str_replace('-', '', date('d-m-Y', strtotime($request->father_dob)));

        $father_email = $request->father_email;
        $gender = 'Male';
        $user = $this->createUser(
            $request->father_first_name,
            $request->father_last_name,
            $request->father_mobile,
            $father_email,
            $father_plaintext_password,
            $gender
        );
        $user->assignRole(self::$parentRole);

        //Parent Dynamic FormField
        $father = new Parents();
        $fatherFields = FormField::where('for', 2)->orderBy('rank')->get();

        $status = 0;
        $dynamic_data = json_decode($father->dynamic_field_values, true);
        $parentFormData = $this->setCustomFormFields(
            $fatherFields,
            $request,
            $status,
            $dynamic_data
        );

        // End Parent Dynamic FormField
        // --------------------------------------------------- \\
        $father->user_id = $user->id;
        $father->first_name = $request->father_first_name;
        $father->last_name = $request->father_last_name;
        $father->mobile = $request->father_mobile;
        $father->email = $request->father_email;
        $father->gender = $gender;
        $father->dynamic_fields = json_encode($parentFormData);
        // --------------------------------------------------- \\

        $father->save();

        return $father;
    }

    private function setSetudentGrNumber()
    {
        $data = getSettings('session_year');

        $session_year = SessionYear::select('name')->where('id', $data['session_year'])->pluck('name')->first();
        $get_student = Students::withTrashed()->select('id')->latest('id')->pluck('id')->first();
        return $session_year . ($get_student + 1);

    }
    private function storeMother($request): User
    {
        $mother_plaintext_password = str_replace('-', '', date('d-m-Y', strtotime($request->mother_dob)));
        $mother_email = $request->mother_email;
        $mother = $this->createUser(
            $request->mother_first_name,
            $request->mother_last_name,
            $request->mother_mobile,
            $mother_email,
            $mother_plaintext_password,
            'Female'
        );
        $mother->assignRole(self::$parentRole);

        $mother_parent = new Parents();

        //Parent Dynamic FormField
        $motherFields = FormField::where('for', 2)->orderBy('rank', 'ASC')->get();
        $status = 0;
        $dynamic_data = json_decode($mother_parent->dynamic_field_values, true);

        $data = $this->setCustomFormFields(
            $motherFields,
            $request,
            $status,
            $dynamic_data
        );
        // End Parent Dynamic FormField

        $mother_parent->user_id = $mother->id;
        $mother_parent->first_name = $request->mother_first_name;
        $mother_parent->last_name = $request->mother_last_name;

        $mother_parent->mobile = $request->mother_mobile;
        $mother_parent->email = $request->mother_email;

        $mother_parent->gender = 'Female';
        $mother_parent->dynamic_fields = json_encode($data);

        $mother_parent->save();

        return $mother;
    }

    public function storeGuardian($request): ?User
    {
        if (! empty($request->guardian_email)) {
            if (! intval($request->guardian_email)) {
                return $this->storeGuardianModel($request);
            } else {
                return $this->findExistingParent($request->guardian_email);
            }
        }
        return null;
    }

    private function storeGuardianModel($request)
    {
        $guardian_plaintext_password = str_replace('-', '', date('d-m-Y', strtotime($request->guardian_dob)));
        $guardian_email = $request->guardian_email;
        $guardian_user = $this->createUser(
            $request->guardian_first_name,
            $request->guardian_last_name,
            $request->guardian_mobile,
            $request->guardian_email,
            $guardian_plaintext_password,
            $request->guardian_gender,
        );

        $guardian_user->assignRole(self::$parentRole);

        $guardian_parent = new Parents();

        //Parent Dynamic FormField
        $guardianFields = FormField::where('for', 2)->orderBy('rank', 'ASC')->get();

        $status = 0;
        $dynamic_data = json_decode($guardian_parent->dynamic_field_values, true);

        $data = $this->setCustomFormFields($guardianFields, $request, $status, $dynamic_data);
        // End Parent Dynamic FormField

        $guardian_parent->user_id = $guardian_user->id;
        $guardian_parent->first_name = $request->guardian_first_name;
        $guardian_parent->last_name = $request->guardian_last_name;

        $guardian_parent->email = $guardian_email;
        $guardian_parent->mobile = $request->guardian_mobile;
        $guardian_parent->gender = $request->guardian_gender;
        $guardian_parent->dynamic_fields = json_encode($data);
        $guardian_parent->save();

        return $guardian_user;
    }
    public function storeStudent($request, $fatherId = null, $motherId = null, $guardianId = null)
    {
        $studentRole = Role::where('name', 'Student')->first();
        $password = ! empty($request->password) ? $request->password : str_replace('-', '', date('d-m-Y', strtotime($request->dob)));
        $user = new User();
        $user = $this->createUser(
            $request->first_name,
            $request->last_name,
            $request->mobile,
            $request->email_addreess,
            $password,
            null,
        );

        $user->save();
        $user->assignRole($studentRole);

        $student = new Students();

        // End student dynamic field
        $student->user_id = $user->id;
        $student->class_section_id = $request->class_section_id;
        $student->category_id = $request->category_id;
        $student->admission_no = $this->setSetudentGrNumber();

        $student->father_id = $fatherId;
        $student->mother_id = $motherId;
        $student->guardian_id = $guardianId;
        $student->dynamic_fields = null;
        $student->save();
        return $user;
    }


    public function setCustomFormFields($formFields, $request, $status, $dynamic_data)
    {
        $data = [];
        foreach ($formFields as $form_field) {

            // INPUT TYPE CHECKBOX
            if ($form_field->type == 'checkbox') {
                if ($status == 0) {
                    $data[] = $request->input('father_checkbox', []);
                    $status = 1;
                }
            } else if ($form_field->type == 'file') {
                // INPUT TYPE FILE
                $get_file = '';
                $field = "father_" . str_replace(" ", "_", $form_field->name);
                if ($dynamic_data && count($dynamic_data) > 0) {
                    foreach ($dynamic_data as $field_data) {
                        if (isset($field_data[$field])) { // GET OLD FILE IF EXISTS
                            $get_file = $field_data[$field];
                        }
                    }
                }
                $hidden_file_name = 'file-' . $field;

                if ($request->hasFile($field)) {
                    if ($get_file) {
                        Storage::disk('public')->delete($get_file); // DELETE OLD FILE IF NEW FILE IS SELECT
                    }
                    $data[] = [str_replace(" ", "_", $form_field->name) => $request->file($field)->store('parent', 'public')];
                } else {
                    if ($request->$hidden_file_name) {
                        $data[] = [str_replace(" ", "_", $form_field->name) => $request->$hidden_file_name];
                    }
                }
            } else {
                $field = "father_" . str_replace(" ", "_", $form_field->name);
                $data[] = [str_replace(" ", "_", $form_field->name) => $request->$field];
            }
        }
        return $data;
    }
}
