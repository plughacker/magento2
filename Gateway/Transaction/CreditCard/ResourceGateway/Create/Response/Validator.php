<?php
namespace PlugHacker\PlugPagamentos\Gateway\Transaction\CreditCard\ResourceGateway\Create\Response;


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
            throw new \InvalidArgumentException('Plug Credit Card Authorize Response object should be provided');
        }

        $isValid = true;
        $fails = [];

        return $this->createResult($isValid, $fails);
    }
}
