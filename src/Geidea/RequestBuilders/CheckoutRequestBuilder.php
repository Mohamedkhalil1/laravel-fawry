<?php

namespace App\Services\Payment\PaymentProviders\Geidea\RequestBuilders;

use App\MemberProfile;
use App\Services\Payment\Models\PaymentCheckout;
use App\Services\Payment\PaymentProviders\Geidea\Enums\GeideaPaymentOptionEnum;
use Illuminate\Http\Resources\MissingValue;

class CheckoutRequestBuilder extends BaseRequestBuilder
{
    private float $amount = 1;
    private string $currency = 'EGP';
    private ?string $callbackUrl = null;
    private string $merchantReferenceId;
    private string $language = 'en';
    private bool $cardOnFile = true;
    private ?string $tokenId = null;
    private ?string $initiatedBy = null;
    private int $agreementId;
    private string $agreementType = 'Unscheduled';
    private array $cofAgreement = [];
    /**
     * @var GeideaPaymentOptionEnum[]
     */
    private array $paymentOptions = [];

    public function __construct()
    {
        $this->setInitiatedBy('Internet');
    }

    public static function make(): static
    {
        return new static();
    }

    public function buildInstallmentPayment(PaymentCheckout $paymentCheckout, MemberProfile $memberProfile)
    {
        return $this
            ->setCardOnFile(false)
            ->setCallbackUrl(route('geidea.installment.callback'))
            ->setMerchantReferenceId($paymentCheckout->orderId)
            ->setAmount($paymentCheckout->amount)
            ->setAgreementId($memberProfile->id)
            ->setCofAgreementToCheckout();
//            ->setPaymentOptions([
//                GeideaPaymentOptionEnum::VALU
//            ]);
    }

    public function buildCheckoutPayment(PaymentCheckout $paymentCheckout, MemberProfile $memberProfile)
    {
        return $this
            ->setTokenId($paymentCheckout->cardToken)
            ->setMerchantReferenceId($paymentCheckout->orderId)
            ->setAmount($paymentCheckout->amount)
            ->setAgreementId($memberProfile->id)
            ->setCallbackUrl(route('geidea.token.callback'))
            ->setCofAgreementToCheckout();
//            ->setPaymentOptions([
//                GeideaPaymentOptionEnum::VISA,
//                GeideaPaymentOptionEnum::MASTER_CARD,
//            ]);
    }

    public function setAmount(?float $amount = null): self
    {
        if (!$amount) {
            return $this;
        }
        $this->amount = $amount;
        return $this;
    }

    public function setCallbackUrl(string $url): self
    {
        if (app()->environment('local', 'testing')) {
            config(['app.url' => config('app.ngrok_test_url')]);
            $url = str_replace('http://localhost', config('app.ngrok_test_url'), $url);
        }
        $this->callbackUrl = $url;
        return $this;
    }

    public function setMerchantReferenceId(string $referenceId): self
    {
        $this->merchantReferenceId = $referenceId;
        return $this;
    }

    public function toArray(): array
    {
        return array_merge($this->setTokenIdOrSetCardOnFile(), $this->cofAgreement, [
            'amount' => round($this->amount, 2),
            'currency' => $this->currency,
            'callbackUrl' => $this->callbackUrl ?? new MissingValue(),
            'merchantReferenceId' => $this->merchantReferenceId,
            'language' => $this->language,
            'initiatedBy' => $this->initiatedBy,
//            'paymentOptions' => collect($this->paymentOptions)->map(fn(GeideaPaymentOptionEnum $option) => [
//                'label' => $option->label(),
//                'paymentMethods' => $option->value,
//            ])->all(),
        ]);
    }

    public function setTokenId(string $tokenId = null): self
    {
        if (!$tokenId) {
            return $this;
        }
        $this->tokenId = $tokenId;

        return $this;
    }

    private function setTokenIdOrSetCardOnFile(): array
    {
        if ($this->tokenId) {
            return [
                'tokenId' => $this->tokenId
            ];
        }
        return [
            'cardOnFile' => $this->cardOnFile
        ];
    }

    public function setInitiatedBy(string $initiatedBy): self
    {
        $this->initiatedBy = $initiatedBy;
        return $this;
    }

    public function setAgreementId(int $memberProfileId): static
    {
        $this->agreementId = $memberProfileId;
        return $this;
    }

    public function setCofAgreementToCheckout(): self
    {
        $this->initiatedBy = 'Merchant';

        $this->cofAgreement = [
            'agreementId' => (string)$this->agreementId,
            'agreementType' => $this->agreementType,
            'payWithToken' => true
        ];

        return $this;
    }

    public function setCofAgreementToCreateSession(): self
    {
        $this->initiatedBy = 'Internet';

        $this->cofAgreement = [
            'cofAgreement' => [
                'id' => (string)$this->agreementId,
                'type' => $this->agreementType,
            ]
        ];

        return $this;
    }

    public function setCardOnFile($cardOnFile): self
    {
        $this->cardOnFile = $cardOnFile;
        return $this;
    }

    /**
     * @param GeideaPaymentOptionEnum[] $paymentOptions
     */
    public function setPaymentOptions(array $paymentOptions): self
    {
        $this->paymentOptions = $paymentOptions;

        return $this;
    }
}
