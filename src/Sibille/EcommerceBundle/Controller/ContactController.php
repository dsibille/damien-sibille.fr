<?php

namespace Sibille\EcommerceBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use Symfony\Component\Validator\Constraints\NotBlank;
use Sibille\EcommerceBundle\Form\ContactType;

class ContactController extends Controller
{
    public function indexAction()
    {        
        $contentService = $this->getRepository()->getContentService();
        $content = $contentService->loadContent( 64 );

        $form = $this->createForm(new ContactType());

        $request = $this->getRequest();
        if ($request->getMethod() == 'POST') {            
            $form->bind($request);

            if ($form->isValid()) {

                $this->get('email')->envoyerMessage($form);
                $request->getSession()->getFlashBag()->add('info', 'Votre message a bien été envoyé.');
                    
            }
        } 
        
        return $this->render('SibilleEcommerceBundle:full:contact.html.twig', array(
            'content' => $content, 
            'form' => $form->createView()
            ));              
        
    }

}
