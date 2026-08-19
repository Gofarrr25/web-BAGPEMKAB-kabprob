<li>
    <div class="flex flex-col items-center group relative z-20">
        @if($member->type == 'functional_group')
            <!-- Functional Group Node -->
            <div class="bg-white/80 backdrop-blur-sm border-2 border-gray-400 border-dashed shadow-md p-3 w-32 sm:w-40 flex items-center justify-center rounded-lg relative">
                <span class="text-[10px] sm:text-xs font-bold text-gray-700 text-center uppercase tracking-wide leading-tight">{{ $member->position }}</span>
            </div>
        @else
            <!-- Person Node -->
            @php
                // Tentukan ukuran dan style berdasarkan level kedalaman
                if ($level == 1) {
                    $imgSize = "w-28 h-28 sm:w-36 sm:h-36";
                    $borderColor = "border-blue-600";
                    $borderWidth = "border-4";
                    $boxWidth = "";
                    $nameSize = "text-base sm:text-lg";
                    $titleSize = "text-xs sm:text-sm py-2 px-6";
                    $titleBg = "bg-[#1e3a5f]";
                } elseif ($level == 2) {
                    $imgSize = "w-20 h-20 sm:w-28 sm:h-28";
                    $borderColor = "border-blue-600";
                    $borderWidth = "border-4";
                    $boxWidth = "w-40 sm:w-56";
                    $nameSize = "text-sm sm:text-base";
                    $titleSize = "text-[10px] sm:text-xs py-1.5 px-3";
                    $titleBg = "bg-[#1e3a5f]";
                } else {
                    $imgSize = "w-16 h-16 sm:w-24 sm:h-24";
                    $borderColor = "border-emerald-500";
                    $borderWidth = "border-[3px]";
                    $boxWidth = "w-32 sm:w-48";
                    $nameSize = "text-xs sm:text-sm";
                    $titleSize = "text-[9px] sm:text-[10px] py-1 px-2";
                    $titleBg = "bg-[#2c5282]";
                }
            @endphp
            
            @if($member->image)
            <div class="{{ $imgSize }} rounded-full {{ $borderWidth }} {{ $borderColor }} bg-white overflow-hidden p-1 shadow-md mb-3 transition-transform duration-300 group-hover:scale-105">
                <img src="{{ asset('storage/' . $member->image) }}" class="w-full h-full object-cover rounded-full bg-gray-200" alt="{{ $member->name }}">
            </div>
            @endif

            <div class="text-center {{ $boxWidth }} bg-white/70 px-2 py-2 rounded-lg backdrop-blur-sm">
                @if($member->name)
                    <h4 class="font-extrabold text-gray-900 {{ $nameSize }} leading-tight mb-2">{{ $member->name }}</h4>
                @endif
                <div class="{{ $titleBg }} text-white {{ $titleSize }} font-bold uppercase rounded shadow-md inline-block">
                    {{ $member->position }}
                </div>
            </div>
        @endif
    </div>

    @if($member->children->count() > 0)
        <ul>
            @foreach($member->children as $child)
                @if($child->is_active)
                    @include('frontend.partials.org_node', ['member' => $child, 'level' => $level + 1])
                @endif
            @endforeach
        </ul>
    @endif
</li>
