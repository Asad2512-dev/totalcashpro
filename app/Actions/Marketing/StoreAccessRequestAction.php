<?php

declare(strict_types=1);

namespace App\Actions\Marketing;

use App\Enums\AccessRequestStatus;
use App\Enums\SubscriptionPlan;
use App\Mail\AccessRequestSubmittedMail;
use App\Models\AccessRequest;
use App\Services\Mail\MailSender;

final class StoreAccessRequestAction
{
    public function __construct(private readonly MailSender $mail) {}

    /**
     * @param  array{
     *     business_name: string,
     *     owner_name: string,
     *     email: string,
     *     phone: string,
     *     business_address?: string|null,
     *     country: string,
     *     business_type: string,
     *     number_of_employees: string,
     *     selected_plan: string,
     *     additional_notes?: string|null
     * }  $data
     */
    public function execute(array $data): AccessRequest
    {
        $request = AccessRequest::query()->create([
            ...$data,
            'selected_plan' => SubscriptionPlan::from($data['selected_plan']),
            'status' => AccessRequestStatus::Pending,
        ]);

        $supportEmail = (string) config('totalcashpro.support_email');

        if ($supportEmail !== '') {
            $this->mail->sendMailable(new AccessRequestSubmittedMail($request), $supportEmail);
        }

        return $request;
    }
}
