<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);
namespace Magento\AdobeStockAsset\Test\Unit\Model;
use Magento\AdobeStockAsset\Model\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
/**
 * Config data test.
 */
class ConfigTest extends TestCase
{
    private const XML_PATH_ENABLED = 'adobe_stock/integration/enabled';
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfigMock;
    /**
     * Prepare test objects.
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->config = $this->objectManager->getObject(
            Config::class,
            [
                'scopeConfig' => $this->scopeConfigMock
            ]
        );
    }
    /**
     * Is adobe stock integration enabled test.
     */
    public function testIsEnabled(): void
    {
        $this->scopeConfigMock->expects($this->once())
            ->method('isSetFlag')
            ->with(self::XML_PATH_ENABLED)
            ->willReturn(true);
        $this->assertEquals(true, $this->config->isEnabled());
    }
}
