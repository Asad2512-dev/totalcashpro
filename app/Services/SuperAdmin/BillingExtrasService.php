<?php

declare(strict_types=1);

namespace App\Services\SuperAdmin;

use App\Concerns\LogsAdminActions;
use App\Contracts\ServiceInterface;
use App\Models\Coupon;
use App\Models\Discount;
use Illuminate\Support\Str;

final class BillingExtrasService implements ServiceInterface
{
    use LogsAdminActions;

    /**
     * @param  array<string, mixed>  $data
     */
    public function createCoupon(array $data): Coupon
    {
        $data['code'] = Str::upper($data['code']);
        $coupon = Coupon::query()->create($data);
        $this->logAdminAction('coupon.created', 'Coupon created: '.$coupon->code, $coupon, null, $coupon->toArray());

        return $coupon;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateCoupon(Coupon $coupon, array $data): Coupon
    {
        $old = $coupon->toArray();
        if (isset($data['code'])) {
            $data['code'] = Str::upper($data['code']);
        }
        $coupon->update($data);
        $this->logAdminAction('coupon.updated', 'Coupon updated: '.$coupon->code, $coupon, $old, $coupon->fresh()?->toArray());

        return $coupon->refresh();
    }

    public function deleteCoupon(Coupon $coupon): void
    {
        $snapshot = $coupon->toArray();
        $code = $coupon->code;
        $coupon->delete();
        $this->logAdminAction('coupon.deleted', 'Coupon deleted: '.$code, null, $snapshot);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function createDiscount(array $data): Discount
    {
        $discount = Discount::query()->create($data);
        $this->logAdminAction('discount.applied', 'Discount applied to organisation #'.$discount->organization_id, $discount, null, $discount->toArray());

        return $discount;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function updateDiscount(Discount $discount, array $data): Discount
    {
        $old = $discount->toArray();
        $discount->update($data);
        $this->logAdminAction('discount.updated', 'Discount updated', $discount, $old, $discount->fresh()?->toArray());

        return $discount->refresh();
    }

    public function deleteDiscount(Discount $discount): void
    {
        $snapshot = $discount->toArray();
        $discount->delete();
        $this->logAdminAction('discount.deleted', 'Discount removed', null, $snapshot);
    }
}
