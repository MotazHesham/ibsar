<?php

namespace App\Services;

use App\Models\BeneficiaryOrder;
use App\Models\Donation;
use App\Models\DonationAllocation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DonationAllocationService
{
    public function allocate(int $donationId, int $beneficiaryOrderId, float $amount): DonationAllocation
    {
        return DB::transaction(function () use ($donationId, $beneficiaryOrderId, $amount) {
            /** @var \App\Models\Donation $donation */
            $donation = Donation::lockForUpdate()->findOrFail($donationId);

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

            /** @var \App\Models\DonationAllocation $allocation */
            $allocation = DonationAllocation::create([
                'donation_id'          => $donation->id,
                'beneficiary_order_id' => $order->id,
                'allocated_amount'     => $amount,
            ]);

            $donation->used_amount = (float) $donation->used_amount + $amount;
            $donation->remaining_amount = (float) $donation->total_amount - (float) $donation->used_amount;
            $donation->save();

            return $allocation;
        });
    }

    public function deallocate(DonationAllocation $allocation): void
    {
        DB::transaction(function () use ($allocation) {
            /** @var \App\Models\Donation $donation */
            $donation = Donation::lockForUpdate()->findOrFail($allocation->donation_id);

            $donation->used_amount = max(0, (float) $donation->used_amount - (float) $allocation->allocated_amount);
            $donation->remaining_amount = (float) $donation->total_amount - (float) $donation->used_amount;
            $donation->save();

            $allocation->delete();
        });
    }
}

