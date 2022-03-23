<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Repository\CommentRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    private $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }
    /**
     * @Route("/admin/comments", name="app_comments")
     */
    public function comments(CommentRepository $comments): Response
    {
        return $this->render('comment/index.html.twig', ['comments' => $comments->findBy([],['createdAt' => 'DESC'])
        ]);
    }
    /**
     * @Route("/admin/comment/{id}/delete", name="app_comment_delete")
     */
    public function deleteComment(Comment $comment, Request $request, CommentRepository $commentRepository): Response
    {
        $comment = $commentRepository->find($comment);

        if($request->request->count()>0) {

            $commentRepository->remove($comment);
            $message = " The account of ".$comment->getUser()->getFirstname()." ".$comment->getUser()->getLastname()." has deleted successfully !!";
            $this->addFlash('success',$message);
            return $this->redirectToRoute('app_comments');
        }
        
        return $this->render('comment/delete.html.twig', ['comment' => $comment
        ]);
    }

    public function addComment (Comment $comment, Trick $trick) :void
    {
        $comment->setCreatedAt(new DateTimeImmutable('now'));
        $comment->setTrick($trick);
        $comment->setUser($this->getUser());
        $this->commentRepository->add($comment);
        $this->addFlash('success', 'Comment successfully added !!');
    }
    
}
