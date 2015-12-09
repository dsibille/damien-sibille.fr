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
                new Criterion\ContentTypeIdentifier( 'article' )
            )
        );
        $query->sortClauses = array( new SortClause\DatePublished( Query::SORT_DESC ) );
        
        $result = $this->getRepository()->getSearchService()->findContent($query);
        $articles = array();
        foreach($result->searchHits as $hit)
        {
            $articles[] = $hit->valueObject;
        }
                
        $contentTypeService = $this->getRepository()->getContentTypeService();
        $contentType = $contentTypeService->loadContentTypeByIdentifier( 'article' );
        $categories = $contentType->getFieldDefinition('category')->fieldSettings['options'];

        return $this->render(
                'SibilleEcommerceBundle:full:blog.html.twig',
                array('content' => $content, 'articles' => $articles, 'categories' => $categories)
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
        $contentType = $contentTypeService->loadContentTypeByIdentifier( 'article' );
        $categories = $contentType->getFieldDefinition('category')->fieldSettings['options'];
          
        $params += array( 'categories' => $categories );
        $response = $this->get( 'ez_content' )->viewLocation( $locationId, $viewType, $layout, $params );
    
        return $response;
    }   
    
    
}
