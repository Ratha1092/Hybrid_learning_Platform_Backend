<?php

namespace App\Filament\Pages;

use App\Domains\Courses\Models\Course;
use App\Domains\System\Models\Setting;
use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Http\Request;

class Settings extends Page
{
    protected string $view = 'filament.pages.settings';
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel               = 'Settings';
    protected static string|\UnitEnum|null $navigationGroup = 'System';
    protected static ?int    $navigationSort                = 1;
    protected static ?string $slug                          = 'settings';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return '';
    }

    public function saveCommission(Request $request): \Illuminate\Http\RedirectResponse
    {
        $value = $request->input('default_commission_percentage');

        if (!is_numeric($value) || $value < 0 || $value > 100) {
            return back()->with('error', 'Commission must be a number between 0 and 100.');
        }

        $value = round((float) $value, 2);

        Setting::set('default_commission_percentage', $value, 'finance');

        if ($request->boolean('apply_to_all_courses')) {
            Course::withoutGlobalScopes()->update(['commission_percentage' => $value]);
            $count = Course::withoutGlobalScopes()->count();
            return back()->with('success', "Commission updated to {$value}% and applied to all {$count} courses.");
        }

        return back()->with('success', "Default commission updated to {$value}%. New courses will use this rate.");
    }

    protected function getViewData(): array
    {
        $commissionValue = (float) Setting::get('default_commission_percentage', 20);

        $courseStats = [
            'total'   => Course::withoutGlobalScopes()->count(),
            'at_rate' => Course::withoutGlobalScopes()->where('commission_percentage', $commissionValue)->count(),
        ];

        return compact('commissionValue', 'courseStats');
    }
}
