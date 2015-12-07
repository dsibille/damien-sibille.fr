<?php

namespace Sibille\EcommerceBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\Core\FieldType\RelationList;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;

class DefaultController extends Controller
{
    public function indexAction()
    {
        $rootLocationId = 2;
        $repository = $this->getRepository();
        $contentService = $repository->getContentService();
        $location = $repository->getLocationService()->loadLocation( $rootLocationId );
        $content = $repository->getContentService()->loadContentByContentInfo( $location->getContentInfo() );
                        
        // SLIDER
        $field = $content->getField('slider');
        $relations = $field->attribute('value')->destinationContentIds;        
        $slides = array();
        foreach ($relations as $relation) {
            $slides[] = $contentService->loadContent( $relation );
        }

        // SERVICES
        $field = $content->getField('services');
        $relations = $field->attribute('value')->destinationContentIds;
        $services = array();
        foreach ($relations as $relation) {
            $services[] = $contentService->loadContent( $relation );
        }

        // DERNIERS PROJETS
        $field = $content->getField('derniers_projets');
        $relations = $field->attribute('value')->destinationContentIds;
        $projets = array();
        foreach ($relations as $relation) {
            $projets[] = $contentService->loadContent( $relation );
        }

        // DERNIERS POSTS
        $query = new Query();
        $query->limit = 4;
        $query->criterion = new Criterion\LogicalAnd(
            array(
                new Criterion\ParentLocationId( 62 )
            )
        );
        $query->sortClauses = array( new SortClause\DatePublished( Query::SORT_DESC ) );
        
        $result = $repository->getSearchService()->findContent($query);
        $articles = array();
        foreach($result->searchHits as $hit)
        {
            $articles[] = $hit->valueObject;
        }

        // ON RETOURNE LA VUE COMPLETEE DES VARIABLES CI-DESSUS
        $params = array( 
            'content' => $content,
            'slides' => $slides, 
            'services' => $services, 
            'projets' => $projets,
            'articles' => $articles
            );
        $response = $this->get( 'ez_content' )->viewLocation( $rootLocationId, 'full', false, $params );
    
        return $response;
    }
    
    
}
