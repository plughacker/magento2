<?php
namespace PlugHacker\PlugPagamentos\Gateway\Transaction\CreditCard\ResourceGateway\Capture\Response;


use Magento\Payment\Gateway\Validator\ValidatorInterface;
use PlugHacker\PlugPagamentos\Gateway\Transaction\Base\ResourceGateway\Response\AbstractValidator;

class Validator extends AbstractValidator implements ValidatorInterface
{
    /**
     * {@inheritdoc}
     */
    public function validate(array $validationSubject)
    {
        if (!isset($validationSubject['response'])) {
            throw new \InvalidArgumentException('Plug Credit Card Capture Response object should be provided');
        }

        $isValid = true;
        $fails = [];

        return $this->createResult($isValid, $fails);
    }
}
