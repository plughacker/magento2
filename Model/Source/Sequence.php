<?php
namespace PlugHacker\PlugPagamentos\Model\Source;

use Magento\Framework\Option\ArrayInterface;

class Sequence implements ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => 'AnalyseFirst',
                'label' => __('Analyse First'),
            ],
            [
                'value' => 'AuthorizeFirst',
                'label' => __('Authorize First')
            ]
        ];
    }
}
