<?php

namespace AppBundle\Controller\Api;

use AppBundle\Application\Photo\PhotoInterface;
use AppBundle\Application\Tags\TagsInterface;
use AppBundle\Controller\AbstractRestController;
use AppBundle\Entity\Photo;
use AppBundle\Entity\Tags;
use AppBundle\Exception\DeserializeException;
use AppBundle\Exception\FileUploadException;
use AppBundle\Exception\ValidatorException;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\View\View;

class TagsController extends AbstractRestController
{
    /**
     * Create new tag.
     *
     * @ApiDoc(
     *      resource = true,
     *      description = "Create new tag",
     *      parameters={
     *          {"name"="tag", "dataType"="string", "required"=true, "description"="name for new tag"},
     *      },
     *      statusCodes = {
     *          200 = "Successful",
     *          400 = "Bad request"
     *      },
     *      section="Tag"
     * )
     * @RestView()
     *
     * @param Request $request
     *
     * @return View
     */
    public function postTagAction(Request $request)
    {
        $r = 1;
        try {
            $tag = $this->getTagInterface()->postTagEntity(
                $request->request
            );

            return $this->createSuccessResponse($tag, [Tags::GROUP_GET_TAG], true);

        } catch (FileUploadException $e) {
            $view = $this->view(['message' => $e->getMessage()], self::HTTP_STATUS_CODE_BAD_REQUEST);
        } catch (ValidatorException $e) {
            $view = $this->view(['message' => $e->getErrors()], self::HTTP_STATUS_CODE_BAD_REQUEST);
        } catch (DeserializeException $e) {
            $view = $this->view(['message' => $e->getMessage()], self::HTTP_STATUS_CODE_BAD_REQUEST);
        } catch (\Exception $e) {
            $view = $this->view(['message' => self::SERVER_ERROR], self::HTTP_STATUS_CODE_INTERNAL_ERROR);
        }

        return $this->handleView($view);
    }

    /**
     * @return TagsInterface
     */
    private function getTagInterface()
    {
        return $this->get('app.application.tags');
    }    
}
