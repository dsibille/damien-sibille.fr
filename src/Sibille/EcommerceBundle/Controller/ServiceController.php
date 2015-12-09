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
        
        return $this->render(
                'SibilleEcommerceBundle:full:services.html.twig',
                array('content' => $content, 'services' => $services)
        );         
    }
    
    
}
