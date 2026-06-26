<?php

namespace App\Domains\Billing\Services;

use App\Domains\Billing\Models\BillingAddress;
use App\Domains\Users\Models\User;

class BillingAddressService
{
    public function store(User $user, array $data): BillingAddress
    {
        if (!empty($data['is_default'])) {
            BillingAddress::where('user_id', $user->id)->update(['is_default' => false]);
        }

        return BillingAddress::create([
            'user_id'    => $user->id,
            'name'       => $data['name'],
            'line1'      => $data['line1'],
            'line2'      => $data['line2'] ?? null,
            'city'       => $data['city'],
            'country'    => $data['country'],
            'tax_id'     => $data['tax_id'] ?? null,
            'is_default' => !empty($data['is_default']),
        ]);
    }

    public function update(BillingAddress $address, array $data): BillingAddress
    {
        if (!empty($data['is_default'])) {
            BillingAddress::where('user_id', $address->user_id)
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update([
            'name'       => $data['name']       ?? $address->name,
            'line1'      => $data['line1']      ?? $address->line1,
            'line2'      => $data['line2']      ?? $address->line2,
            'city'       => $data['city']       ?? $address->city,
            'country'    => $data['country']    ?? $address->country,
            'tax_id'     => $data['tax_id']     ?? $address->tax_id,
            'is_default' => $data['is_default'] ?? $address->is_default,
        ]);

        return $address->fresh();
    }

    public function setDefault(BillingAddress $address): void
    {
        BillingAddress::where('user_id', $address->user_id)
            ->update(['is_default' => false]);

        $address->update(['is_default' => true]);
    }

    public function destroy(BillingAddress $address): void
    {
        $address->delete();
    }
}
