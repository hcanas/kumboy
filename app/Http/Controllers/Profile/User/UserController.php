<?php

namespace App\Http\Controllers\Profile\User;

use App\Events\UserChangeName;
use App\Events\UserChangePassword;
use App\Services\MailService;
use App\Services\VerificationService;
use App\Traits\Validation\HasUserValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;

class UserController extends ProfileController
{
    use HasUserValidation;

    public function showAccountSettings($id)
    {
        $this->authorize('viewAccountSettings', $this->user);

        return $this->profile
            ->with('content', 'users.profile.account_settings.index')
            ->with('contentData', ['user' => $this->user]);
    }

    public function changeName($id, Request $request)
    {
        $this->authorize('changeName', $this->user);

        $validated_data = $request->validate($this->getUserRules(['name']));

        try {
            $this->beginTransaction();

            $oldName = $this->user->name;
            $this->user->update(['name' => $validated_data['name']]);

            if ($this->user->wasChanged()) {
                event(new UserChangeName($this->user, $oldName));
            }

            $this->commit();

            return back()
                ->with('message_type', 'success')
                ->with('message_content', $this->user->wasChanged()
                    ? 'Name has been changed.'
                    : 'No changes were made.'
                );
        } catch (\Exception $e) {
            $this->beginTransaction();
            logger($e);
            return back()
                ->with('message_type', 'danger')
                ->with('message_content', 'Server error.');
        }
    }

    public function sendPasswordResetCode(
        $id,
        Request $request,
        VerificationService $verification_service,
        MailService $mail_service
    ) {
        if ($request->wantsJson()) {
            $gate = Gate::inspect('changePassword', $this->user);

            if ($gate->allowed()) {
                try {
                    $this->beginTransaction();

                    $verificationCode = $verification_service->generateCode($this->user->email);

                    if ($verificationCode === null) {
                        return response()->json('Unable to generate verification code.', 500);
                    }

                    if ($mail_service->sendPasswordResetCode($this->user->email, $verificationCode)) {
                        $this->commit();
                        return response()->json('Verification code has been sent.');
                    } else {
                        return response()->json('Unable to send verification code.', 500);
                    }
                } catch (\Exception $e) {
                    $this->rollback();
                    logger($e);
                    return response()->json('Server error.', 500);
                }
            } else {
                return response()->json('Forbidden', 403);
            }
        }
    }

    public function changePassword($id, Request $request, VerificationService $verification_service)
    {
        $this->authorize('changePassword', $this->user);

        // inject user email to request for verification code validation to work
        $request->merge(['email' => $this->user->email]);
        $validated_data = $request->validate($this->getUserRules([
            'password',
            'password_confirmation',
            'verification_code',
        ]));

        try {
            $this->beginTransaction();

            if ($verification_service->consumeVerificationCode($this->user->email, $validated_data['verification_code'])) {
                $this->user->update(['password' => Hash::make($validated_data['password'])]);
                event(new UserChangePassword($this->user));
                $this->commit();

                return back()
                    ->with('message_type', 'success')
                    ->with('message_content', 'Password has been changed.');
            } else {
                $this->rollback();

                return back()
                    ->with('message_type', 'danger')
                    ->with('message_content', 'Unable to use verification code.');
            }
        } catch (\Exception $e) {
            $this->rollback();
            logger($e);
            return back()
                ->with('message_type', 'danger')
                ->with('message_content', 'Server error.');
        }
    }
}
