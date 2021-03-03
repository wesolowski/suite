<?php declare(strict_types=1);

namespace Pyz\Zed\AdditionalCosts\Business\Model;

use Generated\Shared\Transfer\CalculableObjectTransfer;

interface AdditionalCostsTypeCalculatorInterface
{
    /**
     * @param CalculableObjectTransfer $calculableObjectTransfer
     */
    public function calculateAdditionalCosts(CalculableObjectTransfer $calculableObjectTransfer);
}
