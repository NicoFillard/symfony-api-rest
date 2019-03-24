<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations as FOSRest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class CategoryController extends FOSRestController
{
    /**
     * @FOSRest\Get("api/categories")
     *
     * @param ObjectManager $manager
     *
     * @return Response
     */
    public function getCategorysAction(ObjectManager $manager)
    {
        $categoryRepository = $manager->getRepository(Category::class);
        $categorys = $categoryRepository->findAll();

        return $this->json($categorys, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Get("api/category/{id}")
     *
     * @param ObjectManager $manager
     * @param $id
     *
     * @return Response
     */
    public function getCategoryAction(ObjectManager $manager, $id)
    {
        $categoryRepository = $manager->getRepository(Category::class);
        $category = $categoryRepository->find($id);

        if (!$category instanceof Category) {
            return $this->json([
                'success' => false,
                'error' => 'Category not found'
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json($category, Response::HTTP_OK);
    }

    /**
     * @FOSRest\Post("api/categories")
     *
     * @ParamConverter("category", converter="fos_rest.request_body")
     *
     * @param ObjectManager $manager
     * @param Category $category
     * @param ValidatorInterface $validator
     *
     * @return Response
     */
    public function postCategoryAction(ObjectManager $manager, Category $category, ValidatorInterface $validator)
    {
        $errors = $validator->validate($category);

        if (!count($errors)) {
            $manager->persist($category);
            $manager->flush();

            return $this->json($category, Response::HTTP_CREATED);
        } else {
            return $this->json([
                'success' => false,
                'error' => $errors[0]->getMessage().' ('.$errors->getPropertyPath().')'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    /**
     * @FOSRest\Delete("api/category/{id}")
     *
     * @param ObjectManager $manager
     * @param $id
     *
     * @return Response
     */
    public function deleteCategoryAction(ObjectManager $manager, $id)
    {
        $categoryRepository = $manager->getRepository(Category::class);
        $category = $categoryRepository->find($id);

        if ($category instanceof Category) {
            $manager->remove($category);
            $manager->flush();
            return $this->json([
                'success' => true,
            ], Response::HTTP_OK);
        } else {
            return $this->json([
                'success' => false,
                'error' => 'Category not found'
            ], Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @FOSRest\Put("/api/category/{id}")
     *
     * @param Request $request
     * @param int $id
     * @param ObjectManager $manager
     * @param ValidatorInterface $validator
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function updateCategoryAction(Request $request, int $id, ObjectManager $manager, ValidatorInterface $validator)
    {
        $categoryRepository = $manager->getRepository(Category::class);
        $existingCategory   = $categoryRepository->find($id);

        if (!$existingCategory instanceof Category) {
            return $this->json([
                'success' => false,
                'error'   => 'Category not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(CategoryType::class, $existingCategory);
        $form->submit($request->request->all());

        $errors = $validator->validate($existingCategory);

        if (!count($errors)) {
            $manager->persist($existingCategory);
            $manager->flush();

            return $this->json($existingCategory, Response::HTTP_CREATED);
        } else {
            return $this->json([
                'success' => false,
                'error'   => $errors[0]->getMessage() . ' (' . $errors[0]->getPropertyPath() . ')'
            ], Response::HTTP_BAD_REQUEST);
        }
    }

}
