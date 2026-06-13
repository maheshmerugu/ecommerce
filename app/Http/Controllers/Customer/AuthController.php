<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeEmail;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Customer;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Show the customer login form.
     */
    public function showLogin(Request $request)
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard');
        }

        // Store intended URL if redirect parameter is provided
        if ($request->has('redirect')) {
            $request->session()->put('url.intended', $request->get('redirect'));
        }

        return view('customer.auth.login');
    }

    /**
     * Handle customer login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Capture guest session cart before session regeneration
        $guestSessionId = Session::getId();
        $guestCart = Cart::where('session_id', $guestSessionId)
                         ->whereNull('customer_id')
                         ->with('items')
                         ->first();

        if (Auth::guard('customer')->attempt($request->only('email', 'password'), $request->filled('remember'))) {
            $request->session()->regenerate();

            $customer = Auth::guard('customer')->user();

            // Update last login
            $customer->update(['last_login_at' => now()]);

            // Merge guest cart into customer cart
            if ($guestCart && $guestCart->items->isNotEmpty()) {
                $customerCart = $customer->getOrCreateCart();
                foreach ($guestCart->items as $guestItem) {
                    $existing = $customerCart->items()->where('product_id', $guestItem->product_id)->first();
                    if ($existing) {
                        $existing->increment('quantity', $guestItem->quantity);
                    } else {
                        $customerCart->items()->create([
                            'product_id' => $guestItem->product_id,
                            'quantity'   => $guestItem->quantity,
                            'price'      => $guestItem->price,
                        ]);
                    }
                }
                $guestCart->delete();
            }

            return redirect()->intended(route('customer.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => __('The provided credentials do not match our records.'),
        ]);
    }

    /**
     * Show the form to request a password reset link.
     */
    public function showForgotForm()
    {
        return view('customer.auth.forgot');
    }

    /**
     * Send a password reset link to the given user.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('customers')->sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    /**
     * Show the password reset form for the given token.
     */
    public function showResetForm($token)
    {
        return view('customer.auth.reset', ['token' => $token]);
    }

    /**
     * Handle the password reset.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $status = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request) {
                $user->password = Hash::make($password);
                $user->setRememberToken(Str::random(60));
                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }

    /**
     * Show the customer registration form.
     */
    public function showRegister(Request $request)
    {
        if (Auth::guard('customer')->check()) {
            return redirect()->route('customer.dashboard');
        }

        // Store intended URL if redirect parameter is provided
        if ($request->has('redirect')) {
            $request->session()->put('url.intended', $request->get('redirect'));
        }

        return view('customer.auth.register');
    }

    /**
     * Handle customer registration request.
     */
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:male,female,other'],
        ]);

        $customer = Customer::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'is_active' => true,
        ]);

        event(new Registered($customer));

        Mail::to($customer->email)->send(new WelcomeEmail($customer));

        Auth::guard('customer')->login($customer);

        return redirect()->intended(route('customer.dashboard'));
    }

    /**
     * Handle customer logout request.
     */
    public function logout(Request $request)
    {
        Auth::guard('customer')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
