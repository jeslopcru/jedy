<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Content;
use AppBundle\Form\ContentForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/admin/content")
 */
class ContentController extends Controller
{
    /**
     * @Route("s/", name="admin_content_home")
     */
    public function indexAction()
    {
        $contents = $this->getDoctrine()->getRepository('AppBundle:Content')->findBy(
            ['locale' => $this->container->get('locales')->getLocaleActive()]
        );

        return $this->render(
            'admin/content/admin_content_index.html.twig',
            [
                'contents' => $contents,
            ]
        );
    }

    /**
     * @Route("/{type}/new/", name="admin_content_new", requirements={"type" = "page|post"} )
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, $type)
    {
        $content = new Content($this->get('locales')->getLocaleActive());

        $form = $this->createForm(ContentForm::class, $content, ['type' => $type]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($content);
            $em->flush();

            $this->addFlash('success-content', 'created_successfully');

            return $this->redirectToRoute('admin_content_home');
        }

        return $this->render(
            'admin/content/admin_content_new.html.twig',
            [
                'form' => $form->createView(),
                'type' => $type,
            ]
        );
    }

    /**
     * @Route("/{id}/edit/", name="admin_content_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Content $content, Request $request)
    {
        $form = $this->createForm(ContentForm::class, $content, ['type' => $content->getType()]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($content);
            $em->flush();

            $this->addFlash('success-content', 'created_successfully');

            return $this->redirectToRoute('admin_content_home');
        }

        return $this->render(
            'admin/content/admin_content_edit.html.twig',
            [
                'form' => $form->createView(),
                'type' => $content->getType(),
            ]
        );
    }

    /**
     * @Route("/{id}/translations/", name="admin_content_translations")
     * @Method("GET")
     */
    public function translationsAction(Content $content)
    {
        return $this->render(
            'admin/content/admin_content_translations.html.twig',
            [
                'content' => $content,
                'translations' => $this->get('locales')->getLocales(),
                'active' => $this->get('locales')->getLocaleActive(),
            ]
        );
    }

    /**
     * @Route("/{id}/translations/add/{localeContent}/{localeTranslation}", name="admin_content_translation_add")
     * @Method({"GET", "POST"})
     */
    public function addTranslationAction(Request $request, Content $content, $localeTranslation)
    {
        $newContent = new Content($localeTranslation);

        $form = $this->createForm(ContentForm::class, $newContent, ['type' => $content->getType(), 'parent' => $content]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($newContent);
            $em->flush();

            $this->addFlash('success-content', 'created_successfully');

            return $this->redirectToRoute('admin_content_translations', ['id' => $content->getId()]);
        }

        return $this->render(
            'admin/content/admin_content_new.html.twig',
            [
                'form' => $form->createView(),
                'content' => $content,
                'type' => $content->getType()
            ]
        );
    }
}