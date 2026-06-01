<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crawler Management System - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="min-h-screen flex">
        <!-- Left Section: Branding -->
        <div
            class="hidden lg:flex lg:w-1/2 flex-flex-col justify-between p-8 xl:p-12 grid-background relative overflow-hidden">
            <!-- Subtle glow elements -->
            <div
                class="absolute top-20 left-10 w-72 h-72 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-5">
            </div>
            <div
                class="absolute bottom-20 right-10 w-72 h-72 bg-cyan-500 rounded-full mix-blend-multiply filter blur-3xl opacity-5">
            </div>

            <div class="relative z-10">
                <div class="inline-block mb-8">
                    <div class="flex items-center gap-3 mb-2">
                        <div
                            class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 6a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2zm0 6a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z">
                                </path>
                            </svg>
                        </div>
                        <span class="text-2xl font-bold text-blue-400">Crawler</span>
                    </div>
                </div>

                <h1 class="text-4xl xl:text-5xl font-bold mb-3 leading-tight">Crawler Management System</h1>
                <p class="text-lg text-muted-foreground mb-12 max-w-sm" style="color: var(--muted-foreground);">Monitor,
                    manage and automate large-scale crawling operations</p>

                <div class="space-y-4">
                    <div class="feature-item">
                        <span class="feature-check">✓</span>
                        <span>Task Monitoring</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-check">✓</span>
                        <span>Queue Management</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-check">✓</span>
                        <span>Real-time Logs</span>
                    </div>
                    <div class="feature-item">
                        <span class="feature-check">✓</span>
                        <span>Data Synchronization</span>
                    </div>
                </div>
            </div>

            <div class="relative z-10 text-sm" style="color: var(--muted-foreground);">
                <p>Enterprise-grade infrastructure for large-scale web data collection</p>
            </div>
        </div>

        <!-- Right Section: Login Card -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 md:p-8"
            style="background-color: var(--sidebar-background);">
            <div class="w-full max-w-md">
                <!-- Mobile branding -->
                <div class="lg:hidden mb-8 text-center">
                    <div class="flex items-center justify-center gap-2 mb-4">
                        <div
                            class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-cyan-400 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm0 6a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2zm0 6a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1v-2z">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold">Crawler System</h2>
                    <p class="text-sm mt-1" style="color: var(--muted-foreground);">Enterprise Management</p>
                </div>

                <!-- Login Card -->
                <div class="card-premium">
                    <h2 class="text-2xl font-bold mb-1">Welcome Back</h2>
                    <p class="text-sm mb-8" style="color: var(--muted-foreground);">Sign in to your crawling
                        infrastructure account</p>

                    <form x-data="loginForm()" @submit.prevent="submitForm" method="POST"
                        action="{{ route('login') }}">
                        @csrf

                        <!-- Email Field -->
                        <div class="form-group">
                            <label class="label-text" for="email">Email Address</label>
                            <input id="email" type="email" name="email" class="input-base w-full"
                                placeholder="admin@crawler.system" x-model="form.email" @focus="focused = 'email'"
                                @blur="focused = ''" required>
                            @error('email')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Password Field -->
                        <div class="form-group">
                            <div class="flex items-center justify-between mb-2">
                                <label class="label-text mb-0" for="password">Password</label>
                            </div>
                            <div class="relative">
                                <input id="password" :type="showPassword ? 'text' : 'password'" name="password"
                                    class="input-base w-full pr-10" placeholder="••••••••" x-model="form.password"
                                    @focus="focused = 'password'" @blur="focused = ''" required>
                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors"
                                    style="color: var(--muted-foreground);"
                                    :style="showPassword ? 'color: var(--primary);' : ''">
                                    <svg v-if="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                        </path>
                                    </svg>
                                    <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-4.803m5.596-3.856a3.375 3.375 0 11-4.753 4.753m4.753-4.753L3.596 3.039m10.318 10.318L21 21">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                            @error('password')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="flex items-center justify-between mb-6">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="remember" x-model="form.remember"
                                    class="w-4 h-4 rounded"
                                    style="background-color: var(--input); border: 1px solid var(--border); accent-color: var(--primary);">
                                <span class="text-sm" style="color: var(--muted-foreground);">Remember me</span>
                            </label>
                            {{-- <a href="{{ route('password.request') }}" class="btn-link">Forgot password?</a> --}}
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-primary" :disabled="isLoading">
                            <span x-show="!isLoading">Sign In</span>
                            <div x-show="isLoading" class="flex items-center justify-center gap-2">
                                <div class="spinner"></div>
                                <span>Signing in...</span>
                            </div>
                        </button>

                        <!-- Session Error Alert -->
                        @if (session('error'))
                            <div class="mt-4 p-3 rounded-md"
                                style="background-color: rgba(239, 67, 67, 0.1); border: 1px solid var(--destructive);">
                                <p class="text-sm" style="color: var(--destructive);">{{ session('error') }}</p>
                            </div>
                        @endif

                        <!-- Alpine Error Alert -->
                        <div x-show="error" class="mt-4 p-3 rounded-md"
                            style="background-color: rgba(239, 67, 67, 0.1); border: 1px solid var(--destructive);">
                            <p class="text-sm" style="color: var(--destructive);" x-text="error"></p>
                        </div>
                    </form>

                    <!-- Footer -->
                    <div class="mt-8 pt-8 border-t" style="border-color: var(--border);">
                        <p class="text-center text-sm" style="color: var(--muted-foreground);">
                            Enterprise access only • Secure infrastructure
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function loginForm() {
            return {
                form: {
                    email: '',
                    password: '',
                    remember: false,
                },
                showPassword: false,
                isLoading: false,
                error: '',
                focused: '',

                async submitForm() {
                    if (!this.form.email || !this.form.password) {
                        this.error = 'Please fill in all fields';
                        return;
                    }

                    this.isLoading = true;
                    this.error = '';

                    try {
                        // Submit the form natively — Laravel handles validation & errors
                        this.$el.submit();
                    } catch (err) {
                        this.error = 'An error occurred. Please try again.';
                        this.isLoading = false;
                    }
                },
            }
        }
    </script>
</body>

</html>
