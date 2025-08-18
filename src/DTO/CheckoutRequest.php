<?php
declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class CheckoutRequest
{
    #[Assert\NotBlank(message: 'Укажите имя получателя')]
    #[Assert\Length(max: 255)]
    public string $fullName;

    #[Assert\NotBlank(message: 'Укажите телефон')]
    #[Assert\Length(max: 50)]
    public string $phone;

    #[Assert\NotBlank(message: 'Укажите email')]
    #[Assert\Email(message: 'Некорректный email')]
    #[Assert\Length(max: 255)]
    public string $email;

    #[Assert\NotBlank(message: 'Укажите адрес доставки')]
    #[Assert\Length(max: 1000)]
    public string $deliveryAddress;

    #[Assert\NotBlank(message: 'Укажите город')]
    #[Assert\Length(max: 255)]
    public string $city;

    #[Assert\NotBlank(message: 'Укажите регион')]
    #[Assert\Length(max: 255)]
    public string $region;

    #[Assert\NotBlank(message: 'Укажите индекс')]
    #[Assert\Length(max: 255)]
    public string $zip;


    #[Assert\Length(max: 2000)]
    public ?string $comment = null;

    // чекбокс на согласие с политикой (можешь не хранить в БД, но валидировать)
    #[Assert\IsTrue(message: 'Необходимо согласие на обработку персональных данных')]
    public bool $pdnConsent = false;

    // необязательная подписка на рассылку
    public bool $newsletterOptIn = false;
}