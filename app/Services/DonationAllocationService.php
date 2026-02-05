<?php

namespace App\Services;

use App\Models\BeneficiaryOrder;
use App\Models\Donation;
use App\Models\DonationAllocation;
use App\Models\DonationAllocationItem;
use App\Models\DonationItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DonationAllocationService
{
    public function allocate(int $donationId, int $beneficiaryOrderId, float $amount, ?int $itemIndex = null, ?float $itemQuantity = null): DonationAllocation
    {
        return DB::transaction(function () use ($donationId, $beneficiaryOrderId, $amount, $itemIndex, $itemQuantity) {
            /** @var \App\Models\Donation $donation */
            $donation = Donation::with('items')->lockForUpdate()->findOrFail($donationId);

            /** @var \App\Models\BeneficiaryOrder $order */
            $order = BeneficiaryOrder::findOrFail($beneficiaryOrderId);

            if ($amount <= 0) {
                throw ValidationException::withMessages([
                    'allocated_amount' => trans('validation.min.numeric', ['attribute' => 'allocated_amount', 'min' => 0.01]),
                ]);
            }

            if ($amount > (float) $donation->remaining_amount) {
                throw ValidationException::withMessages([
                    'allocated_amount' => trans('validation.max.numeric', ['attribute' => 'allocated_amount', 'max' => $donation->remaining_amount]),
                ]);
            }

            // If this is an item allocation, validate item quantity
            if ($donation->donation_type === Donation::TYPE_ITEMS && $itemIndex !== null && $itemQuantity !== null) {
                $items = $donation->items;
                if (!isset($items[$itemIndex])) {
                    throw ValidationException::withMessages([
                        'allocation_item' => 'Invalid item selected.',
                    ]);
                }

                $item = $items[$itemIndex];
                $remainingQuantity = $this->getRemainingItemQuantity($item->id);

                if ($itemQuantity > $remainingQuantity) {
                    throw ValidationException::withMessages([
                        'item_quantity' => trans('validation.max.numeric', [
                            'attribute' => 'quantity',
                            'max' => $remainingQuantity
                        ]),
                    ]);
                }
            }

            /** @var \App\Models\DonationAllocation $allocation */
            $allocation = DonationAllocation::create([
                'donation_id'          => $donation->id,
                'beneficiary_order_id' => $order->id,
                'allocated_amount'     => $amount,
            ]);

            // If this is an item allocation, create allocation item record
            if ($donation->donation_type === Donation::TYPE_ITEMS && $itemIndex !== null && $itemQuantity !== null) {
                $item = $donation->items[$itemIndex];
                DonationAllocationItem::create([
                    'donation_allocation_id' => $allocation->id,
                    'donation_item_id' => $item->id,
                    'allocated_quantity' => $itemQuantity,
                    'allocated_amount' => $itemQuantity * $item->unit_price,
                ]);
            }

            $donation->used_amount = (float) $donation->used_amount + $amount;
            $donation->remaining_amount = (float) $donation->total_amount - (float) $donation->used_amount;
            $donation->save();

            return $allocation;
        });
    }

    protected function getRemainingItemQuantity(int $itemId): float
    {
        $item = DonationItem::findOrFail($itemId);
        $allocatedQuantity = DonationAllocationItem::where('donation_item_id', $itemId)
            ->sum('allocated_quantity');
        
        return max(0, (float) $item->quantity - (float) $allocatedQuantity);
    }

    public function deallocate(DonationAllocation $allocation): void
    {
        DB::transaction(function () use ($allocation) {
            /** @var \App\Models\Donation $donation */
            $donation = Donation::lockForUpdate()->findOrFail($allocation->donation_id);

            // Delete allocation items if any
            $allocation->items()->delete();

            $donation->used_amount = max(0, (float) $donation->used_amount - (float) $allocation->allocated_amount);
            $donation->remaining_amount = (float) $donation->total_amount - (float) $donation->used_amount;
            $donation->save();

            $allocation->delete();
        });
    }
}

