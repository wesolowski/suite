<?php declare(strict_types=1);


namespace Pyz\Zed\AdditionalCosts;


use Spryker\Zed\Kernel\AbstractBundleConfig;

class AdditionalCostsConfig extends AbstractBundleConfig
{
    public const CALCULATION_ADDITIONAL_COSTS = 'CALCULATION_ADDITIONAL_COSTS';

    /**
     * @return int
     */
    public function getAdditionalCosts(): int
    {
        return 25;
    }
}
