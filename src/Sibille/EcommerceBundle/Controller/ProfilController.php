<?php

namespace Sibille\EcommerceBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\Core\Pagination\Pagerfanta\ContentSearchAdapter;

class ProfilController extends Controller
{
    public function indexAction()
    {        
        // lecture contenu de la page liste profil
        $contentService = $this->getRepository()->getContentService();
        $content = $contentService->loadContent( 124 );
        $location = $this->getRepository()->getLocationService()->loadLocation( 123 );
        
        // recherche de toutes les expériences 
        $query = new Query();
        $query->criterion = new Criterion\LogicalAnd(
            array( 
                new Criterion\ContentTypeIdentifier( 'experience' ),
            )
        );
        $query->sortClauses = array( new SortClause\Field('experience', 'periode', Query::SORT_DESC, 'fre-FR') );
        $result = $this->getRepository()->getSearchService()->findContent($query);
        
        $experiences = array();
        foreach($result->searchHits as $hit)
        {
            $experiences[] = $hit->valueObject;
        }
  
        // lecture fichier média 
        $cv = $contentService->loadContent( 132 )->getFields();

        return $this->render(
            'SibilleEcommerceBundle:full:profil.html.twig', array(
                'content' => $content, 
                'location' => $location,
                'experiences' => $experiences,
                'cv' => $cv
            )
        );          
    }

    
}
