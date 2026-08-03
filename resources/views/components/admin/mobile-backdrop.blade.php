<div
    x-cloak
    x-show="sidebarOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-30 bg-gray-900/50 backdrop-blur-[2px] lg:hidden"
    @click="closeSidebar()"
    aria-hidden="true"
></div>
