<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\MailService;
use App\Services\VerificationService;
use App\Traits\Validation\HasUserValidation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends DatabaseController
{
    use HasUserValidation;

    public function sendEmailVerificationCode(
        Request $request,
        VerificationService $verification_service,
        MailService $mail_service
    ) {
        if ($request->wantsJson()) {
            $validator = Validator::make($request->all(), $this->getUserRules(['email']));

            if ($validator->fails()) {
                return response()->json($validator->errors()->get('email'), 400);
            }

            try {
                $this->beginTransaction();

                $code = $verification_service->generateCode($validator->validated()['email']);

                if ($code === null) {
                    $this->rollback();
                    return response()->json('Unable to generate verification code.', 500);
                }

                $mail_service->sendEmailVerificationCode($validator->validated()['email'], $code);

                $this->commit();

                return response()->json('Verification code has been sent.');
            } catch (\Exception $e) {
                $this->rollback();
                logger($e);
                return response()->json('Server error.', 500);
            }
        }
    }

    public function sendPasswordResetCode(
        Request $request,
        VerificationService $verification_service,
        MailService $mail_service
    ) {
        if ($request->wantsJson()) {
            $validator = Validator::make($request->all(), ['email' => 'required|email|exists:users']);

            if ($validator->fails()) {
                return response()->json($validator->errors()->get('email'), 400);
            }

            try {
                $this->beginTransaction();

                $code = $verification_service->generateCode($validator->validated()['email']);

                if ($code === null) {
                    $this->rollback();
                    return response()->json('Unable to generate verification code.', 500);
                }

                $mail_service->sendPasswordResetCode($validator->validated()['email'], $code);

                $this->commit();

                return response()->json('Verification code has been sent.');
            } catch (\Exception $e) {
                $this->rollback();
                logger($e);
                return response()->json('Server error.', 500);
            }
        }
    }

    public function findByEmail(Request $request)
    {
        if ($request->wantsJson()) {
            $user = User::query()
                ->where('email', $request->get('email'))
                ->whereNull('banned_until')
                ->first();

            if ($user === null) {
                return response()->json('User not found.', 404);
            } elseif ($user->id === Auth::user()->id) {
                return response()->json('Pick a user other than yourself.', 400);
            } else {
                return response()->json($user);
            }
        }
    }

    public function showRegistrationForm()
    {
        return view('pages.auth.registration');
    }

    public function register(Request $request, VerificationService $verification_service)
    {
        $validated_data = $request->validate($this->getUserRules());

        try {
            $this->beginTransaction();

            $verification_service->consumeVerificationCode($validated_data['email'], $validated_data['verification_code']);

            User::query()
                ->create([
                    'name' => $validated_data['name'],
                    'email' => $validated_data['email'],
                    'email_verified_at' => now(),
                    'password' => Hash::make($validated_data['password']),
                    'role' => 'user',
                ]);

            $this->commit();

            return back()
                ->with('message_type', 'success')
                ->with('message_content', 'You have successfully registered. You may now login.');
        } catch (\Exception $e) {
            $this->rollback();
            logger($e);
            return back()
                ->with('message_type', 'danger')
                ->with('message_content', 'Server error.');
        }
    }

    public function showPasswordResetForm()
    {
        return view('pages.auth.recovery');
    }

    public function resetPassword(Request $request)
    {
        $validatedData = $request->validate(array_merge(
            ['email' => 'required|email|exists:users'],
            $this->getUserRules(['verification_code', 'password', 'password_confirmation'])
        ));

        try {
            $user = User::query()
                ->where('email', $validatedData['email'])
                ->first();

            $user->update(['password' => Hash::make($validatedData['password'])]);

            return back()
                ->with('message_type', 'success')
                ->with('message_content', 'Password has been changed.');
        } catch (\Exception $e) {
            logger($e);
            return back()
                ->with('message_type', 'danger')
                ->with('message_content', 'Server error.');
        }
    }

    public function search(Request $request)
    {
        return redirect()
            ->route('user.list', [1, 25, $request->get('keyword')]);
    }

    public function list($current_page = 1, $items_per_page = 12, $keyword = null)
    {
        $this->authorize('list', new User());

        $users = User::query();

        if (!empty($keyword)) {
            $users->whereRaw('MATCH (name, email, role) AGAINST(? IN BOOLEAN MODE)', [$keyword.'*']);
        }

        $offset = ($current_page - 1) * $items_per_page;
        $total_count = $users->count();

        $list = $users->skip($offset)
            ->take($items_per_page)
            ->orderBy('name')
            ->get();

        return view('pages.user.list')
            ->with('users', $list)
            ->with('keyword', $keyword)
            ->with('pagination', view('partials.pagination')
                ->with('item_start', $offset + 1)
                ->with('item_end', $list->count() + $offset)
                ->with('total_count', $total_count)
                ->with('current_page', $current_page)
                ->with('total_pages', ceil($total_count / $items_per_page))
                ->with('items_per_page', $items_per_page)
                ->with('keyword', $keyword)
                ->with('route_name', 'user.list')
                ->with('route_params', [
                    'items_per_page' => $items_per_page,
                ])
            );
    }
}
