<?php declare(strict_types=1);


namespace Pyz\Zed\AdditionalCosts\Business;


use Pyz\Zed\AdditionalCosts\AdditionalCostsConfig;
use Pyz\Zed\AdditionalCosts\Business\Model\AdditionalCostsTypeCalculator;
use Pyz\Zed\AdditionalCosts\Business\Model\AdditionalCostsTypeCalculatorInterface;
use Spryker\Zed\Kernel\Business\AbstractBusinessFactory;

/**
 * @method \Pyz\Zed\AdditionalCosts\AdditionalCostsConfig getConfig()
 */
class AdditionalCostsBusinessFactory extends AbstractBusinessFactory
{
    /**
     * @return AdditionalCostsTypeCalculatorInterface
     */
    public function createAdditionalCostsCalculation(): AdditionalCostsTypeCalculatorInterface
    {
        return new AdditionalCostsTypeCalculator(
            $this->getConfig()->getAdditionalCosts()
        );
    }
}
