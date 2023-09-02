<?php

namespace App\Services\Payment\PaymentProviders\Geidea\Callbacks;

use App\MemberCreditCard;
use App\MemberProfile;
use App\Services\Payment\Logger\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsController;

class GeideaTokenCallback
{
    use AsController;

    private Request $request;
    private MemberProfile $memberProfile;

    public function handle(Request $request): JsonResponse
    {
        $this->request = $request;

        Log::create([
            'key' => 'geidea_token_callback',
            'value' => json_encode($request->all()),
        ]);
        DB::beginTransaction();
        $this
            ->findMemberProfileFromReferenceId()
            ->updateOrCreateMemberCreditCard();
        DB::commit();
        return response()->json([
            'success' => true,
        ]);
    }


    private function findMemberProfileFromReferenceId(): self
    {
        $this->memberProfile = MemberProfile::where('id', $this->request->get('order')['merchantReferenceId'])
            ->first();

        return $this;
    }

    private function updateOrCreateMemberCreditCard(): self
    {
        $paymentMethodInfo = $this->request->get('order')['transactions'][0]['paymentMethod'];

        if (!$this->request->get('order')['tokenId']) {
            return $this;
        }

        $this->memberProfile->member->creditCards()
            ->where('default', true)->update([
                'default' => false
            ]);

        MemberCreditCard::firstOrCreate([
            'member_id' => $this->memberProfile->member_id,
            'app_id' => $this->memberProfile->app_id,
            'token' => $this->request->get('order')['tokenId'],
        ], [
            'masked_card' => $paymentMethodInfo['maskedCardNumber'],
            'card_provider' => $paymentMethodInfo['brand'],
            'holder_name' => $paymentMethodInfo['cardholderName'],
            'default' => true,
        ]);

        return $this;
    }
}
