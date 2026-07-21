
@php
    use App\Helpers\MenuHelper;
    $menuGroups = MenuHelper::getMenuGroups();

    // Get current path
    $currentPath = request()->path();
@endphp

<aside id="sidebar"
    class="fixed flex flex-col mt-0 top-0 px-5 left-0 bg-[#006d5b] text-white h-screen transition-all duration-300 ease-in-out z-99999"
    x-data="{
        openSubmenus: {},
        init() {
            // Auto-open Dashboard menu on page load
            this.initializeActiveMenus();
        },
        initializeActiveMenus() {
            const currentPath = '{{ $currentPath }}';

            @foreach ($menuGroups as $groupIndex => $menuGroup)
                @foreach ($menuGroup['items'] as $itemIndex => $item)
                    @if (isset($item['subItems']))
                        // Check if any submenu item matches current path
                        @foreach ($item['subItems'] as $subItem)
                            if (currentPath === '{{ ltrim($subItem['path'], '/') }}' ||
                                window.location.pathname === '{{ $subItem['path'] }}') {
                                this.openSubmenus['{{ $groupIndex }}-{{ $itemIndex }}'] = true;
                            } @endforeach
            @endif
            @endforeach
            @endforeach
        },
        toggleSubmenu(groupIndex, itemIndex) {
            const key = groupIndex + '-' + itemIndex;
            const newState = !this.openSubmenus[key];

            // Close all other submenus when opening a new one
            if (newState) {
                this.openSubmenus = {};
            }

            this.openSubmenus[key] = newState;
        },
        isSubmenuOpen(groupIndex, itemIndex) {
            const key = groupIndex + '-' + itemIndex;
            return this.openSubmenus[key] || false;
        },
        isActive(path) {
            return window.location.pathname === path || '{{ $currentPath }}' === path.replace(/^\//, '');
        }
    }"
    :class="{
        'w-[290px]': $store.sidebar.isExpanded || $store.sidebar.isMobileOpen || $store.sidebar.isHovered,
        'w-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
        'translate-x-0': $store.sidebar.isMobileOpen,
        '-translate-x-full xl:translate-x-0': !$store.sidebar.isMobileOpen
    }"
    @mouseenter="if (!$store.sidebar.isExpanded) $store.sidebar.setHovered(true)"
    @mouseleave="$store.sidebar.setHovered(false)">
    <!-- Logo Section -->
    <div class="pt-8 pb-7 flex items-center"
        :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
        'xl:justify-center px-0' :
        'justify-between px-2'">
        <a href="/" class="flex items-center gap-3 min-w-0">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-white/10 text-white shrink-0">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A5 5 0 0 0 8 8c0 1 .3 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5"/><path d="M9 18h6"/><path d="M10 22h4"/></svg>
            </div>
            <span x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen" 
                  class="text-xl font-bold text-white tracking-wide font-outfit truncate">
                SIDERAL
            </span>
        </a>
        
        <!-- Desktop Minimize Button inside Sidebar -->
        <button
            x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
            @click="$store.sidebar.toggleExpanded()"
            class="hidden xl:flex items-center justify-center w-8 h-8 text-white/70 hover:text-white bg-white/10 hover:bg-white/20 rounded-lg transition-colors cursor-pointer shrink-0"
            aria-label="Minimize Sidebar">
            <!-- Left Chevron/Arrow if expanded, Right if collapsed -->
            <svg x-show="$store.sidebar.isExpanded" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
            <svg x-show="!$store.sidebar.isExpanded" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>

    <!-- Navigation Menu Container -->
    <div class="flex-1 flex flex-col overflow-y-auto duration-300 ease-linear no-scrollbar">
        <nav class="mb-6">
            <div class="flex flex-col gap-4">
                @foreach ($menuGroups as $groupIndex => $menuGroup)
                    <div>
                        <!-- Menu Group Title -->
                        <h2 class="mb-4 text-xs uppercase flex leading-[20px] text-gray-400"
                            :class="(!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                            'lg:justify-center' : 'justify-start'">
                            <template
                                x-if="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                                <span>{{ $menuGroup['title'] }}</span>
                            </template>
                            <template x-if="!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen">
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                  <path fill-rule="evenodd" clip-rule="evenodd" d="M5.99915 10.2451C6.96564 10.2451 7.74915 11.0286 7.74915 11.9951V12.0051C7.74915 12.9716 6.96564 13.7551 5.99915 13.7551C5.03265 13.7551 4.24915 12.9716 4.24915 12.0051V11.9951C4.24915 11.0286 5.03265 10.2451 5.99915 10.2451ZM17.9991 10.2451C18.9656 10.2451 19.7491 11.0286 19.7491 11.9951V12.0051C19.7491 12.9716 18.9656 13.7551 17.9991 13.7551C17.0326 13.7551 16.2491 12.9716 16.2491 12.0051V11.9951C16.2491 11.0286 17.0326 10.2451 17.9991 10.2451ZM13.7491 11.9951C13.7491 11.0286 12.9656 10.2451 11.9991 10.2451C11.0326 10.2451 10.2491 11.0286 10.2491 11.9951V12.0051C10.2491 12.9716 11.0326 13.7551 11.9991 13.7551C12.9656 13.7551 13.7491 12.9716 13.7491 12.0051V11.9951Z" fill="currentColor"/>
                                </svg>
                            </template>
                        </h2>

                        <!-- Menu Items -->
                        <ul class="flex flex-col gap-1">
                            @foreach ($menuGroup['items'] as $itemIndex => $item)
                                <li>
                                    @if (isset($item['subItems']))
                                        <!-- Menu Item with Submenu -->
                                        <button @click="toggleSubmenu({{ $groupIndex }}, {{ $itemIndex }})"
                                            class="menu-item group w-full"
                                            :class="[
                                                isSubmenuOpen({{ $groupIndex }}, {{ $itemIndex }}) ?
                                                'menu-item-active' : 'menu-item-inactive',
                                                !$store.sidebar.isExpanded && !$store.sidebar.isHovered ?
                                                'xl:justify-center' : 'xl:justify-start'
                                            ]">

                                            <!-- Icon -->
                                            <span :class="isSubmenuOpen({{ $groupIndex }}, {{ $itemIndex }}) ?
                                                    'menu-item-icon-active' : 'menu-item-icon-inactive'">
                                                {!! MenuHelper::getIconSvg($item['icon']) !!}
                                            </span>

                                            <!-- Text -->
                                            <span
                                                x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                                class="menu-item-text flex items-center gap-2">
                                                {{ $item['name'] }}
                                            </span>
                                        </button>
                                    @else
                                        <!-- Simple Menu Item -->
                                        <a href="{{ $item['path'] }}" class="menu-item group"
                                            :class="[
                                                isActive('{{ $item['path'] }}') ? 'menu-item-active' :
                                                'menu-item-inactive',
                                                (!$store.sidebar.isExpanded && !$store.sidebar.isHovered && !$store.sidebar.isMobileOpen) ?
                                                'xl:justify-center' :
                                                'justify-start'
                                            ]">

                                            <!-- Icon -->
                                            <span
                                                :class="isActive('{{ $item['path'] }}') ? 'menu-item-icon-active' :
                                                    'menu-item-icon-inactive'">
                                                {!! MenuHelper::getIconSvg($item['icon']) !!}
                                            </span>

                                            <!-- Text -->
                                            <span
                                                x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen"
                                                class="menu-item-text flex items-center gap-2">
                                                {{ $item['name'] }}
                                            </span>
                                        </a>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </nav>
    </div>

    <!-- Logged In User Profile & Red Logout Button (At Very Bottom) -->
    @auth
    <div class="mt-auto border-t border-white/10 pt-4 pb-6 px-1">
        <div class="flex items-center justify-between gap-2">
            <!-- User Profile Details -->
            <div class="flex items-center gap-3 overflow-hidden" x-show="$store.sidebar.isExpanded || $store.sidebar.isHovered || $store.sidebar.isMobileOpen">
                <div class="flex h-9 w-9 items-center justify-center rounded-full text-white font-bold text-sm shrink-0 {{ auth()->user()->isTeknisi() ? 'bg-blue-600' : 'bg-emerald-600' }}">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="truncate">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <span class="inline-block px-2 py-0.5 text-[10px] font-bold uppercase rounded tracking-wider {{ auth()->user()->isTeknisi() ? 'bg-blue-900/80 text-blue-200 border border-blue-700/60' : 'bg-emerald-900/80 text-emerald-200 border border-emerald-700/60' }}">
                        {{ auth()->user()->role }}
                    </span>
                </div>
            </div>

            <!-- Red Theme Logout Button -->
            <form method="POST" action="{{ route('logout') }}" class="inline shrink-0">
                @csrf
                <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-lg bg-red-500/20 text-red-300 hover:bg-red-600 hover:text-white border border-red-500/30 transition shadow-sm cursor-pointer" title="Keluar / Logout">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                </button>
            </form>
        </div>
    </div>
    @endauth
</aside>

<!-- Mobile Overlay -->
<div x-show="$store.sidebar.isMobileOpen" @click="$store.sidebar.setMobileOpen(false)"
    class="fixed z-50 h-screen w-full bg-gray-900/50"></div>
