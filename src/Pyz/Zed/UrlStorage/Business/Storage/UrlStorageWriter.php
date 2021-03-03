<?php

/**
 * This file is part of the Spryker Suite.
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Pyz\Zed\UrlStorage\Business\Storage;

use Generated\Shared\Transfer\QueueSendMessageTransfer;
use Generated\Shared\Transfer\SynchronizationDataTransfer;
use Generated\Shared\Transfer\UrlStorageTransfer;
use Orm\Zed\UrlStorage\Persistence\SpyUrlStorage;
use Propel\Runtime\Propel;
use Pyz\Zed\UrlStorage\Business\Storage\Cte\UrlStorageCteInterface;
use Spryker\Client\Queue\QueueClientInterface;
use Spryker\Service\Synchronization\SynchronizationServiceInterface;
use Spryker\Zed\UrlStorage\Business\Storage\UrlStorageWriter as SprykerUrlStorageWriter;
use Spryker\Zed\UrlStorage\Dependency\Facade\UrlStorageToStoreFacadeInterface;
use Spryker\Zed\UrlStorage\Dependency\Service\UrlStorageToUtilSanitizeServiceInterface;
use Spryker\Zed\UrlStorage\Persistence\UrlStorageEntityManagerInterface;
use Spryker\Zed\UrlStorage\Persistence\UrlStorageRepositoryInterface;

/**
 * @example
 *
 * This is an example of running UrlStorageWriter
 * with CTE (@see https://www.postgresql.org/docs/9.1/queries-with.html).
 * By using this class, reduce the amount of database queries and increase the performance
 * for saving storage data in database.
 */
class UrlStorageWriter extends SprykerUrlStorageWriter
{
    /**
     * @var \Spryker\Service\Synchronization\SynchronizationServiceInterface
     */
    protected $synchronizationService;

    /**
     * @var \Spryker\Client\Queue\QueueClientInterface
     */
    protected $queueClient;

    /**
     * @var array
     */
    protected $synchronizedDataCollection = [];

    /**
     * @var array
     */
    protected $synchronizedMessageCollection = [];

    /**
     * @var \Pyz\Zed\UrlStorage\Business\Storage\Cte\UrlStorageCteInterface
     */
    protected $urlStorageCte;

    /**
     * @param \Spryker\Zed\UrlStorage\Dependency\Service\UrlStorageToUtilSanitizeServiceInterface $utilSanitize
     * @param \Spryker\Zed\UrlStorage\Persistence\UrlStorageRepositoryInterface $urlStorageRepository
     * @param \Spryker\Zed\UrlStorage\Persistence\UrlStorageEntityManagerInterface $urlStorageEntityManager
     * @param \Spryker\Zed\UrlStorage\Dependency\Facade\UrlStorageToStoreFacadeInterface $storeFacade
     * @param bool $isSendingToQueue
     * @param \Spryker\Service\Synchronization\SynchronizationServiceInterface $synchronizationService
     * @param \Spryker\Client\Queue\QueueClientInterface $queueClient
     * @param \Pyz\Zed\UrlStorage\Business\Storage\Cte\UrlStorageCteInterface $urlStorageCte
     */
    public function __construct(
        UrlStorageToUtilSanitizeServiceInterface $utilSanitize,
        UrlStorageRepositoryInterface $urlStorageRepository,
        UrlStorageEntityManagerInterface $urlStorageEntityManager,
        UrlStorageToStoreFacadeInterface $storeFacade,
        bool $isSendingToQueue,
        SynchronizationServiceInterface $synchronizationService,
        QueueClientInterface $queueClient,
        UrlStorageCteInterface $urlStorageCte
    ) {
        parent::__construct($utilSanitize, $urlStorageRepository, $urlStorageEntityManager, $storeFacade, $isSendingToQueue);

        $this->synchronizationService = $synchronizationService;
        $this->queueClient = $queueClient;
        $this->urlStorageCte = $urlStorageCte;
    }

