<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\MediaGalleryMetadata\Model\File;

use Magento\MediaGalleryMetadataApi\Api\ExtractMetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterface;
use Magento\MediaGalleryMetadataApi\Api\Data\MetadataInterfaceFactory;
use Magento\Framework\Exception\ValidatorException;
use Magento\MediaGalleryMetadataApi\Model\FileInterfaceFactory;
use Magento\MediaGalleryMetadataApi\Model\FileInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\MediaGalleryMetadataApi\Model\ReadFileInterface;
use Magento\MediaGalleryMetadataApi\Model\MetadataReaderInterface;

/**
 * Extract Metadata from asset fy file by given extractors
 */
class ExtractMetadata implements ExtractMetadataInterface
{

    /**
     * @var MetadataInterfaceFactory
     */
    private $metadataFactory;

    /**
     * @var array
     */
    private $metadataExtractors;

    /**
     * @var FileInterfaceFactory
     */
    private $fileFactory;

    /**
     * @param FileInterfaceFactory $fileFactory
     * @param MetadataInterfaceFactory $metadataFactory
     * @param array $metadataExtractors
     */
    public function __construct(
        FileInterfaceFactory $fileFactory,
        MetadataInterfaceFactory $metadataFactory,
        array $metadataExtractors
    ) {
        $this->fileFactory = $fileFactory;
        $this->metadataFactory = $metadataFactory;
        $this->metadataExtractors = $metadataExtractors;
    }

    /**
     * @inheritdoc
     */
    public function execute(string $path): MetadataInterface
    {
        try {
            return $this->extractMetadata($path);
        } catch (\Exception $exception) {
            return $this->getEmptyResult();
        }
    }

    /**
     * Create empty metadata object
     *
     * @return MetadataInterface
     */
    private function getEmptyResult(): MetadataInterface
    {
        return $this->metadataFactory->create([
            'title' => '',
            'description' => '',
            'keywords' => []
        ]);
    }

    /**
     * Extract metadata from file
     *
     * @param string $path
     * @return MetadataInterface
     */
    private function extractMetadata(string $path): MetadataInterface
    {
        foreach ($this->metadataExtractors as $extractor) {
            $file = $this->readFile($extractor['fileReaders'], $path);

            if (!empty($file->getSegments())) {
                list($title, $description, $keywords) = $this->readSegments($extractor['segmentReaders'], $file);
            }
            
            if (!empty($title) && !empty($description) && !empty($keywords)) {
                break;
            }
        }
        return $this->metadataFactory->create([
            'title' => $title,
            'description' => $description,
            'keywords' => empty($keywords) ? null : $keywords
        ]);
    }

    /**
     * Read  file segments by given segmentReader
     *
     * @param array $segmentReaders
     * @param FileInterface $file
     */
    private function readSegments(array $segmentReaders, FileInterface $file): array
    {
        $title = null;
        $description = null;
        $keywords = [];
        
        foreach ($segmentReaders as $segmentReader) {
            if (!$segmentReader instanceof MetadataReaderInterface) {
                throw new LocalizedException(__('SegmentReader must implement MetadataReaderInterface'));
            }

            $data = $segmentReader->execute($file);
            $title = !empty($data->getTitle()) ? $data->getTitle() : $title;
            $description = !empty($data->getDescription()) ? $data->getDescription() : $description;
            $keywords =  $keywords + $data->getKeywords();
        }
        return [$title, $description, $keywords];
    }

    /**
     * Read file by given fileReader
     *
     * @param array $fileReaders
     * @param string $path
     */
    private function readFile(array $fileReaders, string $path): FileInterface
    {
        $file =  $this->fileFactory->create([
            'path' => $path,
            'segments' => []
        ]);

        foreach ($fileReaders as $fileReader) {
            if (!$fileReader instanceof ReadFileInterface) {
                throw new LocalizedException(__('FileReader must implement ReadFileInterface'));
            }

            try {
                $file = $fileReader->execute($path);
            } catch (ValidatorException $exception) {
                continue;
            } catch (\Exception $exception) {
                throw new LocalizedException(
                    __('Could not parse the image file for metadata: %path', ['path' => $path])
                );
            }
        }
        return $file;
    }
}
