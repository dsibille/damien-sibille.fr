<?php

namespace Sibille\EcommerceBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;

class PortfolioController extends Controller
{
    public function indexAction()
    {        
        $contentService = $this->getRepository()->getContentService();
        $content = $contentService->loadContent( 74 );
        
        $query = new Query();
        $query->criterion = new Criterion\LogicalAnd(
            array(
                new Criterion\ParentLocationId( 73 )
            )
        );
        $query->sortClauses = array( new SortClause\LocationPriority( Query::SORT_ASC ) );
        
        $result = $this->getRepository()->getSearchService()->findContent($query);
        $projets = array();
        foreach($result->searchHits as $hit)
        {
            $projets[] = $hit->valueObject;
        }
                
        return $this->render(
                'SibilleEcommerceBundle:full:portfolio.html.twig',
                array('content' => $content, 'projets' => $projets)
        );         
    }
    
    public function viewLocationAction( $locationId, $viewType, $layout = false, array $params = array() )
    {
        // récupération du projet en cours
        $repository = $this->getRepository();
        $location = $repository->getLocationService()->loadLocation( $locationId );
        $content = $repository->getContentService()->loadContentByContentInfo( $location->getContentInfo() );

        // récupération des mots clés dans le champ techno
        $contentTypeService = $this->getRepository()->getContentTypeService();
        $contentType = $contentTypeService->loadContentTypeByIdentifier( 'projet' );
        $fieldDefinition = $contentType->getFieldDefinition('techno');
        $field = $content->getField( $fieldDefinition->identifier )->value->values;
          
        $params += array( 'technos' => $field );
        $response = $this->get( 'ez_content' )->viewLocation( $locationId, $viewType, $layout, $params );
    
        return $response;
    }    
    
    
}
