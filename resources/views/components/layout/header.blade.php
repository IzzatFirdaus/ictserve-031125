<header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 z-10">
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Hamburger Menu (Mobile) -->
                <div class="-ml-2 mr-2 flex items-center md:hidden">
                    <button @click="sidebarOpen = !sidebarOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary-500 min-h-11 min-w-11" aria-controls="mobile-menu" aria-expanded="false">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Global Search -->
            <div class="flex-1 flex items-center justify-center px-2 lg:ml-6 lg:justify-end">
                <livewire:global-search />
            </div>

            <div class="flex items-center">
                <!-- User Dropdown -->
                <div class="ml-3 relative">
                    <x-ui.dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button type="button" class="bg-white dark:bg-gray-800 flex text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 min-h-11 min-w-11 items-center gap-2" id="user-menu-button" aria-expanded="false" aria-haspopup="true">
                                <span class="sr-only">Open user menu</span>
                                @php
                                    // Determine user display name safely and compute an initial character.
                                    // This helper will recursively find a scalar string inside nested arrays/objects.
                                    $normalizeToString = function ($value) use (&$normalizeToString): string {
                                        if ($value === null) {
                                            return 'U';
                                        }

                                        if (is_string($value)) {
                                            return $value;
                                        }

                                        if (is_object($value)) {
                                            if (method_exists($value, '__toString')) {
                                                return (string) $value;
                                            }
                                            if (property_exists($value, 'name')) {
                                                return $normalizeToString($value->name);
                                            }
                                            return (string) $value;
                                        }

                                        if (is_array($value)) {
                                            // Prefer 'en' locale key
                                            if (array_key_exists('en', $value)) {
                                                return $normalizeToString($value['en']);
                                            }
                                            foreach ($value as $v) {
                                                $s = $normalizeToString($v);
                                                if ($s !== 'U' && $s !== '') {
                                                    return $s;
                                                }
                                            }
                                            return 'U';
                                        }

                                        return (string) $value;
                                    };

                                    $authUser = auth()->user();
                                    $displayName = 'Guest';
                                    if ($authUser !== null) {
                                        $displayName = $normalizeToString($authUser->name ?? ($authUser->username ?? null));
                                    }

                                    $safeDisplayName = (string) ($displayName ?? 'U');
                                    $initial = 'U';
                                    if (is_string($safeDisplayName) && $safeDisplayName !== '') {
                                        $initial = mb_substr($safeDisplayName, 0, 1);
                                    }
                                    // Local helper to guarantee we never pass an array to e() / htmlspecialchars
                                    if (! function_exists('blade_safe_string')) {
                                        function blade_safe_string($value) {
                                            if (is_array($value)) {
                                                if (array_key_exists('en', $value)) {
                                                    $v = $value['en'];
                                                    return is_array($v) ? (string)(array_values($v)[0] ?? '') : (string)$v;
                                                }
                                                foreach ($value as $v) {
                                                    if (! is_array($v)) return (string) $v;
                                                }
                                                return (string) reset($value);
                                            }
                                            if (is_object($value)) {
                                                if (method_exists($value, '__toString')) return (string) $value;
                                                if (property_exists($value, 'name')) return blade_safe_string($value->name);
                                                return (string) $value;
                                            }
                                            return (string) ($value ?? '');
                                        }
                                    }
                                    // Debug logging removed - normalization helper in use to ensure safe outputs
                                @endphp
                                <div class="h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-bold">
                                    {{ blade_safe_string($initial) }}
                                </div>
                                <span class="hidden md:block text-gray-700 dark:text-gray-300 font-medium">{{ blade_safe_string($safeDisplayName) }}</span>
                                <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-2 text-xs text-gray-500 dark:text-gray-400 border-b border-gray-100 dark:border-gray-600">
                                {{ blade_safe_string(__('Manage Account')) }}
                            </div>

                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600" role="menuitem">
                                {{ blade_safe_string(__('Profile')) }}
                            </a>

                            <div class="border-t border-gray-100 dark:border-gray-600"></div>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600" role="menuitem">
                                    {{ blade_safe_string(__('Log Out')) }}
                                </a>
                            </form>
                        </x-slot>
                    </x-ui.dropdown>
                </div>
            </div>
        </div>
    </div>
</header>
