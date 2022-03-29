<?php

namespace App\Controller;

use App\Entity\TypeTrick;
use App\Form\DeleteTypeFormType;
use App\Form\TypeTrickFormType;
use App\Repository\TypeTrickRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TypeTrickController extends AbstractController
{
    /**
     * @Route("/admin/type", name="app_type")
     */
    public function adminTypeTrick(TypeTrickRepository $types): Response
    {
        return $this->render('type/index.html.twig', ['types' => $types->findAll()]);
    }

    /**
     * @Route("/admin/type/add", name="app_type_add")
     * @Route("/admin/type/{id}/edit", name="app_type_edit")
     */
    public function adminTypeAdd(TypeTrick $type=null, Request $request, TypeTrickRepository $typeRepository): Response
    {
        if (!$type) {
            $type= new TypeTrick();
        }
        // Create form for type
        $form = $this->createForm(TypeTrickFormType::class, $type);
        $form->handleRequest($request);
        // If form is submitted, save type
        if ($form->isSubmitted() && $form->isValid()) {
            $typeRepository->add($type);
            $this->addFlash('success', "The type of has changed successfully !!");
            return $this->redirectToRoute('app_type');
        }

        return $this->render('type/add.html.twig', ['typeTrickForm' => $form->createView(), 'editMode'=>$type->getId()!== null]);
    }

    /**
     * @Route("/admin/type/{id}/delete", name="app_type_delete")
     */
    public function adminTypeDelete(TypeTrick $type, Request $request, TypeTrickRepository $typeRepository): Response
    {
        // Create Delete Trick form for type
        $form = $this->createForm(DeleteTypeFormType::class, $type);
        $form->handleRequest($request);
        // When delete Trick Form is submitted
        if ($form->isSubmitted() && $form->isValid()) {
            // Delete all tricks of this type
            foreach ($type->getTricks() as $trickRemove) {
                $type->removeTrick($trickRemove);
            }

            // Delete type
            $typeRepository->remove($type);
            $message = " The type of ".$type->getName()." has deleted successfully !!";
            $this->addFlash('success', $message);
            return $this->redirectToRoute('app_type');
        }

        return $this->render('type/delete.html.twig', ['type' => $type, 'deleteForm' => $form->createView()]);
    }
}
