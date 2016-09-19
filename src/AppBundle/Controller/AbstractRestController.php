<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Request;

class AbstractRestController extends FOSRestController
{
    const HTTP_STATUS_CODE_OK             = 200;
    const HTTP_STATUS_CODE_BAD_REQUEST    = 400;
    const HTTP_STATUS_CODE_INTERNAL_ERROR = 500;
    const DATA_MESSAGE                    = 'message';
    const SUCCESS                         = 'success';
    const SERVER_ERROR                    = 'Server Error';
    const PARAM_DATE_FROM                 = 'date_from';
    const PARAM_DATE_TO                   = 'date_to';
    
    /**
     * @param $data
     * @param array|null $groups
     * @param boolean|null $withEmptyField
     * @return View
     */
    protected function createSuccessResponse($data, array $groups = null, $withEmptyField = null)
    {
        $context = SerializationContext::create()->enableMaxDepthChecks();
        if ($groups) {
            $context->setGroups($groups);
        }

        if ($withEmptyField) {
            $context->setSerializeNull(true);
        }

        return View::create()
            ->setStatusCode(self::HTTP_STATUS_CODE_OK)
            ->setData($data)
            ->setSerializationContext($context);
    }

    /**
     * @param string $data
     * @return View
     */
    protected function createSuccessStringResponse($data)
    {
        return View::create()
            ->setStatusCode(self::HTTP_STATUS_CODE_OK)
            ->setData([self::DATA_MESSAGE => $data]);
    }
}
