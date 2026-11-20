<x-layouts::auth :title="__('Register')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                :label="__('Name')"
                :value="old('name')"
                type="text"
                required
                autofocus
                autocomplete="name"
                :placeholder="__('Full name')"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                :label="__('Email address')"
                :value="old('email')"
                type="email"
                required
                autocomplete="email"
                placeholder="email@example.com"
            />

            <!-- Phone -->
            <flux:input
                name="phone"
                :label="__('Phone (optional)')"
                :value="old('phone')"
                type="tel"
                autocomplete="tel"
                placeholder="012-3456789"
            />

            <!-- Student or external: Alpine shows the student ID field only for students -->
            @php($studentChecked = old('email') === null ? true : (bool) old('is_student'))
            <div x-data="{ isStudent: @js($studentChecked) }" class="flex flex-col gap-4">
                <label class="flex items-center gap-2 text-sm font-medium text-zinc-800 dark:text-white">
                    <input type="checkbox" name="is_student" value="1" x-model="isStudent"
                        class="size-4 rounded border-zinc-300 accent-zinc-800 dark:accent-white" data-test="is-student-checkbox">
                    {{ __('I am a GMI student (bookings are free)') }}
                </label>

                <div x-show="isStudent" x-transition>
                    <flux:input
                        name="student_id"
                        :label="__('Student ID')"
                        :value="old('student_id')"
                        placeholder="GMI2xxxxx"
                    />
                </div>

                <flux:text x-show="!isStudent" x-cloak class="text-sm">
                    {{ __('Alumni and members of the public pay a fee per slot. You will see it before you book.') }}
                </flux:text>
            </div>

            <!-- Password -->
            <flux:input
                name="password"
                :label="__('Password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                :label="__('Confirm password')"
                type="password"
                required
                autocomplete="new-password"
                :placeholder="__('Confirm password')"
                passwordrules="{{ \Illuminate\Validation\Rules\Password::defaults()->toPasswordRulesString() }}"
                viewable
            />

            <div class="flex items-center justify-end">
                <flux:button type="submit" variant="primary" class="w-full" data-test="register-user-button">
                    {{ __('Create account') }}
                </flux:button>
            </div>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
            <span>{{ __('Already have an account?') }}</span>
            <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
        </div>
    </div>
</x-layouts::auth>
