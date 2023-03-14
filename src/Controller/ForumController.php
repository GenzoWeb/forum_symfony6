<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ForumController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        return $this->render('home/home.html.twig', []);
    }

    #[Route('/forum', name: 'forum')]
    public function forumHome(EntityManagerInterface $manager): Response
    {
        $categories = $manager->getRepository(Category::class)->findAll();
        // $messages = $manager->getRepository(Message::class)->findLatest(10);
        $messages = $manager->getRepository(Message::class)->findBy([],['published' => 'DESC'], 10);
        $subjects = [];
        $subjectCounts = [];
    
        foreach ($messages as $message) {
            $subject = $message->getSubject();
    
            if (!isset($subjects[$subject->getId()])) {
                $subjects[$subject->getId()] = $subject;
                $subjectCounts[$subject->getId()] = 1;
            } else {
                $subjectCounts[$subject->getId()]++;
            }
        }

        return $this->render('forum/forumHome.html.twig', [
            'categories' => $categories,
            'messages' => $messages,
            'subjects' => $subjects,
            'subjectCounts' => $subjectCounts,
        ]);
    }
}
