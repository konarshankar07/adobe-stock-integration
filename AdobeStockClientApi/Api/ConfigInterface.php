<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockClientApi\Api;

/**
 * Class Config
 * @api
 */
interface ConfigInterface
{
    /**
     * Environment configuration
     *
     * @return string|null
     */
    public function getTargetEnvironment() : ?string;

    /**
     * Product name
     *
     * @return string|null
     */
    public function getProductName() : ?string;

    /**
     * Get Adobe Stock API files url
     *
     * @return string
     */
    public function getFilesUrl(): string;
}
