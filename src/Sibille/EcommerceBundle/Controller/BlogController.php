<?php

namespace Sibille\EcommerceBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\Core\Pagination\Pagerfanta\ContentSearchAdapter;
use Pagerfanta\Pagerfanta;

class BlogController extends Controller
{
    public function indexAction( $categorie = 'toutes' )
    {        
        $contentService = $this->getRepository()->getContentService();
        $content = $contentService->loadContent( 61 );
        $location = $this->getRepository()->getLocationService()->loadLocation( 62 );
        
        // récupération des catégories du blog
        $contentTypeService = $this->getRepository()->getContentTypeService();
        $contentType = $contentTypeService->loadContentTypeByIdentifier( 'article' );
        $options = $contentType->getFieldDefinition('categorie_article')->fieldSettings['options'];

        // recherche de tous les posts 
        $query = new Query();
        if ($categorie == 'toutes') {
            $query->criterion = new Criterion\LogicalAnd(
                array( 
                    new Criterion\ContentTypeIdentifier( 'article' ),
                )
            );
        } else {
            $query->criterion = new Criterion\LogicalAnd(
                array( 
                    new Criterion\ContentTypeIdentifier( 'article' ),
                    new Criterion\Field( 'categorie_article', Criterion\Operator::CONTAINS, $categorie )
                )
            );
        }
        $query->sortClauses = array( new SortClause\Field('article', 'publish_date', Query::SORT_DESC, 'fre-FR') );

        $pager = new Pagerfanta(
            new ContentSearchAdapter( $query, $this->getRepository()->getSearchService() )
        );
        $pager->setMaxPerPage( 5 );
        $pager->setCurrentPage( $this->getRequest()->get( 'page', 1 ) );        
  
        return $this->render(
            'SibilleEcommerceBundle:full:blog.html.twig', array(
                'content' => $content, 
                'location' => $location,
                'total' => $pager->getNbResults(),
                'articles' => $pager, 
                'categories' => $options
            )
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
        $options = $contentType->getFieldDefinition('categorie_article')->fieldSettings['options'];
          
        $params += array( 'categories' => $options );
        $response = $this->get( 'ez_content' )->viewLocation( $locationId, $viewType, $layout, $params );
    
        return $response;
    }   
    
    
}
