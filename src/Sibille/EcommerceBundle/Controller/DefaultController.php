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
        $competencesId = 88;
        
        // CONTENU PRINCIPAL DE LA HOME
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
        $query->limit = 3;
        $query->criterion = new Criterion\LogicalAnd(
            array(
               new Criterion\ContentTypeIdentifier( 'article' )
            )
        );
        $query->sortClauses = array( new SortClause\Field('article', 'publish_date', Query::SORT_DESC, 'fre-FR') );
        $result = $repository->getSearchService()->findContent($query);
        $articles = array();
        foreach($result->searchHits as $hit)
        {
            $articles[] = $hit->valueObject;
        }

        // DERNIERS POSTS
        $query->limit = 5;
        $query->criterion = new Criterion\LogicalAnd(
            array(
               new Criterion\ContentTypeIdentifier( 'competence' )
            )
        );
        $query->sortClauses = array( new SortClause\LocationPriority( Query::SORT_ASC ) );       
        $result = $repository->getSearchService()->findContent($query);
        $competences = array();
        foreach($result->searchHits as $hit)
        {
            $competences[] = $hit->valueObject;
        }

        // ON RETOURNE LA VUE COMPLETEE DES VARIABLES CI-DESSUS
        $params = array( 
            'content' => $content,
            'slides' => $slides, 
            'services' => $services, 
            'projets' => $projets,
            'articles' => $articles,
            'competences' => $competences
            );
        $response = $this->get( 'ez_content' )->viewLocation( $rootLocationId, 'full', false, $params );
    
        return $response;
    }
    
    
}
