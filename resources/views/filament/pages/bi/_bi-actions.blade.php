{{--
    Shared BI page toolbar: Export CSV button + date-range filter.
    Include with: @include('filament.pages.bi._bi-actions', ['biKey' => 'stu'])
    $biKey must be a short unique suffix per page (used for wire:key).
--}}
<div class="bi-actions">
    <button type="button" class="bi-export-btn" wire:click="exportCsv" wire:loading.attr="disabled" wire:target="exportCsv">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
        Export CSV
    </button>
    <a href="{{ route('admin.bi.pdf', array_filter(['type' => $biKey, 'preset' => $activePreset, 'date_from' => $activePreset === 'custom' ? $activeDateFrom : null, 'date_to' => $activePreset === 'custom' ? $activeDateTo : null])) }}"
       class="bi-export-btn" target="_blank">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9z"/></svg>
        Export PDF
    </a>
    <div class="rp-drp-wrap" wire:key="bi-drp-{{ $biKey }}"
        x-data="rpDate({preset:'{{ $activePreset }}',from:'{{ $activeDateFrom }}',to:'{{ $activeDateTo }}',extras:{}})"
        data-rp-preset="{{ $activePreset }}" data-rp-from="{{ $activeDateFrom }}" data-rp-to="{{ $activeDateTo }}"
        @click.outside="open=false">
        <div class="rp-drp-pill" @click="open=!open">
            <span class="rp-drp-icon"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span>
            <span class="rp-drp-from" x-text="pFrom"></span><span class="rp-drp-sep">|</span><span class="rp-drp-to" x-text="pTo"></span>
            <span class="rp-drp-chev" :class="{open}"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg></span>
        </div>
        <div class="rp-drp-panel" :class="{open}">
            <div class="rp-drp-presets">
                @foreach(\App\Support\Concerns\HasDateRangePresets::dateRangePresetOptions() as $key => $label)
                    @if($key !== 'custom')<button type="button" class="rp-drp-pre" :class="{active:preset==='{{ $key }}'}" @click="pick('{{ $key }}')">{{ $label }}</button>@endif
                @endforeach
            </div>
            <hr class="rp-drp-hr">
            <div class="rp-drp-cus-lbl">Custom Range</div>
            <div class="rp-drp-cus-row">
                <input type="date" class="rp-drp-dt" x-model="from" @change="preset='custom'">
                <span class="rp-drp-dt-sep">—</span>
                <input type="date" class="rp-drp-dt" x-model="to" @change="preset='custom'">
            </div>
            <div class="rp-drp-btns">
                <button type="button" class="rp-drp-apply" @click="apply()">Apply</button>
                <button type="button" class="rp-drp-reset" @click="reset()">Reset</button>
            </div>
        </div>
    </div>
</div>
