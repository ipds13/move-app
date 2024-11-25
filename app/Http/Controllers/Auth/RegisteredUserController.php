<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\CustomRecaptcha;
use App\Services\MailSenderService;
use App\Traits\GetGlobalInformationTrait;
use Cache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Str;
use Modules\InstructorRequest\app\Models\InstructorRequest;
use Modules\InstructorRequest\app\Models\InstructorRequestSetting;
use Modules\PaymentWithdraw\app\Models\WithdrawMethod;
use App\Enums\SuperInstructor;

class RegisteredUserController extends Controller
{
    use GetGlobalInformationTrait;

    public function create(): View
    {
        $instructorRequestSetting = InstructorRequestSetting::first();
        $withdrawMethods = WithdrawMethod::where('status', 'active')->get();
        return view('auth.register', [
            'instructorRequestSetting' => $instructorRequestSetting,
            'withdrawMethods' => $withdrawMethods,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $setting = Cache::get('setting');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', 'min:4', 'max:100'],
            'role' => ['required', 'in:instructor,student'],
            'g-recaptcha-response' => $setting->recaptcha_status == 'active' ? ['required', new CustomRecaptcha()] : '',
        ];
    
        if ($request->role === 'instructor') {
            $rules = array_merge($rules, [
                'certificate.*' => 'required|file|mimes:pdf,png,jpg,jpeg|max:1024',
                'identity_scan' => 'required|file|mimes:pdf,png,jpg,jpeg|max:1024',
                'payout_account' => 'required|string',
                'payout_information' => 'required|string|max:500',
                'extra_information' => 'required|string|max:500',
            ]);
        }

        $messages = [
            'name.required' => __('Name is required'),
            'email.required' => __('Email is required'),
            'email.unique' => __('Email already exist'),
            'password.required' => __('Password is required'),
            'password.confirmed' => __('Confirm password does not match'),
            'password.min' => __('You have to provide minimum 4 character password'),
            'role.required' => __('Role is required'),
            'role.in' => __('Invalid role selected'),
            'g-recaptcha-response.required' => __('Please complete the recaptcha to submit the form'),
            'certificate.*.mimes' => __('Certificate must be a file of type: pdf, png, jpg, jpeg'),
            'certificate.*.max' => __('Certificate size must be under 1 MB'),
            'identity_scan.mimes' => __('Identity scan must be a file of type: pdf, png, jpg, jpeg'),
            'identity_scan.max' => __('Identity scan size must be under 1 MB'),
        ];
    
        $request->validate($rules, $messages);    
        $superInstructors = $request->role === 'instructor' ? SuperInstructor::YES : SuperInstructor::NO;

        $user = User::create([
            'role' => $request->role,
            'name' => $request->name,
            'email' => $request->email,
            'status' => 'active',
            'is_banned' => 'no',
            'password' => Hash::make($request->password),
            'verification_token' => Str::random(100),
            'super_instructors' => $superInstructors,
        ]);

        $settings = cache()->get('setting');
        $marketingSettings = cache()->get('marketing_setting');
        if ($user && $settings->google_tagmanager_status == 'active' && $marketingSettings->register) {
            $register_user = [
                'name' => $user->name,
                'email' => $user->email,
            ];
            session()->put('registerUser', $register_user);
        }

        if ($request->role === 'instructor') {
            if ($request->has('certificate') && count($request->file('certificate')) > 0) {
                foreach ($request->file('certificate') as $index => $certificateFile) {
                    $filePath = file_upload($certificateFile);
                    $identityFilePath = null;
                    
                    if ($index === 0 && $request->has('identity_scan')) {
                        $identityFilePath = file_upload($request->identity_scan);
                    }

                    $instructorRequest = InstructorRequest::create([
                        'user_id' => $user->id,
                        'status' => 'pending', 
                        'payout_account' => $request->payout_account,
                        'payout_information' => $request->payout_information,
                        'extra_information' => $request->extra_information,
                        'certificate' => $filePath,
                        'identity_scan' => $identityFilePath,
                    ]);
        
                    $instructorRequest->save();
                }
            } else {
                $instructorRequest = InstructorRequest::create([
                    'user_id' => $user->id,
                    'status' => 'pending',
                    'payout_account' => $request->payout_account,
                    'payout_information' => $request->payout_information,
                    'extra_information' => $request->extra_information,
                    'certificate' => null,
                ]);
        
                if ($request->has('identity_scan')) {
                    $filePath = file_upload($request->identity_scan);
                    $instructorRequest->identity_scan = $filePath;
                    $instructorRequest->save();
                }
            }
        }

        (new MailSenderService)->sendVerifyMailToUserFromTrait('single_user', $user);

        if ($request->role === 'instructor') {
            $notification = __('A verification link has been sent to your mail, please verify and Instructor request submitted successfully. We will let you know when your account is approved.');
        } else {
            $notification = __('A verification link has been sent to your mail, please verify and enjoy our service.');
        }
        $notification = ['messege' => $notification, 'alert-type' => 'success'];

        return redirect()->back()->with($notification);
    }

    public function custom_user_verification($token)
    {

        $user = User::where('verification_token', $token)->first();
        if ($user) {

            if ($user->email_verified_at != null) {
                $notification = __('Email already verified');
                $notification = ['messege' => $notification, 'alert-type' => 'error'];

                return redirect()->route('login')->with($notification);
            }

            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->verification_token = null;
            $user->save();

            $notification = __('Verification successful please try to login now');
            $notification = ['messege' => $notification, 'alert-type' => 'success'];
            return redirect()->route('login')->with($notification);
        } else {
            $notification = __('Invalid token');
            $notification = ['messege' => $notification, 'alert-type' => 'error'];

            return redirect()->route('register')->with($notification);
        }
    }
}
