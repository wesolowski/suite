<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace PyzTest\Glue\Checkout\RestApi\Fixtures;

use Generated\Shared\Transfer\CustomerTransfer;
use Generated\Shared\Transfer\PaymentMethodTransfer;
use Generated\Shared\Transfer\PaymentProviderTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use Generated\Shared\Transfer\ShipmentMethodTransfer;
use PyzTest\Glue\Checkout\CheckoutApiTester;
use SprykerTest\Shared\Shipment\Helper\ShipmentMethodDataHelper;
use SprykerTest\Shared\Testify\Fixtures\FixturesBuilderInterface;
use SprykerTest\Shared\Testify\Fixtures\FixturesContainerInterface;

/**
 * Auto-generated group annotations
 *
 * @group PyzTest
 * @group Glue
 * @group Checkout
 * @group RestApi
 * @group GuestCheckoutDataRestApiFixtures
 * Add your own group annotations below this line
 * @group EndToEnd
 */
class GuestCheckoutDataRestApiFixtures implements FixturesBuilderInterface, FixturesContainerInterface
{
    protected const ANONYMOUS_PREFIX = 'anonymous:';

    /**
     * @var string
     */
    protected $guestCustomerReference;

    /**
     * @var \Generated\Shared\Transfer\CustomerTransfer
     */
    protected $guestCustomerTransfer;

    /**
     * @var \Generated\Shared\Transfer\QuoteTransfer
     */
    protected $guestQuoteTransfer;

    /**
     * @var \Generated\Shared\Transfer\ShipmentMethodTransfer
     */
    protected $shipmentMethodTransfer;

    /**
     * @return string
     */
    public function getGuestCustomerReference(): string
    {
        return $this->guestCustomerReference;
    }

    /**
     * @return \Generated\Shared\Transfer\CustomerTransfer
     */
    public function getGuestCustomerTransfer(): CustomerTransfer
    {
        return $this->guestCustomerTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    public function getGuestQuoteTransfer(): QuoteTransfer
    {
        return $this->guestQuoteTransfer;
    }

    /**
     * @return \Generated\Shared\Transfer\ShipmentMethodTransfer
     */
    public function getShipmentMethodTransfer(): ShipmentMethodTransfer
    {
        return $this->shipmentMethodTransfer;
    }

    /**
     * @param \PyzTest\Glue\Checkout\CheckoutApiTester $I
     *
     * @return \SprykerTest\Shared\Testify\Fixtures\FixturesContainerInterface
     */
    public function buildFixtures(CheckoutApiTester $I): FixturesContainerInterface
    {
        $this->guestCustomerReference = $I->createGuestCustomerReference();
        $this->guestCustomerTransfer = $I->createCustomerTransfer([
            CustomerTransfer::CUSTOMER_REFERENCE => static::ANONYMOUS_PREFIX . $this->guestCustomerReference,
        ]);

        $this->shipmentMethodTransfer = $I->haveShipmentMethod(
            [
                ShipmentMethodTransfer::CARRIER_NAME => 'Spryker Dummy Shipment',
                ShipmentMethodTransfer::NAME => 'Standard',
            ],
            [],
            ShipmentMethodDataHelper::DEFAULT_PRICE_LIST,
            [
                $I->getStoreFacade()->getCurrentStore()->getIdStore(),
            ]
        );

        $this->guestQuoteTransfer = $I->havePersistentQuoteWithItemsAndItemLevelShipment(
            $this->guestCustomerTransfer,
            [$I->getQuoteItemOverrideData($I->haveProductWithStock(), $this->shipmentMethodTransfer, 10)]
        );

        $this->havePayments($I);

        return $this;
    }

    /**
     * @param \PyzTest\Glue\Checkout\CheckoutApiTester $I
     *
     * @return void
     */
    protected function havePayments(CheckoutApiTester $I): void
    {
        $paymentProviderTransfer = $I->havePaymentProvider([
            PaymentProviderTransfer::PAYMENT_PROVIDER_KEY => 'DummyPayment',
            PaymentProviderTransfer::NAME => 'dummyPayment',
        ]);
        $I->havePaymentMethodWithStore([
            PaymentMethodTransfer::IS_ACTIVE => true,
            PaymentMethodTransfer::METHOD_NAME => 'Invoice',
            PaymentMethodTransfer::NAME => 'dummyPaymentInvoice',
            PaymentMethodTransfer::ID_PAYMENT_PROVIDER => $paymentProviderTransfer->getIdPaymentProvider(),
        ]);
        $I->havePaymentMethodWithStore([
            PaymentMethodTransfer::IS_ACTIVE => true,
            PaymentMethodTransfer::METHOD_NAME => 'Credit Card',
            PaymentMethodTransfer::NAME => 'dummyPaymentCreditCard',
            PaymentMethodTransfer::ID_PAYMENT_PROVIDER => $paymentProviderTransfer->getIdPaymentProvider(),
        ]);
    }
}
