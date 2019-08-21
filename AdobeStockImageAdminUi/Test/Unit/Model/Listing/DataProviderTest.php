<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\AdobeStockImageAdminUi\Test\Unit\Model\Listing;

use Magento\AdobeStockImageAdminUi\Model\Listing\DataProvider;
use Magento\AdobeStockImageApi\Api\GetImageListInterface;
use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Api\AttributeInterface;

/**
 * Test data image provider.
 */
class DataProviderTest extends TestCase
{
    /**
     * @var DataProvider
     */
    private $dataProvider;

    /**
     * @var GetImageListInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $getImageListMock;

    /**
     * @var SearchCriteriaBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    private $searchCriteriaBuilder;

    /**
     * Prepare test objects.
     */
    public function setUp(): void
    {
        $this->getImageListMock = $this->getMockBuilder(GetImageListInterface::class)
            ->setMethods(['execute'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->searchCriteriaBuilder = $this->getMockBuilder(SearchCriteriaBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->dataProvider = (new ObjectManager($this))->getObject(
            DataProvider::class,
            [
                'name' => 'adobe_stock_images_listing_data_source',
                'primaryFieldName' => 'id',
                'requestFieldName' => 'id',
                'searchCriteriaBuilder' => $this->searchCriteriaBuilder,
                'getImageList' => $this->getImageListMock,
            ]
        );
    }

    /**
     * Test data in result.
     */
    public function testGetSearchResult(): void
    {
        $searchCriteria = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteria->expects($this->once())
            ->method('setRequestName')
            ->with('adobe_stock_images_listing_data_source');

        $this->searchCriteriaBuilder->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        /** @var SearchResultInterface|MockObject $searchResult */
        $searchResult = $this->getMockBuilder(SearchResultInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->getImageListMock->expects($this->once())
            ->method('execute')
            ->with($searchCriteria)
            ->willReturn($searchResult);

        $this->assertEquals($searchResult, $this->dataProvider->getSearchResult());
    }

    /**
     * @dataProvider itemsDataProvider
     */
    public function testGetData(array $itemsData): void
    {
        $searchCriteria = $this->getMockBuilder(SearchCriteria::class)
            ->disableOriginalConstructor()
            ->getMock();
        $searchCriteria->expects($this->once())
            ->method('setRequestName')
            ->with('adobe_stock_images_listing_data_source');

        $this->searchCriteriaBuilder->expects($this->once())
            ->method('create')
            ->willReturn($searchCriteria);

        $searchResult = $this->getSearchResult($itemsData);

        $this->getImageListMock->expects($this->once())
            ->method('execute')
            ->with($searchCriteria)
            ->willReturn($searchResult);

        $data = [
            'items' => $itemsData,
            'totalRecords' => count($itemsData)
        ];

        $this->assertEquals($data, $this->dataProvider->getData());
    }

    /**
     * @return SearchResultInterface|MockObject
     */
    private function getSearchResult(array $itemsData): SearchResultInterface
    {
        $items = [];

        foreach ($itemsData as $itemData) {
            $item = $this->getMockForAbstractClass(DocumentInterface::class);
            $attributes = [];
            foreach ($itemData as $key => $value) {
                $attribute = $this->getMockForAbstractClass(AttributeInterface::class);
                $attribute->expects($this->once())
                    ->method('getAttributeCode')
                    ->willReturn($key);
                $attribute->expects($this->once())
                    ->method('getValue')
                    ->willReturn($value);
                $attributes[] = $attribute;
            }
            $item->expects($this->once())
                ->method('getCustomAttributes')
                ->willReturn($attributes);
            $items[] = $item;
        }
        /** @var SearchResultInterface|MockObject $searchResult */
        $searchResult = $this->getMockBuilder(SearchResultInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $searchResult->expects($this->once())
            ->method('getItems')
            ->willReturn($items);
        $searchResult->expects($this->once())
            ->method('getTotalCount')
            ->willReturn(count($items));

        return $searchResult;
    }

    /**
     * @return array
     */
    public function itemsDataProvider(): array
    {
        $itemsData = [
            [
                'id_field_name' => 'id',
                'id' => 273563073,
                'path' => '',
                'url' => 'https://test.com/image1.jpg',
                'height' => 3664,
                'width' => 14136,
                'media_type_id' => 0,
                'keywords' => [],
                'premium_level_id' => 0,
                'adobe_id' => 0,
                'stock_id' => 0,
                'licensed' => 0,
                'title' => '',
                'preview_url' => '',
                'preview_width' => 0,
                'preview_height' => 0,
                'country_name' => '',
                'details_url' => '',
                'vector_type' => '',
                'content_type' => '',
                'creation_date' => '',
                'created_at' => '',
                'updated_at' => ''
            ],
            [
                'id_field_name' => 'id',
                'id' => 272239824,
                'path' => '',
                'url' => 'https://test.com/image2.jpg',
                'height' => 7264,
                'width' => 13111,
                'media_type_id' => 0,
                'keywords' => [],
                'premium_level_id' => 0,
                'adobe_id' => 0,
                'stock_id' => 0,
                'licensed' => 0,
                'title' => '',
                'preview_url' => '',
                'preview_width' => 0,
                'preview_height' => 0,
                'country_name' => '',
                'details_url' => '',
                'vector_type' => '',
                'content_type' => '',
                'creation_date' => '',
                'created_at' => '',
                'updated_at' => ''
            ],
            [
                'id_field_name' => 'id',
                'id' => 272492011,
                'path' => '',
                'url' => 'https://test.com/image3.jpg',
                'height' => 4000,
                'width' => 6000,
                'media_type_id' => 0,
                'keywords' => [],
                'premium_level_id' => 0,
                'adobe_id' => 0,
                'stock_id' => 0,
                'licensed' => 0,
                'title' => '',
                'preview_url' => '',
                'preview_width' => 0,
                'preview_height' => 0,
                'country_name' => '',
                'details_url' => '',
                'vector_type' => '',
                'content_type' => '',
                'creation_date' => '',
                'created_at' => '',
                'updated_at' => ''
            ],
        ];

        return [
            [$itemsData]
        ];
    }
}
