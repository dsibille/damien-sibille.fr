<?php

namespace Sibille\EcommerceBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;

class ServiceController extends Controller
{
    public function indexAction()
    {        
        $contentService = $this->getRepository()->getContentService();
        $content = $contentService->loadContent( 73 );
        
        // LISTE DES SERVICES
        $query = new Query();
        $query->criterion = new Criterion\LogicalAnd(
            array(
               new Criterion\ContentTypeIdentifier( 'service' )
            )
        );
        $query->sortClauses = array( new SortClause\LocationPriority( Query::SORT_ASC ) );
        
        $result = $this->getRepository()->getSearchService()->findContent($query);
        $services = array();
        foreach($result->searchHits as $hit)
        {
                $services[] = $hit->valueObject;
        }
        
        // SLIDER LOGOS CLIENTS
        $field = $content->getField('clients');
        $relations = $field->attribute('value')->destinationContentIds;        
        $clients = array();
        foreach ($relations as $relation) {
            $clients[] = $contentService->loadContent( $relation );
        }

        return $this->render(
                'SibilleEcommerceBundle:full:services.html.twig',
                array('content' => $content, 'services' => $services, 'clients' => $clients)
        );         
    }

}
