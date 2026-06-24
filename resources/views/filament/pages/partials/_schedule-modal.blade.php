{{-- Shared "schedule this report" modal. Pairs with
     App\Domains\Reports\Concerns\HasScheduleAction. Expects $reportLabel. --}}
<style>
    .hl-schedule-modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .55);
        z-index: 9998;
        display: flex;
        align-items: center;
        justify-content: center;
        --sm-panel: #1e293b;
        --sm-panel-2: #263245;
        --sm-border: rgba(255, 255, 255, .13);
        --sm-text: #e2e8f0;
        --sm-muted: #94a3b8;
    }
    html:not(.dark) .hl-schedule-modal-backdrop {
        --sm-panel: #ffffff;
        --sm-panel-2: #f8fafc;
        --sm-border: rgba(15, 23, 42, .14);
        --sm-text: #0f172a;
        --sm-muted: #64748b;
    }
</style>
<div
    class="hl-schedule-modal-backdrop"
    x-data="{ open: false }"
    x-on:open-schedule-modal.window="open = true"
    x-show="open"
    x-cloak
    x-on:click="if ($event.target === $el) open = false"
>
    <div style="background:var(--sm-panel);border:1px solid var(--sm-border);border-radius:16px;padding:28px;width:100%;max-width:420px;">
        <h3 style="font-size:16px;font-weight:750;color:var(--sm-text);margin-bottom:4px;">Schedule {{ $reportLabel }} Report</h3>
        <p style="font-size:12px;color:var(--sm-muted);margin-bottom:16px;">Sent automatically by email on the cadence below, using your current filters.</p>

        <div style="display:flex;flex-direction:column;gap:12px;">
            <label style="font-size:12px;color:var(--sm-muted);">Frequency
                <select wire:model="scheduleFrequency" style="width:100%;margin-top:4px;padding:8px;border-radius:8px;border:1px solid var(--sm-border);background:var(--sm-panel-2);color:var(--sm-text);">
                    <option value="daily">Daily</option>
                    <option value="weekly">Weekly</option>
                    <option value="monthly">Monthly</option>
                    <option value="quarterly">Quarterly</option>
                </select>
            </label>
            <label style="font-size:12px;color:var(--sm-muted);">Format
                <select wire:model="scheduleFormat" style="width:100%;margin-top:4px;padding:8px;border-radius:8px;border:1px solid var(--sm-border);background:var(--sm-panel-2);color:var(--sm-text);">
                    <option value="csv">CSV</option>
                    <option value="pdf">PDF</option>
                </select>
            </label>
            <label style="font-size:12px;color:var(--sm-muted);">Recipient Emails (comma-separated)
                <input wire:model="scheduleEmails" type="text" placeholder="finance@example.com" style="width:100%;margin-top:4px;padding:8px;border-radius:8px;border:1px solid var(--sm-border);background:var(--sm-panel-2);color:var(--sm-text);">
            </label>
        </div>

        <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:18px;">
            <button type="button" x-on:click="open = false" style="padding:8px 14px;border-radius:8px;font-size:12px;font-weight:700;border:1px solid var(--sm-border);background:var(--sm-panel-2);color:var(--sm-text);cursor:pointer;">Cancel</button>
            <button type="button" wire:click="scheduleReport" x-on:click="open = false" style="padding:8px 14px;border-radius:8px;font-size:12px;font-weight:700;border:none;background:#D7A441;color:#fff;cursor:pointer;">Schedule</button>
        </div>
    </div>
</div>
