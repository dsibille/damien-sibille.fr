<?php

namespace Sibille\EcommerceBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;

class BlogController extends Controller
{
    public function indexAction()
    {        
        $contentService = $this->getRepository()->getContentService();
        $content = $contentService->loadContent( 61 );
        
        $query = new Query();
        $query->criterion = new Criterion\LogicalAnd(
            array(
                new Criterion\ParentLocationId( 62 )
            )
        );
        $query->sortClauses = array( new SortClause\DatePublished( Query::SORT_DESC ) );
        
        $result = $this->getRepository()->getSearchService()->findContent($query);
        $articles = array();
        foreach($result->searchHits as $hit)
        {
            $articles[] = $hit->valueObject;
        }
                
        return $this->render(
                'SibilleEcommerceBundle:full:blog.html.twig',
                array('content' => $content, 'articles' => $articles)
        );         
    }
    
    
}
