<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;

if (! function_exists('usd_to_sp_rate')) {
    function usd_to_sp_rate(): float
    {
        try {
            if (Schema::hasTable('settings')) {
                return (float) Setting::get('usd_to_sp_rate', 135);
            }
        } catch (\Throwable) {
            // Fall through to default during install/migrate.
        }

        return 135.0;
    }
}

if (! function_exists('format_price')) {
    /**
     * Format a USD amount from the database for display (emails default to SP).
     */
    function format_price(float|int|string|null $amount, ?string $currency = null): string
    {
        $usd = (float) $amount;
        $rate = usd_to_sp_rate();

        if ($currency === 'usd') {
            return '$'.number_format($usd, 2, '.', ',');
        }

        return number_format($usd * $rate, 0, '.', ',').' SP';
    }
}
