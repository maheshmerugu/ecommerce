@extends('customer.layouts.app')

@section('title', 'Customer Login')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div>
            <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
                Sign in to your account
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Or
                <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    create a new account
                </a>
            </p>
        </div>
        
        <form class="mt-8 space-y-6" method="POST" action="{{ route('login') }}">
            @csrf
            
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="rounded-md shadow-sm -space-y-px">
                <div>
                    <label for="email" class="sr-only">Email address</label>
                    <input id="email" name="email" type="email" autocomplete="email" required 
                           class="relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm @error('email') border-red-500 @enderror" 
                           placeholder="Email address" 
                           value="{{ old('email') }}">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="relative">
                    <label for="password" class="sr-only">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required 
                           class="relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm @error('password') border-red-500 @enderror" 
                           placeholder="Password">
                    <button type="button" id="togglePassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500" aria-label="Show password">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 10C3.732 6.943 6.523 5 10 5s6.268 1.943 7.542 5c-1.274 3.057-4.065 5-7.542 5S3.732 13.057 2.458 10z" />
                            <path id="pupil" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 13a3 3 0 100-6 3 3 0 000 6z" />
                        </svg>
                    </button>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input id="remember" name="remember" type="checkbox" 
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="remember" class="ml-2 block text-sm text-gray-900">
                        Remember me
                    </label>
                </div>

                <div class="text-sm">
                    <a href="{{ route('password.request') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Forgot your password?
                    </a>
                </div>
            </div>

            <div>
                <button type="submit" 
                        class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Sign in
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    (function(){
        const toggle = document.getElementById('togglePassword');
        const input = document.getElementById('password');
        if (!toggle || !input) return;
        toggle.addEventListener('click', function(){
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            // toggle aria-label
            toggle.setAttribute('aria-label', type === 'password' ? 'Show password' : 'Hide password');
        });
    })();
</script>
@endpush