<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\CmsBlockCategoryConnector;

use Spryker\Zed\CmsBlockCategoryConnector\CmsBlockCategoryConnectorConfig as SprykerCmsBlockCategoryConnectorConfig;

class CmsBlockCategoryConnectorConfig extends SprykerCmsBlockCategoryConnectorConfig
{
    public const CMS_BLOCK_CATEGORY_POSITION_TOP = 'Top';
    public const CMS_BLOCK_CATEGORY_POSITION_MIDDLE = 'Middle';
    public const CMS_BLOCK_CATEGORY_POSITION_BOTTOM = 'Bottom';

    /**
     * @return string[]
     */
    public function getCmsBlockCategoryPositionList()
    {
        return [
            static::CMS_BLOCK_CATEGORY_POSITION_TOP,
            static::CMS_BLOCK_CATEGORY_POSITION_MIDDLE,
            static::CMS_BLOCK_CATEGORY_POSITION_BOTTOM,
        ];
    }
}
