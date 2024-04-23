<?php
/**
 * @category   Monogo
 * @package    Monogo\TypesenseSynonyms
 * @author     Vladyslav Deyneko <vladyslav.deyneko@monogo.pl>
 * @copyright  Copyright (c) 2024 Monogo Sp. z o.o.
 */

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Model\Synonym;

use Http\Client\Exception;
use Magento\Framework\Data\OptionSourceInterface;
use Monogo\TypesenseCore\Adapter\Client;
use Monogo\TypesenseCore\Exceptions\ConnectionException;
use Typesense\Exceptions\ConfigError;
use Typesense\Exceptions\TypesenseClientError;

/**
 * Class SearchCollectionAliasList
 * @since 1.0.0
 */
class SearchCollectionAliasList implements OptionSourceInterface
{
    private Client $typesenseClient;


    /**
     * @param Client $typesenseClient
     */
    public function __construct(Client $typesenseClient)
    {
        $this->typesenseClient = $typesenseClient;
    }

    /**
     * @return array
     *
     * @throws Exception
     * @throws ConnectionException
     * @throws ConfigError
     * @throws TypesenseClientError
     */
    private function getAliases(): array
    {
        return $this->typesenseClient->getClient()->getAliases()->retrieve();
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        try {
            return array_map(fn($alias) => [
                'value' => $alias['name'],
                'label' => $alias['name']
            ], $this->getAliases()['aliases']);
        } catch (Exception $e) {
            return [];
        }
    }
}
