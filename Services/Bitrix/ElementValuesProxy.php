<?php

namespace Prokl\StaticPageMakerBundle\Services\Bitrix;

use Bitrix\Iblock\InheritedProperty\ElementValues;

/**
 * Class ElementValuesProxy
 * @package Prokl\StaticPageMakerBundle\Services\Bitrix
 *
 * @since 23.01.2021
 */
class ElementValuesProxy
{
    /**
     * @var integer $iblockId ID инфоблока.
     */
    private $iblockId;

    /**
     * @var integer $elementId ID элемента.
     */
    private $elementId;

    /**
     * @return array
     */
    public function queryValues() : array
    {
        $ipropValues = new ElementValues($this->iblockId, $this->elementId);

        return $ipropValues->queryValues();
    }

    /**
     * @param integer $iblockId ID инфоблока.
     *
     * @return $this
     */
    public function setIblockId(int $iblockId): self
    {
        $this->iblockId = $iblockId;

        return $this;
    }

    /**
     * @param integer $elementId ID элемента.
     *
     * @return $this
     */
    public function setElementId(int $elementId): self
    {
        $this->elementId = $elementId;

        return $this;
    }
}
