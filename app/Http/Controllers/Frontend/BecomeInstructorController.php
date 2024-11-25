<?php

namespace App\Http\Controllers\Frontend;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\BecomeInstructorStoreRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\InstructorRequest\app\Models\InstructorRequest;
use Modules\InstructorRequest\app\Models\InstructorRequestSetting;
use Modules\PaymentWithdraw\app\Models\WithdrawMethod;

class BecomeInstructorController extends Controller
{

    function index(): View|RedirectResponse
    {
        if ($this->checkIfApproveInstructor()) return to_route('instructor.dashboard');

        $instructorRequestSetting = InstructorRequestSetting::first();
        $withdrawMethods = WithdrawMethod::where('status', 'active')->get();
        return view('frontend.pages.become-instructor', compact('withdrawMethods', 'instructorRequestSetting'));
    }

    function store(BecomeInstructorStoreRequest $request): RedirectResponse
    {
        if ($request->has('certificate') && count($request->file('certificate')) > 0) {
            foreach ($request->file('certificate') as $index => $certificateFile) {
                $filePath = file_upload($certificateFile);
                $identityFilePath = null;
                
                if ($index === 0 && $request->has('identity_scan')) {
                    $identityFilePath = file_upload($request->identity_scan);
                }

                $instructorRequest = InstructorRequest::create([
                    'user_id' => auth('web')->user()->id,
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
                'user_id' => auth('web')->user()->id,
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

        return redirect()->route('student.dashboard')->with([
            'success' => __('Instructor request submitted successfully. We will let you know when your account is approved'),
            'alert-type' => 'success'
        ]);
    }

    function checkIfApproveInstructor(): bool
    {
        return instructorStatus() == UserStatus::APPROVED->value;
    }
}
