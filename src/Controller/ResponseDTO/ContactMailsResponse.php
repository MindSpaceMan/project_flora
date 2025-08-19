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
final class ContactMailsResponse extends OA\Get
{
    public function __construct()
    {
        parent::__construct(
            summary: 'Письма на почту владельца',
            tags: ['Mail'],
            responses: [
                new OA\Response(
                    response: 200,
                    description: 'Письма на почту владельца',
                    content: new OA\JsonContent(
                        type: 'array',
                        items: new OA\Items(ref: new Model(type: ContactMail::class, groups: ['contact:read']))
                    )
                ),
                new OA\Response(response: 401, description: 'Не авторизован'),
            ],
        );
    }
}