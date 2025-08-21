@extends('layouts.guest')
@section('title', 'Forgot Password')

@section('content')
    <!-- Session Status -->
    <x-session-message class="mb-4" :status="session('status')" />

    <div class="flex-1 h-full max-w-4xl mx-auto overflow-hidden bg-white rounded-lg shadow-xl dark:bg-gray-800">
        <div class="flex flex-col overflow-y-auto md:flex-row">
            <div class="h-32 md:h-auto md:w-1/2">
                <img aria-hidden="true" class="object-cover w-full h-full dark:hidden"
                    src="{{ asset('img/forgot-password-office.jpeg')}}" alt="Office" />
                <img aria-hidden="true" class="hidden object-cover w-full h-full dark:block"
                    src="{{ asset('img/forgot-password-office-dark.jpeg')}}" alt="Office" />
            </div>
            <form action="{{ route('password.email') }}" class="flex items-center justify-center p-6 sm:p-12 md:w-1/2">
                @csrf
                <div class="w-full">
                    <h1 class="mb-4 text-xl font-semibold text-gray-700 dark:text-gray-200">
                        Forgot password
                    </h1>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        Forgot your password? No problem. Just let us know your email address and we will email you a
                        password reset link that will allow you to choose a new one.
                    </p>

                    <label class="block text-sm">
                        <span class="text-gray-700 dark:text-gray-400">Email</span>
                        <input type="email"
                            class="block w-full mt-1 text-sm dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:text-gray-300 dark:focus:shadow-outline-gray form-input"
                            placeholder="Write your email..." name="email" value="{{ old('email') }}" required autofocus />
                    </label>

                    <button type="submit"
                        class="flex items-center justify-center w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                        <i class="fas fa-envelope mr-2"></i>
                        Email Password Reset Link
                    </button>

                    <a href="{{ route('login') }}"
                        class="flex items-center justify-center w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center text-gray-700 transition-colors duration-150 border border-gray-300 rounded-lg dark:text-gray-400 dark:border-gray-600 hover:border-gray-500 focus:outline-none focus:shadow-outline-gray">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
