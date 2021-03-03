<?php declare(strict_types=1);


namespace Pyz\Zed\AdditionalCosts\Business\Model;


use Generated\Shared\Transfer\CalculableObjectTransfer;

class AdditionalCostsTypeCalculator implements AdditionalCostsTypeCalculatorInterface
{
    /**
     * @var int
     */
    protected $extraCosts;

    /**
     * @param int $extraCosts
     */
    public function __construct(int $extraCosts)
    {
        $this->extraCosts = $extraCosts;
    }

    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     */
    public function calculateAdditionalCosts(CalculableObjectTransfer $calculableObjectTransfer)
    {
        foreach ($calculableObjectTransfer->getItems() as $item)  {
            $item->setUnitNetPrice($item->getUnitNetPrice() + $this->extraCosts);
            $item->setUnitGrossPrice($item->getUnitGrossPrice() + $this->extraCosts);
            $item->setUnitPrice($item->getUnitPrice() + $this->extraCosts);
            $item->setSumPrice($item->getUnitPrice() + $item->getQuantity());
            $item->setSumNetPrice($item->getUnitNetPrice() * $item->getQuantity());
            $item->setSumGrossPrice($item->getUnitGrossPrice() * $item->getQuantity());
        }
    }
}
