<x-guest-layout>
    <div class="min-h-screen flex">
        <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
            <div class="mx-auto w-full max-w-sm lg:w-96">
                <div>
                    <img class="h-12 w-auto" src="/img/Atlas-Logo-Color.png" alt="Epics Atlas">
                    <h2 class="mt-8 text-2xl font-bold tracking-tight text-gray-900">Sign in to your account</h2>
                </div>

                <div class="mt-10">
                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf
                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-900">Email address</label>
                            <div class="mt-2">
                                <input id="email" name="email" type="email" autocomplete="email" required
                                    class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:outline-brand-600">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-900">Password</label>
                            <div class="mt-2">
                                <input id="password" name="password" type="password" autocomplete="current-password" required
                                    class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:outline-brand-600">
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input id="remember_me" name="remember" type="checkbox"
                                    class="rounded border-gray-300 text-brand-600 shadow-sm focus:ring-brand-500">
                                <label for="remember_me" class="ml-2 block text-sm text-gray-900">Remember me</label>
                            </div>

                            <div class="text-sm">
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="font-semibold text-brand-600 hover:text-brand-500">Forgot password?</a>
                                @endif
                            </div>
                        </div>

                        <!-- Login Button -->
                        <div>
                            <button type="submit"
                                class="flex w-full justify-center rounded-md bg-brand-600 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-brand-500 focus:outline focus:outline-2 focus:outline-brand-600">
                                Sign in
                            </button>
                        </div>
                    </form>

                    <!-- Social Login -->
                    <div class="mt-10">
                        <div class="relative">
                            <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                <div class="w-full border-t border-gray-200"></div>
                            </div>
                            <div class="relative flex justify-center text-sm font-medium">
                                <span class="bg-white px-6 text-gray-900">Or</span>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-4">
                            <a href="{{ route('auth.google') }}"
                                class="flex w-full items-center justify-center gap-3 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-gray-300 hover:bg-gray-50">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" aria-hidden="true">
  <path d="M12.0003 4.75C13.7703 4.75 15.3553 5.36002 16.6053 6.54998L20.0303 3.125C17.9502 1.19 15.2353 0 12.0003 0C7.31028 0 3.25527 2.69 1.28027 6.60998L5.27028 9.70498C6.21525 6.86002 8.87028 4.75 12.0003 4.75Z" fill="#EA4335"></path>
  <path d="M23.49 12.275C23.49 11.49 23.415 10.73 23.3 10H12V14.51H18.47C18.18 15.99 17.34 17.25 16.08 18.1L19.945 21.1C22.2 19.01 23.49 15.92 23.49 12.275Z" fill="#4285F4"></path>
  <path d="M5.26498 14.2949C5.02498 13.5699 4.88501 12.7999 4.88501 11.9999C4.88501 11.1999 5.01998 10.4299 5.26498 9.7049L1.275 6.60986C0.46 8.22986 0 10.0599 0 11.9999C0 13.9399 0.46 15.7699 1.28 17.3899L5.26498 14.2949Z" fill="#FBBC05"></path>
  <path d="M12.0004 24.0001C15.2404 24.0001 17.9654 22.935 19.9454 21.095L16.0804 18.095C15.0054 18.82 13.6204 19.245 12.0004 19.245C8.8704 19.245 6.21537 17.135 5.2654 14.29L1.27539 17.385C3.25539 21.31 7.3104 24.0001 12.0004 24.0001Z" fill="#34A853"></path>
</svg>
                                <span>Login with Google</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="relative hidden w-0 flex-1 lg:block">
            <img class="absolute inset-0 w-full h-full object-cover"
                src="img/account-screen-banner.jpg"
                alt="Background Image">
        </div>
    </div>
</x-guest-layout>
