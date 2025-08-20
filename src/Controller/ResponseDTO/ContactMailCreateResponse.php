<?php
declare(strict_types=1);

namespace App\Controller\ResponseDTO;

use App\Entity\Category;
use App\Entity\ContactMail;
use App\Entity\Product;
use Attribute;
use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Attribute\Model;

#[Attribute(Attribute::TARGET_METHOD)]
final class ContactMailCreateResponse extends OA\Post
{
    public function __construct()
    {
        parent::__construct(
            summary: 'Отправка письма на почту',
            tags: ['Client'],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Отправка письма на почту',
                ),
                new OA\Response(response: 401, description: 'Не авторизован'),
            ],
        );
    }
}