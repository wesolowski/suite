<?php declare(strict_types=1);


namespace Pyz\Zed\AdditionalCosts\Communication\Plugin\Calculation;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Pyz\Zed\AdditionalCosts\Business\AdditionalCostsFacade;
use Spryker\Zed\CalculationExtension\Dependency\Plugin\CalculationPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Pyz\Zed\AdditionalCosts\Business\AdditionalCostsFacade getFacade()
 */
class AdditionalCostsCalculationPlugin extends AbstractPlugin implements CalculationPluginInterface
{
    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     */
    public function recalculate(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this->getFacade()
             ->calculateAdditionalCosts($calculableObjectTransfer);
    }
}
