@extends('layouts.guest')
@section('title', 'Login')

@section('content')
    <!-- Session Status -->
    <x-session-message class="mb-4" :status="session('status')" />

    <div class="flex flex-col overflow-y-auto md:flex-row">
        <div class="h-32 md:h-auto md:w-1/2">
            <img aria-hidden="true" class="object-cover w-full h-full dark:hidden" src="{{ asset('img/login-office.jpeg')}}"
                alt="Office" />
            <img aria-hidden="true" class="hidden object-cover w-full h-full dark:block"
                src="{{ asset('img/login-office-dark.jpeg')}}" alt="Office" />
        </div>
        <form method="POST" action="{{ route('login') }}" class="flex items-center justify-center p-6 sm:p-12 md:w-1/2">
            @csrf
            <div class="w-full">
                <h1 class="mb-4 text-xl font-semibold text-gray-700 dark:text-gray-200">
                    Login
                </h1>
                <label class="block text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Email</span>
                    <input
                        class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                        type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        placeholder="user@example.test" />
                </label>
                <label class="block mt-4 text-sm">
                    <span class="text-gray-700 dark:text-gray-400">Password</span>
                    <input
                        class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                        placeholder="***************" type="password" name="password" required
                        autocomplete="current-password" />
                </label>

                <button type="submit"
                    class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                    Log in
                </button>

                <hr class="my-8" />

                <button
                    class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium leading-5 text-gray-700 transition-colors duration-150 border border-gray-300 rounded-lg dark:text-gray-400 active:bg-transparent hover:border-gray-500 focus:border-gray-500 active:text-gray-500 focus:outline-none focus:shadow-outline-gray">
                    <i class="fab fa-github w-4 h-4 mr-2"></i>
                    Github
                </button>
                <button
                    class="flex items-center justify-center w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-gray-700 transition-colors duration-150 border border-gray-300 rounded-lg dark:text-gray-400 active:bg-transparent hover:border-gray-500 focus:border-gray-500 active:text-gray-500 focus:outline-none focus:shadow-outline-gray">
                    <i class="fab fa-google w-4 h-4 mr-2"></i>
                    Google
                </button>

                <p class="mt-4">
                    <a class="text-sm font-medium text-purple-600 dark:text-purple-400 hover:underline"
                        href="{{ route('password.request') }}">
                        Forgot your password?
                    </a>
                </p>
                <p class="mt-1">
                    <a class="text-sm font-medium text-purple-600 dark:text-purple-400 hover:underline"
                        href="{{ route('register') }}">
                        Create account
                    </a>
                </p>
            </div>
        </form>
    </div>
@endsection
