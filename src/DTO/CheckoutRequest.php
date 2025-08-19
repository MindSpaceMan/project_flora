<?php
declare(strict_types=1);

namespace App\DTO;

use OpenApi\Attributes\Property;
use Symfony\Component\Validator\Constraints as Assert;

final class CheckoutRequest
{
    #[Assert\NotBlank(message: 'Укажите имя получателя')]
    #[Assert\Length(max: 255)]
    #[Property(example: 'Иван Петров')]
    public string $fullName;

    #[Assert\NotBlank(message: 'Укажите телефон')]
    #[Assert\Callback(['App\DTO\Validator', 'mobilePhone'])]
    #[Property(example: 9780000000)]
    public string $phone;

    #[Assert\NotBlank(message: 'Укажите email')]
    #[Assert\Email(message: 'Некорректный email')]
    #[Assert\Length(max: 255)]
    #[Property(example: 'ayl@mail.com')]
    public string $email;

    #[Assert\NotBlank(message: 'Укажите адрес доставки')]
    #[Assert\Length(max: 1000)]
    #[Property(example: 'ул. Цветочная, д. 5')]
    public string $deliveryAddress;

    #[Assert\NotBlank(message: 'Укажите город')]
    #[Assert\Length(max: 255)]
    #[Property(example: 'Симферополь')]
    public string $city;

    #[Assert\NotBlank(message: 'Укажите регион')]
    #[Assert\Length(max: 255)]
    #[Property(example: 'Крым')]
    public string $region;

    #[Assert\NotBlank(message: 'Укажите индекс')]
    #[Assert\Type(
        type: 'string',
        message: 'Индекс может быть только  int'
    )]
    #[Assert\Length(max: 10)]
    #[Property(example: '12345')]
    public string $zip;

    #[Assert\Length(max: 2000)]
    #[Property(example: 'Сообщение')]
    public ?string $comment = null;

    #[Assert\IsTrue(message: 'Необходимо согласие на обработку персональных данных')]
    #[Property(example: true)]
    public bool $pdnConsent = false;
}