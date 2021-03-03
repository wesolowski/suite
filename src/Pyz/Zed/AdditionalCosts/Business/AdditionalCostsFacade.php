<?php declare(strict_types=1);


namespace Pyz\Zed\AdditionalCosts\Business;


use Generated\Shared\Transfer\CalculableObjectTransfer;
use Spryker\Zed\Kernel\Business\AbstractFacade;

/**
 * @method \Pyz\Zed\AdditionalCosts\Business\AdditionalCostsBusinessFactory getFactory()
 */
class AdditionalCostsFacade extends AbstractFacade
{
    /**
     * @param \Generated\Shared\Transfer\CalculableObjectTransfer $calculableObjectTransfer
     */
    public function calculateAdditionalCosts(CalculableObjectTransfer $calculableObjectTransfer)
    {
        $this->getFactory()
             ->createAdditionalCostsCalculation()
             ->calculateAdditionalCosts($calculableObjectTransfer);
    }
}
