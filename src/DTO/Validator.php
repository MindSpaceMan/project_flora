<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class Validator
{
    #[Assert\Callback]
    public static function mobilePhone($object, ExecutionContextInterface $context): void
    {
        if($object != ''){
            // Только цифры
            if(!is_numeric($object)){
                $context->buildViolation('just a number')->addViolation();
            }

            // Проверка на 10 знаков
            if(mb_strlen($object, 'UTF-8') != 10){
                $context->buildViolation('This value should have exactly 10 characters.')->addViolation();
            }

            // Первый знак не равен 0 1 2 7
            $firstCharacter = substr($object, 0, 1);

            if(in_array($firstCharacter, array('0', '1', '2', '7'))){
                $context->buildViolation('First sign is not 0 1 2 7')->addViolation();
            }
        }
    }
}