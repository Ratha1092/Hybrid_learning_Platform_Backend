<div style="
    display: grid;
    grid-template-columns: repeat(5, 1fr);
    gap: 1rem;
    width: 100%;
">
    @foreach ($stats as $stat)
    <div style="
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.07);
        border-radius: 0.875rem;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        overflow: hidden;
        position: relative;
    ">
        {{-- Icon badge + Label --}}
        <div style="display:flex;align-items:center;gap:0.625rem;margin-bottom:0.25rem">
            <div style="
                width: 2.25rem;
                height: 2.25rem;
                border-radius: 0.5rem;
                background: {{ $stat['iconBg'] }};
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                color: {{ $stat['iconColor'] }};
            ">
                @svg($stat['icon'], ['style' => 'width:1.125rem;height:1.125rem'])
            </div>
            <span style="font-size:0.8rem;font-weight:500;color:rgb(156,163,175);letter-spacing:0.01em">
                {{ $stat['label'] }}
            </span>
        </div>

        {{-- Big number --}}
        <div style="font-size:2rem;font-weight:700;color:white;line-height:1.1;letter-spacing:-0.02em">
            {{ number_format($stat['value']) }}
        </div>

        {{-- Description --}}
        <div style="font-size:0.75rem;font-weight:500;color:{{ $stat['descColor'] }};display:flex;align-items:center;gap:0.25rem">
            @if ($stat['chartColor'] === '#a78bfa')
                <svg style="width:0.75rem;height:0.75rem;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            @elseif ($stat['chartColor'] === '#34d399')
                <svg style="width:0.75rem;height:0.75rem;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                </svg>
            @elseif ($stat['chartColor'] === '#fbbf24')
                <svg style="width:0.75rem;height:0.75rem;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            @else
                <svg style="width:0.75rem;height:0.75rem;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            @endif
            {{ $stat['description'] }}
        </div>

        {{-- Sparkline SVG --}}
        <div style="margin-top:0.5rem;margin-left:-1.25rem;margin-right:-1.25rem;margin-bottom:-1.25rem">
            <svg
                viewBox="0 0 200 48"
                preserveAspectRatio="none"
                style="width:100%;height:56px;display:block"
            >
                <defs>
                    <linearGradient id="grad-{{ $loop->index }}" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0%" stop-color="{{ $stat['chartColor'] }}" stop-opacity="0.25"/>
                        <stop offset="100%" stop-color="{{ $stat['chartColor'] }}" stop-opacity="0"/>
                    </linearGradient>
                </defs>
                {{-- Fill area --}}
                <polygon
                    points="{{ $stat['sparkline'] }} 200,48 0,48"
                    fill="url(#grad-{{ $loop->index }})"
                />
                {{-- Line --}}
                <polyline
                    points="{{ $stat['sparkline'] }}"
                    fill="none"
                    stroke="{{ $stat['chartColor'] }}"
                    stroke-width="1.75"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        </div>
    </div>
    @endforeach
</div>
