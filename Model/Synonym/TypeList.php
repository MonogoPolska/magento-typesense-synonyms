<?php
/**
 * @category   Monogo
 * @package    Monogo\TypesenseSynonyms
 * @author     Vladyslav Deyneko <vladyslav.deyneko@monogo.pl>
 * @copyright  Copyright (c) 2024 Monogo Sp. z o.o.
 */

declare(strict_types=1);

namespace Monogo\TypesenseSynonyms\Model\Synonym;

use Magento\Framework\Data\OptionSourceInterface;
use Monogo\TypesenseSynonyms\Api\Data\SynonymInterface;

/**
 * Provides Synonym types array for UI input of type select.
 *
 * @since 1.0.0
 */
class TypeList implements OptionSourceInterface
{
    /**
     * @inheritdoc
     */
    public function toOptionArray(): array
    {
        return [
            [
                'value' => SynonymInterface::TYPE_ONE_WAY,
                'label' => __('One-way'),
            ],
            [
                'value' => SynonymInterface::TYPE_MULTI_WAY,
                'label' => __('Multi-way'),
            ],
        ];
    }
}