    /**
     * @param array $urlStorageTransfers
     * @param array $urlStorageEntities
     *
     * @return void
     */
    protected function storeData(array $urlStorageTransfers, array $urlStorageEntities)
    {
        foreach ($urlStorageTransfers as $urlStorageTransfer) {
            $urlStorageEntity = $urlStorageEntities[$urlStorageTransfer->getIdUrl()] ?? null;

            $this->storeDataSet($urlStorageTransfer, $urlStorageEntity);
        }

        $this->write();

        if ($this->synchronizedMessageCollection !== []) {
            $this->queueClient->sendMessages('sync.storage.url', $this->synchronizedMessageCollection);
        }
    }

    /**
     * @param \Generated\Shared\Transfer\UrlStorageTransfer $urlStorageTransfer
     * @param \Orm\Zed\UrlStorage\Persistence\SpyUrlStorage|null $urlStorageEntity
     *
     * @return void
     */
    protected function storeDataSet(UrlStorageTransfer $urlStorageTransfer, ?SpyUrlStorage $urlStorageEntity = null)
    {
        $resource = $this->findResourceArguments($urlStorageTransfer->toArray());

        if ($resource === null) {
            return;
        }

        $urlStorage = [
            'resources' => [
                'fk_' . $resource[static::RESOURCE_TYPE] => $resource[static::RESOURCE_VALUE],
            ],
            'url' => $urlStorageTransfer->getUrl(),
            'fk_url' => $urlStorageTransfer->getIdUrl(),
            'data' => $urlStorageTransfer->modifiedToArray(),
        ];

        $this->add($urlStorage);
    }

    /**
     * @param array $urlStorage
     *
     * @return void
     */
    protected function add(array $urlStorage): void
    {
        $synchronizedData = $this->buildSynchronizedData($urlStorage, 'url', 'url');
        $this->synchronizedDataCollection[] = $synchronizedData;

        if ($this->isSendingToQueue) {
            $this->synchronizedMessageCollection[] = $this->buildSynchronizedMessage($synchronizedData, 'url');
        }
    }

    /**
     * @param array $data
     * @param string $keySuffix
     * @param string $resourceName
     *
     * @return array
     */
    public function buildSynchronizedData(array $data, string $keySuffix, string $resourceName): array
    {
        $key = $this->generateResourceKey($data, $keySuffix, $resourceName);
        $encodedData = json_encode($data['data']);
        $data['key'] = $key;
        $data['data'] = $encodedData;

        return $data;
    }

    /**
     * @param array $data
     * @param string $keySuffix
     * @param string $resourceName
     *
     * @return string
     */
    protected function generateResourceKey(array $data, string $keySuffix, string $resourceName): string
    {
        $syncTransferData = new SynchronizationDataTransfer();

        $syncTransferData->setReference($data[$keySuffix]);
        $keyBuilder = $this->synchronizationService->getStorageKeyBuilder($resourceName);

        return $keyBuilder->generateKey($syncTransferData);
    }

    /**
     * @param array $data
     * @param string $resourceName
     * @param array $params
     *
     * @return \Generated\Shared\Transfer\QueueSendMessageTransfer
     */
    public function buildSynchronizedMessage(
        array $data,
        string $resourceName,
        array $params = []
    ): QueueSendMessageTransfer {
        $data['_timestamp'] = microtime(true);
        $payload = [
            'write' => [
                'key' => $data['key'],
                'value' => $data['data'],
                'resource' => $resourceName,
                'params' => $params,
            ],
        ];

        $queueSendTransfer = new QueueSendMessageTransfer();
        $queueSendTransfer->setBody(json_encode($payload));

        $queueSendTransfer->setQueuePoolName('synchronizationPool');

        return $queueSendTransfer;
    }

    /**
     * @return void
     */
    public function write()
    {
        if (empty($this->synchronizedDataCollection)) {
            return;
        }

        $stmt = Propel::getConnection()->prepare($this->getSql());
        $stmt->execute($this->getParams());
    }

    /**
     * @return string
     */
    protected function getSql(): string
    {
        return $this->urlStorageCte->getSql();
    }

    /**
     * @return array
     */
    protected function getParams(): array
    {
        return $this->urlStorageCte->buildParams($this->synchronizedDataCollection);
    }
}
