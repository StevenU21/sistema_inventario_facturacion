<div class="px-4 py-3 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
    <!-- Name Field -->
    <label class="block text-sm">
        <span class="text-gray-700 dark:text-gray-400">Name</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <input name="name"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                placeholder="Write a name..." value="Example Name" />
            <div class="absolute inset-y-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-user w-5 h-5"></i>
            </div>
        </div>
        <span class="text-xs text-red-600 dark:text-red-400">
            Example error message for Name field.
        </span>
    </label>

    <!-- Description Field -->
    <label class="block mt-4 text-sm">
        <span class="text-gray-700 dark:text-gray-400">Description</span>
        <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
            <textarea name="description"
                class="block w-full pl-10 mt-1 text-sm dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-textarea focus:border-purple-400 focus:shadow-outline-purple dark:focus:shadow-outline-gray"
                rows="3" placeholder="Enter some content.">Example description content.</textarea>
            <div class="absolute inset-y-0 left-0 flex items-center ml-3 pointer-events-none">
                <i class="fas fa-comment w-5 h-5"></i>
            </div>
        </div>
        <span class="text-xs text-red-600 dark:text-red-400">
            Example error message for Description field.
        </span>
    </label>

    <!-- Submit Button -->
    <div class="mt-6">
        <button type="button"
            class="flex items-center px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple active:bg-purple-600">
            <i class="fas fa-paper-plane mr-2"></i> Submit
        </button>
    </div>
</div>
