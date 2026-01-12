<div
    class="min-h-screen flex flex-col items-center justify-center space-y-4 sm:justify-center pt-6 sm:pt-0 bg-slate-800 px-4"
>
    <x-authentication-card-logo />
    <p class="text-slate-300 font-fantasque text-3xl pb-20">WalletSaga</p>

    <div class="w-full sm:max-w-md pt-6 px-6 py-4 bg-slate-900 shadow-md overflow-hidden rounded-2xl">
        {{ $slot }}
    </div>
</div>
