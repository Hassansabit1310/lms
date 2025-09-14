<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-3xl font-black text-gray-900 dark:text-white mb-2">
            🌟 Join EduVerse!
        </h2>
        <p class="text-slate-600 dark:text-gray-400">
            Create your account and start learning today
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- Name -->
        <div>
            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                👤 Full Name
            </label>
            <input id="name" 
                   type="text" 
                   name="name" 
                   value="{{ old('name') }}" 
                   required 
                   autofocus 
                   autocomplete="name"
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:bg-gray-800 dark:text-white placeholder-gray-400 transition-all duration-200"
                   placeholder="Enter your full name">
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                📧 Email Address
            </label>
            <input id="email" 
                   type="email" 
                   name="email" 
                   value="{{ old('email') }}" 
                   required 
                   autocomplete="username"
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:bg-gray-800 dark:text-white placeholder-gray-400 transition-all duration-200"
                   placeholder="Enter your email address">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                🔒 Password
            </label>
            <input id="password" 
                   type="password" 
                   name="password" 
                   required 
                   autocomplete="new-password"
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:bg-gray-800 dark:text-white placeholder-gray-400 transition-all duration-200"
                   placeholder="Create a strong password">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                🔑 Confirm Password
            </label>
            <input id="password_confirmation" 
                   type="password" 
                   name="password_confirmation" 
                   required 
                   autocomplete="new-password"
                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent dark:bg-gray-800 dark:text-white placeholder-gray-400 transition-all duration-200"
                   placeholder="Confirm your password">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <!-- Submit Button -->
        <button type="submit" 
                class="w-full bg-gradient-to-r from-green-600 to-blue-600 text-white font-bold py-4 px-6 rounded-xl hover:from-blue-600 hover:to-purple-600 transform hover:scale-[1.02] transition-all duration-300 shadow-lg hover:shadow-xl flex items-center justify-center space-x-2">
            <span>🚀 Create Account</span>
        </button>

        <!-- Terms and Privacy -->
        <div class="text-center text-xs text-gray-500 dark:text-gray-400">
            By creating an account, you agree to our 
            <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Terms of Service</a>
            and 
            <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">Privacy Policy</a>
        </div>

        <!-- Login Link -->
        <div class="text-center pt-4 border-t border-gray-200 dark:border-gray-700">
            <p class="text-slate-600 dark:text-gray-400 mb-3">
                Already have an account?
            </p>
            <a href="{{ route('login') }}" 
               class="inline-flex items-center px-6 py-3 border-2 border-gray-600 dark:border-gray-400 text-slate-600 dark:text-gray-400 font-semibold rounded-xl hover:bg-gray-600 hover:text-white dark:hover:bg-gray-400 dark:hover:text-gray-900 transition-all duration-300 space-x-2">
                <span>👤 Sign In</span>
            </a>
        </div>
    </form>
</x-guest-layout>
