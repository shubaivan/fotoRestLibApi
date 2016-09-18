<?php

namespace AppBundle\Controller\Api;

use AppBundle\Application\Photo\PhotoInterface;
use AppBundle\Controller\AbstractRestController;
use AppBundle\Entity\Photo;
use AppBundle\Exception\DeserializeException;
use AppBundle\Exception\FileUploadException;
use AppBundle\Exception\ValidatorException;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\View\View;

class FotoController extends AbstractRestController
{
    /**
     * Upload file image.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Upload file image",
     *  parameters={
     *      {"name"="file", "dataType"="file", "required"=true, "description"="upload file in S3"},
     *      {"name"="tag_ids", "dataType"="arrya<integer>", "required"=true, "description"="array tag ids"},
     *  },
     * statusCodes = {
     *      200 = "Successful",
     *      400 = "Bad request"
     * },
     *  section="File"
     * )
     * @RestView()
     *
     * @param Request $request
     *
     * @return View
     */
    public function postFileAction(Request $request)
    {
        $r =1;
        try {
            $upload = $this->getPhotoInterface()->postPhoto(
                $request->request,
                $request->files->get('file')
            );

            return $this->createSuccessResponse($upload, [Photo::GROUP_GET_PHOTO], true);

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
     * @return PhotoInterface
     */
    private function getPhotoInterface()
    {
        return $this->get('app.application.photo');
    }    
}
