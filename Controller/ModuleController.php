<?php

/**
 * This controller is meant to be used for demonstration
 * Ultimately, you will want to use these snippets in your system.
 *
 * If you want to try these actions, include the routing file of this bundle in your
 * installation.
 * Example in app/config/routing_dev.yml
 * 
 * CornichonForumBundle:
 *     resource: "@CornichonForumBundle/Resources/config/routing.yml"
 *     prefix:   /forum-test
 */

namespace Cornichon\ForumBundle\Controller;

use Cornichon\ForumBundle\Entity\Board;
use Cornichon\ForumBundle\Form\CreateBoardType;

use Cornichon\ForumBundle\Entity\Topic;
use Cornichon\ForumBundle\Form\CreateTopicType;

use Cornichon\ForumBundle\Entity\Message;
use Cornichon\ForumBundle\Form\CreateMessageType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ModuleController extends Controller
{

    public function listTopicsAction($boardId = null)
    {
        $em = $this->get('doctrine')->getEntityManager();

        if ($boardId === null) {
            $topics = $this->get('cornichon.forum.topic')->getLatestTopics(0, 10);
        }
        else {
            $board = $em->getRepository($this->getParameter('cornichon_forum.board_repository.class'))->find($boardId);

            if (!$board instanceof Board) {
                throw $this->createNotFoundException("No Board Found");
            }

            $topics = $this->get('cornichon.forum.topic')->getLatestTopicsByBoard($board, 0, 10);
        }

        return $this->render('CornichonForumBundle:Module:listTopics.html.twig', array(
            'topics' => $topics
        ));
    }

    public function listBoardsAction()
    {
        $em = $this->get('doctrine')->getEntityManager();

        return $this->render('CornichonForumBundle:Module:listBoards.html.twig', array(
        ));
    }

    public function listMessagesAction($topicId)
    {
        $em = $this->get('doctrine')->getEntityManager();

        $topic = $em->getRepository($this->getParameter('cornichon_forum.topic_repository.class'))->find($topicId);

        if (!$topic instanceof Topic) {
            throw $this->createNotFoundException("No Topic Found");
        }

        $messages = $this->get('cornichon.forum.message')->getLatestMessagesByTopic($topic, 0, 10);

        return $this->render('CornichonForumBundle:Module:listMessages.html.twig', array(
            'messages' => $messages
        ));
    }

    public function newMessageAction($topicId)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $em = $this->get('doctrine')->getEntityManager();

        $topic = $em->getRepository($this->getParameter('cornichon_forum.topic_repository.class'))->find($topicId);

        if (!$topic instanceof Topic) {
            throw $this->createNotFoundException("No Topic Found");
        }

        $request = $this->get('request');

        $message = new Message();

        $form = $this->createForm(new CreateMessageType(), $message);

        if ($request->getMethod() === 'POST') {
            $form->bind($request);
            if ($form->isValid() === true) {
                $message->setTopic($topic);
                $this->get('cornichon.forum.message')->save($message);
            }
        }

        return $this->render('CornichonForumBundle:Module:newMessage.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function newTopicAction($boardId)
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $em = $this->get('doctrine')->getEntityManager();

        $board = $em->getRepository($this->getParameter('cornichon_forum.board_repository.class'))->find($boardId);

        if (!$board instanceof Board) {
            throw $this->createNotFoundException("No Board Found");
        }

        $request = $this->get('request');

        $topic = new Topic();

        $form = $this->createForm(new CreateTopicType(), $topic);

        if ($request->getMethod() === 'POST') {
            $form->bind($request);
            if ($form->isValid() === true) {
                $user = $this->get('security.context')->getToken()->getUser();

                $topic->setBoard($board);

                $this->get('cornichon.forum.topic')->save($topic);

                $message = new Message();

                $message->setBody($form['body']->getData());
                $message->setTopic($topic);

                $this->get('cornichon.forum.message')->save($message);
            }
        }

        return $this->render('CornichonForumBundle:Module:newTopic.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function newBoardAction()
    {
        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') === false) {
            throw new \Symfony\Component\Security\Core\Exception\AccessDeniedException();
        }

        $request = $this->get('request');

        $board = new Board();

        $form = $this->createForm(new CreateBoardType(), $board);

        if ($request->getMethod() === 'POST') {
            $form->bind($request);
            if ($form->isValid() === true) {
                $this->get('cornichon.forum.board')->save($board);
            }
        }

        return $this->render('CornichonForumBundle:Module:newBoard.html.twig', array(
            'form' => $form->createView()
        ));
    }
}