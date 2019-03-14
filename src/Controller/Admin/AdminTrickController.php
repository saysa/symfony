<?php


namespace App\Controller\Admin;

use App\Entity\Trick;
use App\Entity\User;
use App\Event\AdminUploadEvent;
use App\Form\TrickType;
use App\Repository\TrickRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class AdminTrickController extends AbstractController
{
    /**
     * @var TrickRepository
     */
    private $repository;
    private $em;

    public function __construct(TrickRepository $repository, ObjectManager $em)
    {
        $this->repository = $repository;
        $this->em = $em;
    }

    /**
     * @Route("/admin", name="admin.tricks.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */

    public function index()
    {
       $tricks = $this->repository->findAll();
       return $this->render ('admin/tricks/index.html.twig', compact ('tricks'));
    }


    /**
     * @Route("/admin/create", name="admin.tricks.new")
     * @param Request $request
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function new(Request $request, AdminUploadEvent $event_dispatcher)
    {
        $trick = new Trick();
        $form = $this->createForm (TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $event_dispatcher->dispatch(AdminUploadEvent::class, null);

            $user= $this->get('security.token_storage')->getToken()->getUser(); // get the current user
            $trick->setAuthor($user);
            /** @var UploadedFile $file */
            $file = $trick->getImage();

            $fileName = $this->generateUniqueFileName().'.'.$file->guessExtension();

            try {
                $file->move(
                    $this->getParameter('images_directory'),
                    $fileName
                );
            } catch (FileException $e) {
                // ... handle exception if something happens during file upload
            }

            $trick->setImage($fileName);

            $this->em->persist($trick);
            $this->em->flush();
            $this->addFlash('success', 'Le trick a bien été créé');

            if(!$form->isSubmitted() && !$form->isValid())
            {
                $this->addFlash('error', 'Le trick n\'a pas pu être créé');
            }
            return $this->redirectToRoute('trick.index');
        }

        return $this->render('admin/tricks/new.html.twig', [
            'trick' => $trick,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/{id}", name="admin.tricks.edit", methods="GET|POST")
     * @param Trick $trick
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function edit(Trick $trick, Request $request)
    {
        $form = $this->createForm (TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          #  $trick->setImage(
           #     new File($this->getParameter('images_directory').'/'.$trick->getImage())
          #  );

            $this->em->flush();
            $this->addFlash('success', 'Le trick a bien été modifié');
            return $this->redirectToRoute('trick.index');
        }

       return $this->render ('admin/tricks/edit.html.twig', [
           'trick' => $trick,
           'form' => $form->createView()
       ]);
    }


    /**
     * @Route("/admin/{id}", name="admin.tricks.delete", methods="DELETE")
     * @param Request $request
     * @param Trick $trick
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Trick $trick)
    {
        $form = $this->createDeleteForm($trick);
        $form->handleRequest($request);

        if ($trick) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($trick);
            $em->flush();
            $this->addFlash('success', 'Le trick a bien été supprimé');
        }

        return $this->redirectToRoute('trick.index');
    }

    /**
     * @param Trick $trick
     * @return mixed
     */
    private function createDeleteForm(Trick $trick)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('admin.tricks.delete', array('id' => $trick->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }


    private function generateUniqueFileName()
    {
        // md5() reduces the similarity of the file names generated by
        // uniqid(), which is based on timestamps
        return md5(uniqid());
    }
}