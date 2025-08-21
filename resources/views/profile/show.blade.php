@extends('layouts.app')
@section('title', 'Profile')

@section('content')
    <div class="container px-6 mx-auto">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Profile
        </h2>

        <!-- Flex directo en horizontal -->
        <div class="flex flex-col sm:flex-row gap-6">
            <!-- Perfil (izquierda) -->
            <div class="sm:w-1/4 w-full bg-white dark:bg-gray-800 rounded-lg shadow-md self-start">
                <div class="relative">
                    <img class="w-full object-cover rounded-t-lg h-32"
                        src="https://images.unsplash.com/photo-1503264116251-35a269479413?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80"
                        alt="Cover Photo">
                </div>

                <div class="p-4 pt-12 text-center">
                    <img class="mx-auto w-20 h-20 rounded-full object-cover border-4 border-white dark:border-gray-800 -mt-12"
                        src="https://randomuser.me/api/portraits/men/75.jpg" alt="User Avatar">

                    <h3 class="mt-2 text-md font-semibold text-gray-700 dark:text-gray-200">John Doe</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">johndoe@example.com</p>

                    <p class="mt-2 text-xs text-gray-600 dark:text-gray-400">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                    </p>
                </div>
            </div>

            <!-- Formulario (derecha) -->
            <div class="sm:w-3/4 w-full bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-200 mb-4">Edit Profile</h3>
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf
                    <!-- Nombre -->
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Name</label>
                        <input type="text" id="name" name="name" value="John Doe"
                            class="w-full mt-1 p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Email</label>
                        <input type="email" id="email" name="email" value="johndoe@example.com"
                            class="w-full mt-1 p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                    </div>

                    <!-- Bio -->
                    <div class="mb-4">
                        <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Bio</label>
                        <textarea id="bio" name="bio" rows="4"
                            class="w-full mt-1 p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</textarea>
                    </div>

                    <!-- Foto de perfil -->
                    <div class="mb-4">
                        <label for="profile_photo"
                            class="block text-sm font-medium text-gray-700 dark:text-gray-200">Profile Photo</label>
                        <input type="file" id="profile_photo" name="profile_photo"
                            class="w-full mt-1 p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                    </div>

                    <!-- Cover -->
                    <div class="mb-4">
                        <label for="cover_photo" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Cover
                            Photo</label>
                        <input type="file" id="cover_photo" name="cover_photo"
                            class="w-full mt-1 p-2 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                    </div>

                    <!-- Botón -->
                    <button
                        class="flex items-center justify-between px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                        <i class="fas fa-save mr-2"></i>
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
