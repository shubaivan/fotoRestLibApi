<?php

namespace AppBundle\Controller\Api;

use AppBundle\Application\Photo\PhotoInterface;
use AppBundle\Controller\AbstractRestController;
use AppBundle\Entity\Photo;
use AppBundle\Exception\DeserializeException;
use AppBundle\Exception\FileUploadException;
use AppBundle\Exception\InvalidDateRangeException;
use AppBundle\Exception\NotExistEntityException;
use AppBundle\Exception\ValidatorException;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\HttpFoundation\Request;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\View as RestView;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations\QueryParam;

class PhotoController extends AbstractRestController
{
    /**
     * get photo by parameters.
     *
     * @ApiDoc(
     * resource = true,
     * description = "get photo by parameters.",
     *  parameters={
     *      {"name"="tag_ids", "dataType"="arrya<integer>", "required"=true, "description"="array tag ids"},
     *      {"name"="date_from", "dataType"="date", "required"=false, "description"="date from period, example - 2016-09-01 00:00:00 (Y-m-d h:i:s)"},
     *      {"name"="date_to", "dataType"="date", "required"=false, "description"="date to period, example - 2016-09-01 00:00:00 (Y-m-d h:i:s)"}     
     *  },
     * statusCodes = {
     *      200 = "Successful",
     *      400 = "Bad request"
     * },
     *  section="File"
     * )
     * @RestView()
     *
     * @QueryParam(name="count", requirements="\d+", default="10", description="Count project at one page")
     * @QueryParam(name="page", requirements="\d+", default="1", description="Number of page to be shown")
     * @QueryParam(name="sort_by", strict=true, requirements="^[a-zA-Z]+", default="createdAt", description="Sort by", nullable=true)
     * @QueryParam(name="sort_order", strict=true, requirements="^[a-zA-Z]+", default="DESC", description="Sort order", nullable=true)
     *
     * @param ParamFetcher $paramFetcher
     * @param Request $request
     *
     * @return View
     */
    public function getFilesAction(ParamFetcher $paramFetcher, Request $request)
    {
        try {
            $photos = $this->getPhotoInterface()->getPhotoByParameters(
                $request->query,
                $paramFetcher,
                $request->query->get(self::PARAM_DATE_FROM),
                $request->query->get(self::PARAM_DATE_TO)
            );

            return $this->createSuccessResponse($photos, [Photo::GROUP_GET_PHOTO], true);

        } catch (InvalidDateRangeException $e) {
            $view = $this->view(['message' => $e->getMessage()], self::HTTP_STATUS_CODE_BAD_REQUEST);
        } catch (\Exception $e) {
            $view = $this->view(['message' => $e->getMessage()], self::HTTP_STATUS_CODE_INTERNAL_ERROR);
        }

        return $this->handleView($view);
    }
    
    /**
     * Upload file photo.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Upload file photo",
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
     * Put exist photo.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Put exist photo",
     *  parameters={
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
     * @param integer $id
     *
     * @return View
     */
    public function putFileAction(Request $request, $id)
    {
        try {
            $upload = $this->getPhotoInterface()->putPhoto(
                $request->request,
                $id
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
     * Remove exist photo.
     *
     * @ApiDoc(
     * resource = true,
     * description = "Remove exist photo",
     *  parameters={
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
     * @param integer $id
     *
     * @return View
     */
    public function deletedFileAction($id)
    {
        try {
            $this->getPhotoInterface()->removeEntity(
                $id
            );

            return $this->createSuccessResponse([self::SUCCESS], [], true);

        } catch (NotExistEntityException $e) {
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
