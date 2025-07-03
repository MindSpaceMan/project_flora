<?php
declare(strict_types=1);

namespace App\Service\Payment;

use App\Enum\PaymentProcessorEnum;
use App\Interface\PaymentProcessorInterface;
use Psr\Log\LoggerInterface;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor;
use App\Attribute\AsPaymentProcessor;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

#[AsPaymentProcessor('stripe')]
final readonly class StripeProcessor implements PaymentProcessorInterface
{
    public function __construct(private StripePaymentProcessor $stripeProcessor,
                                private LoggerInterface $logger)
    {}

    /**
     * @param int $priceInCents
     * @return bool true, if payment success, false — if throwing exception
     */
    public function pay(int $priceInCents): bool
    {
        try {
            $amount = BigDecimal::of($priceInCents);
            $scaled = $amount->dividedBy('100', 2, RoundingMode::HALF_UP);
            $success = $this->stripeProcessor->processPayment($scaled->toFloat());
            if (!$success) {
                $this->logger->error("Stripe Payment failed: Transaction declined.");
            }
            return $success;
        } catch (\Exception $e) {
            $this->logger->error("Payment failed: " . $e->getMessage());
            return false;
        }
    }

    public function getAlias(): string
    {
        return PaymentProcessorEnum::STRIPE->value;
    }
}
