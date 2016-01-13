<?php

namespace Sibille\EcommerceBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\Core\Pagination\Pagerfanta\ContentSearchAdapter;
use Pagerfanta\Pagerfanta;

class PortfolioController extends Controller
{
    public function indexAction()
    {        
        $contentService = $this->getRepository()->getContentService();
        $content = $contentService->loadContent( 74 );
        $location = $this->getRepository()->getLocationService()->loadLocation( 73 );
        
        $query = new Query();
        $query->criterion = new Criterion\LogicalAnd(
            array(
                new Criterion\ContentTypeIdentifier( 'projet' )
            )
        );
        $query->sortClauses = array( new SortClause\Field('projet', 'annee', Query::SORT_DESC, 'fre-FR') );
        
        $pager = new Pagerfanta(
            new ContentSearchAdapter( $query, $this->getRepository()->getSearchService() )
        );
        $pager->setMaxPerPage( 12 );
        $pager->setCurrentPage( $this->getRequest()->get( 'page', 1 ) );        

        $contentTypeService = $this->getRepository()->getContentTypeService();
        $contentType = $contentTypeService->loadContentTypeByIdentifier( 'projet' );
        $categories = $contentType->getFieldDefinition('categorie')->fieldSettings['options'];

        return $this->render(
            'SibilleEcommerceBundle:full:portfolio.html.twig', array(
                'content' => $content, 
                'location' => $location,
                'total' => $pager->getNbResults(),
                'projets' => $pager, 
                'categories' => $categories
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
        $contentType = $contentTypeService->loadContentTypeByIdentifier( 'projet' );
        $fieldDefinition = $contentType->getFieldDefinition('techno');
        $field = $content->getField( $fieldDefinition->identifier )->value->values;
          
        // Liste de tous les id-projets
        $query = new Query();
        $query->criterion = new Criterion\LogicalAnd(
            array(
                new Criterion\ContentTypeIdentifier( 'projet' )
            )
        );
        $query->sortClauses = array( new SortClause\Field('projet', 'annee', Query::SORT_DESC, 'fre-FR') );
        
        $result = $this->getRepository()->getSearchService()->findContent($query);
        $locationIds = array();
        foreach($result->searchHits as $hit) {
            $locationIds[] = $hit->valueObject->contentInfo->mainLocationId;
        }
                
        // recherche du projet précédent 
        $res = 0;
        reset($locationIds);
        end($locationIds);
        $prev = key($locationIds);
        $prevId = 0;
        do {
            $cur = key($locationIds);
            $res = prev($locationIds);
        } while ( ($locationIds[$cur] != $locationId) );

        if( $res ) {
            $prev = key($locationIds);
            $prevId = $locationIds[$prev];
        }
         
        // recherche du projet suivant 
        $res = 0;
        reset($locationIds);
        $next = key($locationIds);
        $nextId = 0;
        do {
            $cur = key($locationIds);
            $res = next($locationIds);
        } while ( ($locationIds[$cur] != $locationId) );

        if( $res ) {
            $next = key($locationIds);
            $nextId = $locationIds[$next];
        }

        $params += array( 'technos' => $field, 'prevId' => $prevId, 'nextId' => $nextId );
        $response = $this->get( 'ez_content' )->viewLocation( $locationId, $viewType, $layout, $params );
    
        return $response;
    }    
    
}
