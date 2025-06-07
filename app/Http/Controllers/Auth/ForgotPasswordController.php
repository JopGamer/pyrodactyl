<?php

namespace Pterodactyl\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Events\Auth\FailedPasswordReset;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Pterodactyl\Models\User;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * Send a reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        $this->validateEmail($request);

        // Check if user exists and has external_id
        $user = User::where('email', $request->input('email'))->first();
        
        if ($user && !is_null($user->external_id)) {
            // User has external_id (logged in through OpenID), deny password reset
            event(new FailedPasswordReset($request->ip(), $request->input('email')));
            
            // Return success response to avoid revealing account existence
            return $this->sendResetLinkResponse($request, Password::RESET_LINK_SENT);
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $this->credentials($request)
        );

        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($request, $response)
                    : $this->sendResetLinkFailedResponse($request, $response);
    }

    /**
     * Get the response for a failed password reset link.
     */
    protected function sendResetLinkFailedResponse(Request $request, $response): JsonResponse
    {
        // As noted in #358 we will return success even if it failed
        // to avoid pointing out that an account does or does not
        // exist on the system.
        event(new FailedPasswordReset($request->ip(), $request->input('email')));

        return $this->sendResetLinkResponse($request, Password::RESET_LINK_SENT);
    }

    /**
     * Get the response for a successful password reset link.
     *
     * @param string $response
     */
    protected function sendResetLinkResponse(Request $request, $response): JsonResponse
    {
        return response()->json([
            'status' => trans($response),
        ]);
    }
}
