<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-purple-600 to-indigo-600 shadow-lg">
    <!-- Animated gradient overlay -->
    <div class="absolute inset-0 animate-gradient opacity-60 pointer-events-none z-0"></div>
    <!-- Optional radial overlays -->
    <div class="absolute inset-0 opacity-10 pointer-events-none z-10"
        style="background-image: radial-gradient(ellipse at top left, rgba(255,255,255,.35), transparent 40%), radial-gradient(ellipse at bottom right, rgba(0,0,0,.25), transparent 40%);">
    </div>
    <div class="relative p-6 sm:p-8 z-20">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight flex items-center">
                    @if(isset($icon))
                        <i class="{{ $icon }} text-white/90 mr-3"></i>
                    @endif
                    {{ $title }}
                </h1>
                @if(isset($subtitle))
                    <p class="mt-1 text-white/80 text-sm">{{ $subtitle }}</p>
                @endif
            </div>
            <div class="flex items-center gap-2">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
