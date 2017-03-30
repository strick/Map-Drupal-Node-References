<?php
/**
 * @file
 * Contains \Drupal\map_drupal_node_references\Controller\MapDrupalNodeReferencesController.
 */
namespace Drupal\map_drupal_node_references\Controller;

use Drupal\Core\DrupalKernel;
use Symfony\Component\HttpFoundation\Request;

class MapDrupalNodeReferencesController {
	
	public function index() {
		
		 return array(
			'#markup' => '<p><a href="/map-drupal-node-references/authors-to-quotes">' . t('Map Authors to Quotes') . '</a></p>',
		 );
	}
	// TODO: Make this accept start and end counts to proccess.
	public function authorsToQuotes($limit, $startNode) {

		// Switch to external database
		\Drupal\Core\Database\Database::setActiveConnection('gtt6');

		// Get a connection going
		$db = \Drupal\Core\Database\Database::getConnection();

		$query = $db->select('content_type_quote', 'ctq');
		$query->fields('ctq', array('nid', 'field_author_nid'));
        $query->orderBy('nid');
		$query->range($limit);

		// If start node is given, then only grab quotes from that point forward.
		if($startNode > 0)
			$query->condition('nid', $startNode, '>=');

		$quotes = $query->execute()->fetchAll();

		// Switch back
		\Drupal\Core\Database\Database::setActiveConnection();
		foreach($quotes as $quote){
			$node = \Drupal::entityTypeManager()->getStorage('node')->load($quote->nid);
			$node->field_author->target_id = $quote->field_author_nid;
			$node->save();
		}
		
	    return array(
	      	'#type' => 'markup',
	      	'#markup' => t('Ended on ' . $quote->nid),
	    );
  	}
}
