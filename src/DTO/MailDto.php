<?php
declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final class MailDto
{
    #[Assert\NotBlank(message: 'Укажите свое имя')]
    #[Assert\Length(max: 255)]
    public string $name;

    #[Assert\NotBlank(message: 'Укажите email')]
    #[Assert\Email(message: 'Некорректный email')]
    #[Assert\Length(max: 255)]
    public string $contact;

    #[Assert\NotBlank(message: 'Укажите комментарий')]
    #[Assert\Length(max: 2000)]
    public ?string $message = null;
}