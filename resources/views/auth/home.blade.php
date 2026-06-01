<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Restricted - Crawler Management System</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-zinc-950 text-zinc-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-zinc-900 border border-zinc-800 rounded-2xl p-8 text-center">
        <div class="w-16 h-16 bg-amber-500/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
            <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
        </div>

        <h1 class="text-2xl font-bold mb-2">Access Restricted</h1>
        <p class="text-zinc-400 mb-2">
            Your account (<strong class="text-zinc-200">{{ Auth::user()->email }}</strong>)
            has the role <strong class="text-zinc-200">{{ Auth::user()->roleLabel() }}</strong>.
        </p>
        <p class="text-zinc-500 text-sm mb-8">
            You do not have permission to access the admin dashboard.
            Please contact an administrator to upgrade your account.
        </p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full px-6 py-3 bg-zinc-800 hover:bg-zinc-700 text-zinc-200 rounded-xl font-medium transition-colors cursor-pointer">
                Sign out & switch account
            </button>
        </form>
    </div>
</body>

</html>
