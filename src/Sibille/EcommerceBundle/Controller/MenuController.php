<?php

namespace Sibille\EcommerceBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\SortClause;

/**
 * Description of MenuController
 *
 * @author Damien
 */
class MenuController extends Controller {
    
    public function indexAction()
    {        
        $rootLocation = $this->getRootLocation();

        $query = new Query();
        $query->criterion = new Criterion\LogicalAnd(
            array(
                new Criterion\Field( 'menu' , '=', true, null )
            )
        );
        $query->sortClauses = array( new SortClause\LocationPriority( Query::SORT_ASC ) );
        
        $result = $this->getRepository()->getSearchService()->findContent($query);
        $menuItems = array();
        foreach($result->searchHits as $hit)
        {
                $menuItems[] = $hit->valueObject;
        }
        
        return $this->render(
                'SibilleEcommerceBundle:parts:menu.html.twig',
                array('menuItems' => $menuItems)
        );        
    }

}