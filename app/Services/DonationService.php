<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\DonationItem;
use Illuminate\Support\Facades\DB;

class DonationService
{
    public function calculateItemsTotal(array $items): float
    {
        $total = 0;

        foreach ($items as $item) {
            $quantity = (float) ($item['quantity'] ?? 0);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $total += $quantity * $unitPrice;
        }

        return round($total, 2);
    }

    public function createDonationWithItems(array $data): Donation
    {
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];

            $total = $this->calculateItemsTotal($items);

            $donationData = $data;
            $donationData['total_amount'] = $total;

            /** @var \App\Models\Donation $donation */
            $donation = Donation::create($donationData);

            foreach ($items as $item) {
                $quantity = (float) ($item['quantity'] ?? 0);
                $unitPrice = (float) ($item['unit_price'] ?? 0);

                DonationItem::create([
                    'donation_id' => $donation->id,
                    'item_name'   => $item['item_name'] ?? '',
                    'quantity'    => $quantity,
                    'unit_price'  => $unitPrice,
                    'total_price' => $quantity * $unitPrice,
                ]);
            }

            $donation->load(['donator', 'project', 'items']);

            return $donation;
        });
    }
}

