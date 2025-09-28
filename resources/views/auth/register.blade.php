@extends('layouts.app')

@section('section')
<main ">
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <img src="{{ asset('logo-dark.png') }}" alt="Horizon" style="height: 9.5rem;" class="mx-auto h-40 w-auto" />
            <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-white" style="color: #c03101;">أنشئ حسابك
        </h2>
    </div>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm/6 font-medium text-gray-100" style="color: #c03101;">الاسم<span style="color: red;">*</span></label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" required
                    class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-gray outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm/6 font-medium text-gray-100" style="color: #c03101;">البريد الإلكتروني<span style="color: red;">*</span></label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                    class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-gray outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-sm/6 font-medium text-gray-100" style="color: #c03101;">كلمة المرور<span style="color: red;">*</span></label>
                <input id="password" type="password" name="password" required autocomplete="new-password"
                    class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-gray outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm/6 font-medium text-gray-100" style="color: #c03101;">تأكيد كلمة المرور<span style="color: red;">*</span></label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    class="block w-full rounded-md bg-white/5 px-3 py-1.5 text-base text-gray outline-1 -outline-offset-1 outline-white/10 placeholder:text-gray-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>

            <div class="flex items-center justify-between">
                <a class="underline text-sm text-indigo-400 hover:text-indigo-300" href="/user-dashboard/login">
                    مسجل بالفعل؟
                </a>

                <button type="submit" class="flex justify-center rounded-md bg-indigo-500 px-3 py-1.5 text-sm/6 font-semibold text-white hover:bg-indigo-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                    التسجيل
                </button>
            </div>
        </form>
    </div>
</div>
</main>

@endsection